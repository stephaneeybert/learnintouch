<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$fileUploadUtils->loadLanguageTexts();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $deleteImage = LibEnv::getEnvHttpPOST("deleteImage");

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
    } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $elearningExerciseUtils->imageFileSize)) {
      array_push($warnings, $str);
    } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $elearningExerciseUtils->imageFilePath)) {
      // Check if the file has been copied to the directory
      array_push($warnings, $str);
    }

    if ($fileUploadUtils->isImageType($elearningExerciseUtils->imageFilePath . $userfile_name) && !$fileUploadUtils->isGifImage($elearningExerciseUtils->imageFilePath . $userfile_name)) {
      $destWidth = $elearningExerciseUtils->getImageWidth();
      LibImage::resizeImageToWidth($elearningExerciseUtils->imageFilePath . $userfile_name, $destWidth);
    }

    // Update the image
    $image = $userfile_name;
  }

  if (count($warnings) == 0) {

    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
      $elearningExercise->setImage($image);
      $elearningExerciseUtils->update($elearningExercise);
    }

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  }

}

$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
if (!$elearningExerciseId) {
  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
}

if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $image = $elearningExercise->getImage();
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
  if (LibImage::isImage($image) && !LibImage::isGif($image)) {
    $filename = urlencode($elearningExerciseUtils->imageFilePath . $image);
    $url = $gUtilsUrl . "/printImage.php?filename=" . $filename . "&width=120&height=";
    $panelUtils->addLine($panelUtils->addCell($mlText[6], "br"), "<img src='$url' border='0' title='' href=''>");
  } else {
    $fileUrl = "$elearningExerciseUtils->imageFileUrl/$image";
    $strImage = "<a href='$fileUrl' $gJSNoStatus title=''>$image</a>";
    $panelUtils->addLine($panelUtils->addCell($mlText[6], "br"), $strImage);
  }
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "br"), $image);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "br"), "<input type='checkbox' name='deleteImage' value='1'>");
}

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($elearningExerciseUtils->imageFileSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('max_file_size', $elearningExerciseUtils->imageFileSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
