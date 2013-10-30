<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$fileUploadUtils->loadLanguageTexts();
$imagePath = $imageSetUtils->computerCustomImagePath;
$imageUrl = $imageSetUtils->computerCustomImageUrl;
$imageSize = $imageSetUtils->imageSize;

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $standardImage = LibEnv::getEnvHttpPOST("standardImage");
  $deleteImage = LibEnv::getEnvHttpPOST("deleteImage");

  if ($deleteImage == 1) {
    // Delete the custom image
    $imageSetUtils->deleteComputerCustomImage($standardImage);
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
    } else if ($str = $fileUploadUtils->checkImageFileType($userfile_name)) {
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

    if (count($warnings) == 0) {
      rename($imagePath . $image, $imagePath . $standardImage);
      copy($imagePath . $standardImage, $imageSetUtils->computerImagePath . $standardImage);
    }
  }

  if (count($warnings) == 0) {
    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  }

}

$standardImage = LibEnv::getEnvHttpGET("standardImage");
if (!$standardImage) {
  $standardImage = LibEnv::getEnvHttpPOST("standardImage");
}

$panelUtils->setHeader($mlText[0]);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
  }
}

$panelUtils->openMultipartForm($PHP_SELF);

if (is_file($imagePath . $standardImage)) {
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "br"), "<img src='$imageUrl/$standardImage' border='0' title='' href=''>");

  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "br"), $standardImage);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "br"), "<input type='checkbox' name='deleteImage' value='1'>");
}

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($imageSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('max_file_size', $imageSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('standardImage', $standardImage);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
