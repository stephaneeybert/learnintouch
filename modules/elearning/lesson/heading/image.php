<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$fileUploadUtils->loadLanguageTexts();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $elearningLessonHeadingId = LibEnv::getEnvHttpPOST("elearningLessonHeadingId");
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
    } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $elearningLessonHeadingUtils->imageFileSize)) {
      array_push($warnings, $str);
    } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $elearningLessonHeadingUtils->imageFilePath)) {
      // Check if the file has been copied to the directory
      array_push($warnings, $str);
    }

    // Update the image
    $image = $userfile_name;
  }

  if (count($warnings) == 0) {

    if ($imageWidth) {
      if ($fileUploadUtils->isImageType($elearningLessonHeadingUtils->imageFilePath . $image) && !$fileUploadUtils->isGifImage($elearningLessonHeadingUtils->imageFilePath . $image)) {
        LibImage::resizeImageToWidth($elearningLessonHeadingUtils->imageFilePath . $image, $imageWidth);
      }
    }

    if ($elearningLessonHeading = $elearningLessonHeadingUtils->selectById($elearningLessonHeadingId)) {
      $elearningLessonHeading->setImage($image);
      $elearningLessonHeadingUtils->update($elearningLessonHeading);
    }

    $str = LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  }

} else {

  $imageWidth = $elearningLessonUtils->getImageWidth();

}

$elearningLessonHeadingId = LibEnv::getEnvHttpGET("elearningLessonHeadingId");
if (!$elearningLessonHeadingId) {
  $elearningLessonHeadingId = LibEnv::getEnvHttpPOST("elearningLessonHeadingId");
}

if ($elearningLessonHeading = $elearningLessonHeadingUtils->selectById($elearningLessonHeadingId)) {
  $image = $elearningLessonHeading->getImage();
}

$panelUtils->setHeader($mlText[0]);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
  }
}

$help = $popupUtils->getHelpPopup($mlText[1], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->openMultipartForm($PHP_SELF);

if ($image) {
  $fileUrl = "$elearningLessonHeadingUtils->imageFileUrl/$image";
  $strImage = "<img src='$fileUrl' $gJSNoStatus title=''></img>";
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "br"), $strImage);
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
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($elearningLessonHeadingUtils->imageFileSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('max_file_size', $elearningLessonHeadingUtils->imageFileSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningLessonHeadingId', $elearningLessonHeadingId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
