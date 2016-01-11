<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);

$imagePath = $photoUtils->imagePath;
$imageUrl = $photoUtils->imageUrl;
$imageSize = $photoUtils->imageSize;

$fileUploadUtils->loadLanguageTexts();

$warnings = array();

// Check if there are photo albums
if ($photoAlbumUtils->countAll() == 0) {
  array_push($warnings, $mlText[8]);
}

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $photoId = LibEnv::getEnvHttpPOST("photoId");
  $photoAlbumId = LibEnv::getEnvHttpPOST("photoAlbumId");

  // Get the file characteristics
  // Note how the form parameter "userfile" creates several variables
  $uploaded_file = LibEnv::getEnvHttpFILE("userfile");
  $userfile = $uploaded_file['tmp_name'];
  $userfile_name = $uploaded_file['name'];
  $userfile_type = $uploaded_file['type'];
  $userfile_size = $uploaded_file['size'];

  // Clean up the filename
  $userfile_name = LibString::stripNonFilenameChar($userfile_name);

  $albumName = '';
  $folderName = '';
  if(!$photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
    array_push($warnings, $mlText[4]);
  } else {
    $albumName = $photoAlbum->getName();
    $folderName = $photoAlbum->getFolderName();
  }

  // Create the album directory if it does not yet exist
  if (!file_exists($imagePath . $folderName)) {
    $folderName = LibString::stripNonFilenameChar($albumName);
    mkdir($imagePath . $folderName);
  }

  // Check if a file has been specified...
  if ($str = $fileUploadUtils->checkFileName($userfile_name)) {
    array_push($warnings, $str);
  } else if ($str = $fileUploadUtils->checkImageFileType($userfile_name)) {
    // Check if the image file name has a correct file type
    array_push($warnings, $str);
  } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $imageSize)) {
    array_push($warnings, $str);
  } else if ($folderName && $str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $imagePath . $folderName)) {
    // Check if the file has been copied to the directory
    array_push($warnings, $str);
  }

  if ($fileUploadUtils->isImageType($imagePath . $folderName . '/' . $userfile_name) && !$fileUploadUtils->isGifImage($imagePath . $folderName . '/' . $userfile_name)) {
    $destWidth = $photoUtils->getImageWidth();
    LibImage::resizeImageToWidth($imagePath . $folderName . '/' . $userfile_name, $destWidth);
  }

  // Update the image
  $image = $userfile_name;

  if (count($warnings) == 0) {

    if ($photo = $photoUtils->selectById($photoId)) {
      $photo->setImage($image);
      $photo->setPhotoAlbum($photoAlbumId);
      $photoUtils->update($photo);
    } else if ($photoAlbumId) {
      $photo = new Photo();
      $photo->setImage($image);
      $reference = LibFile::getFilePrefix($image);
      $photo->setReference($reference);
      $photo->setPhotoAlbum($photoAlbumId);

      // Get the next list order
      $listOrder = $photoUtils->getNextListOrder($photoAlbumId);
      $photo->setListOrder($listOrder);

      $photoUtils->insert($photo);
    }

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  }

} else {

  $photoId = LibEnv::getEnvHttpGET("photoId");
  if (!$photoId) {
    $photoId = LibEnv::getEnvHttpPOST("photoId");
  }

  // Get the chosen album from the session
  $photoAlbumId = LibSession::getSessionValue(PHOTO_SESSION_ALBUM);

}

// If an id is passed get the current properties
$image = '';
if ($photoId) {
  if ($photo = $photoUtils->selectById($photoId)) {
    $photoAlbumId = $photo->getPhotoAlbum();
    $image = $photo->getImage();
  }
}

$photoAlbums = $photoAlbumUtils->selectAll();
$photoAlbumList = Array('' => '');
foreach ($photoAlbums as $photoAlbum) {
  $wPhotoAlbumId = $photoAlbum->getId();
  $wName = $photoAlbum->getName();
  $photoAlbumList[$wPhotoAlbumId] = $wName;
}
$strSelectAlbum = LibHtml::getSelectList("photoAlbumId", $photoAlbumList, $photoAlbumId);

$albumName = '';
$folderName = '';
if($photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
  $albumName = $photoAlbum->getName();
  $folderName = $photoAlbum->getFolderName();
}

$panelUtils->setHeader($mlText[0]);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
  }
}

$panelUtils->openMultipartForm($PHP_SELF);

if ($image && file_exists($imagePath . $folderName . '/' . $image)) {
  if (!LibImage::isGif($image)) {
    $filename = urlencode($imagePath . $folderName . '/' . $image);
    $url = $gUtilsUrl . "/printImage.php?filename=" . $filename . "&width=120&height=";
    $panelUtils->addLine($panelUtils->addCell($mlText[6], "br"), "<img src='$url' border='0' title='' href=''>");
  }
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "br"), $image);
}

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($imageSize));
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $strSelectAlbum);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());

$panelUtils->addHiddenField('max_file_size', $imageSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('photoId', $photoId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
