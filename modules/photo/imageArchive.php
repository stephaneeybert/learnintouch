<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);

$fileUploadUtils->loadLanguageTexts();

$imagePath = $photoUtils->imagePath;
$imageUrl = $photoUtils->imageUrl;
$fileSize = $fileUploadUtils->maximumFileSize;

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $photoId = LibEnv::getEnvHttpPOST("photoId");
  $photoAlbumId = LibEnv::getEnvHttpPOST("photoAlbumId");
  $replaceOrAddToAlbum = LibEnv::getEnvHttpPOST("replaceOrAddToAlbum");
  $description = LibEnv::getEnvHttpPOST("description");
  $tags = LibEnv::getEnvHttpPOST("tags");

  $description = LibString::cleanString($description);
  $tags = LibString::cleanString($tags);

  // Get the file characteristics
  // Note how the form parameter "userfile" creates several variables
  $uploaded_file = LibEnv::getEnvHttpFILE("userfile");
  $userfile = $uploaded_file['tmp_name'];
  $userfile_name = $uploaded_file['name'];
  $userfile_type = $uploaded_file['type'];
  $userfile_size = $uploaded_file['size'];

  // Check if a file has been specified...
  if ($str = $fileUploadUtils->checkFileName($userfile_name)) {
    array_push($warnings, $str);
  } else if ($str = $fileUploadUtils->checkArchiveFileType($userfile_name)) {
    // Check if the file name has a correct file type
    array_push($warnings, $str);
  } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $fileSize)) {
    array_push($warnings, $str);
  } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $imagePath)) {
    // Check if the file has been copied to the directory
    array_push($warnings, $str);
  }

  $archiveFilename = LibString::stripNonFilenameChar($userfile_name);

  $folderName = LibFile::getFilePrefix($archiveFilename);
  $folderName = LibString::stripNonFilenameChar($folderName);

  // Check for a valid directory name
  if (!$folderName) {
    array_push($warnings, $mlText[3]);
  }
  if (!LibString::isAlphaNum($folderName)) {
    array_push($warnings, $mlText[3]);
  }

  // Check if the album does not already exist
  if (!$replaceOrAddToAlbum) {
    if ($photoAlbum = $photoAlbumUtils->selectByFolderName($folderName)) {
      array_push($warnings, $mlText[4] . ' ' . $folderName . ' ' . $mlText[5]);
    }
  }

  if (count($warnings) == 0) {

    if ($photoAlbum = $photoAlbumUtils->selectByFolderName($folderName)) {
      $photoAlbumId = $photoAlbum->getId();
      if ($replaceOrAddToAlbum == PHOTO_REPLACE_ALBUM) {
        // Delete the directory of the already existing album if any
        if (@file_exists($imagePath . $folderName)) {
          if ($folderName) {
            LibDir::deleteDirectory($imagePath . $folderName);
          }
        }

        // Delete the existing album if any
        $photoUtils->emptyAlbum($photoAlbumId);
        $photoAlbumUtils->deleteAlbum($photoAlbumId);
      }
    }

    // Unzip the file
    chdir($imagePath);
    @shell_exec("unzip -o -j -d $folderName $archiveFilename");

    if ($photoAlbum = $photoAlbumUtils->selectByFolderName($folderName)) {
      $photoAlbumId = $photoAlbum->getId();
    } else {
      // Create the photo album
      $photoAlbum = new PhotoAlbum();
      $photoAlbum->setName($folderName);
      $photoAlbum->setFolderName($folderName);
      $nextListOrder = $photoAlbumUtils->getNextListOrder();
      $photoAlbum->setListOrder($nextListOrder);
      $photoAlbumUtils->insert($photoAlbum);
      $photoAlbumId = $photoAlbumUtils->getLastInsertId();
    }

    // If the archive file did not create a directory
    // like a series of images instead of a directory containing images
    if (!@file_exists($imagePath . $folderName)) {
      // Create a directory
      @mkdir($imagePath . $folderName);
    }

    // Make the directory accessible
    chmod($imagePath . $folderName, 0755);

    // Move all images into the newly created directory
    // There should not be any images if the archive created a directory
    // But in case the archive contained only images and no directory
    // then the images must be moved
    $handle = opendir($imagePath);
    while ($imageFilename = readdir($handle)) {
      if ($imageFilename != "." && $imageFilename != ".." && $fileUploadUtils->isImageType($imageFilename)) {
        @rename($imagePath . $oneFile, $imagePath . $folderName . '/' . $oneFile);
      }
    }

    // Clean up the image filenames
    $handle = opendir($imagePath . $folderName);
    while ($imageFilename = readdir($handle)) {
      if ($imageFilename != "." && $imageFilename != "..") {
        $cleanPhotoFilename = LibString::stripNonFilenameChar($imageFilename);
        @rename($imagePath . $folderName . '/' . $imageFilename, $imagePath . $folderName . '/' . $cleanPhotoFilename);
      }
    }

    // Clean up the main photo directory, which should only contain albums directories
    $handle = opendir($imagePath);
    while ($oneFile = readdir($handle)) {
      if (!@is_dir($oneFile)) {
        @unlink($oneFile);
      }
    }

    if ($photoAlbumId) {
      // Create the photos
      $handle = opendir($imagePath . $folderName);
      while ($imageFilename = readdir($handle)) {
        if ($imageFilename != "." && $imageFilename != ".." && $fileUploadUtils->isImageType($imageFilename)) {
          $cleanPhotoFilename = LibString::stripNonFilenameChar($imageFilename);
          $photo = new Photo();
          $photo->setDescription($description);
          $photo->setTags($tags);
          $photo->setImage($cleanPhotoFilename);
          $photo->setPhotoAlbum($photoAlbumId);
          $listOrder = $photoUtils->getNextListOrder($photoAlbumId);
          $photo->setListOrder($listOrder);
          $photoUtils->insert($photo);
        }
      }

      LibSession::putSessionValue(PHOTO_SESSION_ALBUM, $photoAlbumId);
    }

    $str = LibHtml::urlRedirect("$gPhotoUrl/admin.php?photoAlbumId=$photoAlbumId");
    printContent($str);
    return;
  }

} else {

  $folderName = '';
  $description = '';
  $tags = '';

}

$replaceOrAddToAlbumList = array(
  '0' => '',
  PHOTO_REPLACE_ALBUM => $mlText[6],
  PHOTO_ADD_TO_ALBUM => $mlText[7],
);
$strReplaceOrAddToAlbum = LibHtml::getSelectList("replaceOrAddToAlbum", $replaceOrAddToAlbumList);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
  }
}

$panelUtils->setHeader($mlText[0], "$gPhotoUrl/admin.php");
$panelUtils->openMultipartForm($PHP_SELF);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($fileUploadUtils->maximumFileSize));
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), $strReplaceOrAddToAlbum);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[9], $mlText[10], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='description'  value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[11], $mlText[12], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='tags'  value='$tags' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('max_file_size', $fileSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
