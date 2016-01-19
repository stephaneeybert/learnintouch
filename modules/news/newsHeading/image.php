<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$imagePath = $newsHeadingUtils->imageFilePath;
$imageUrl = $newsHeadingUtils->imageFileUrl;
$imageSize = $newsHeadingUtils->imageFileSize;

$fileUploadUtils->loadLanguageTexts();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $newsHeadingId = LibEnv::getEnvHttpPOST("newsHeadingId");
  $deleteImage = LibEnv::getEnvHttpPOST("deleteImage");
  $imageWidth = LibEnv::getEnvHttpPOST("imageWidth");

  if ($deleteImage == 1) {
    $image = '';
  } else {
    // Get the file characteristics
    // Note how the form parameter "userfile" creates several variables
    $uploaded_file = LibEnv::getEnvHttpFILE("userfile");
    $userfile = $uploaded_file['tmp_name'];
    $userfile_name = $uploaded_file['name'];
    $userfile_type = $uploaded_file['type'];
    $userfile_size = $uploaded_file['size'];

    // Clean up the filename
    $userfile_name = LibString::stripNonFilenameChar($userfile_name);

    // Check if a file has been specified...
    if ($str = $fileUploadUtils->checkFileName($userfile_name)) {
      array_push($warnings, $str);
    } else if ($str = $fileUploadUtils->checkMediaFileType($userfile_name)) {
      // Check if the image file name has a correct file type
      array_push($warnings, $str);
    } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $imageSize)) {
      array_push($warnings, $str);
    } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $imagePath)) {
      // Check if the file has been copied to the directory
      array_push($warnings, $str);
    }

    // Update the image
    $image = $userfile_name;
  }

  if (count($warnings) == 0) {

    if ($imageWidth) {
      if ($fileUploadUtils->isImageType($newsHeadingUtils->imageFilePath . $image) && !$fileUploadUtils->isGifImage($newsHeadingUtils->imageFilePath . $image)) {
        LibImage::resizeImageToWidth($newsHeadingUtils->imageFilePath . $image, $imageWidth);
      }
    }

    if ($newsHeading = $newsHeadingUtils->selectById($newsHeadingId)) {
      $newsHeading->setImage($image);
      $newsHeadingUtils->update($newsHeading);
    }

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  }

} else {

  $imageWidth = $newsHeadingUtils->getImageWidth();

}

$newsHeadingId = LibEnv::getEnvHttpGET("newsHeadingId");
if (!$newsHeadingId) {
  $newsHeadingId = LibEnv::getEnvHttpPOST("newsHeadingId");
}

if ($newsHeading = $newsHeadingUtils->selectById($newsHeadingId)) {
  $image = $newsHeading->getImage();
}

$panelUtils->setHeader($mlText[0]);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
  }
}

$panelUtils->openMultipartForm($PHP_SELF);

if ($image) {
  $filename = urlencode($imagePath . $image);
  $url = $imageUrl . "/" . $image;
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "br"), "<img src='$url' border='0' title='' href=''>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "br"), $image);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "br"), "<input type='checkbox' name='deleteImage' value='1'>");
}

$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[4], $mlText[5], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='imageWidth' value='$imageWidth' size='5' maxlength='5'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($imageSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());

$panelUtils->addHiddenField('max_file_size', $imageSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('newsHeadingId', $newsHeadingId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
