<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$mlText = $languageUtils->getMlText(__FILE__);

$fileUploadUtils->loadLanguageTexts();
$imagePath = $dynpageUtils->imagePath;
$imageUrl = $dynpageUtils->imageUrl;
$imageSize = $dynpageUtils->imageSize;

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $deleteImage = LibEnv::getEnvHttpPOST("deleteImage");
  $isHtmlEditor = LibEnv::getEnvHttpPOST("isHtmlEditor");
  $imageWidth = LibEnv::getEnvHttpPOST("imageWidth");

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

  if (count($warnings) == 0) {

    if ($imageWidth) {
      if ($fileUploadUtils->isImageType($imagePath . $userfile_name) && !$fileUploadUtils->isGifImage($imagePath . $userfile_name)) {
        LibImage::resizeImageToWidth($imagePath . $userfile_name, $imageWidth);
      }
    }

    $panelUtils->setHeader($mlText[0]);
    $panelUtils->openForm($PHP_SELF);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[3], "nb"));
    $panelUtils->addLine();

    $imageSource = $imageUrl . "/" . $userfile_name;
    $imageSource = str_replace($gHomeUrl, '', $imageSource);

    // Display the image url if the script is not called from an html editor
    if (!$isHtmlEditor) {
      $panelUtils->addLine($imageSource);
      $panelUtils->addLine();
      $panelUtils->addLine($panelUtils->addCell($mlText[4], "nb"));
      $panelUtils->addLine();
      $panelUtils->addLine('', $panelUtils->getOk());
      $panelUtils->addHiddenField('formSubmitted', 2);
      $panelUtils->closeForm();
      $str = $panelUtils->render();
      printAdminPage($str);
      return;
    } else {
      $str = <<<HEREDOC
<script language="javascript" type="text/javascript">
var parentWindow = opener.tinyMCE.getWindowArg("window");
parentWindow.document.getElementById('src').value = '$imageSource';
parentWindow.document.getElementById('preview').src = '$imageSource';
</script>
HEREDOC;

      $str .= LibJavascript::autoCloseWindow();
      printContent($str);
      return;
    }
  }

} else {

    $imageWidth = $dynpageUtils->getImageWidth();

}

if ($formSubmitted == 2) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

// Check if the script is called from an html editor
$isHtmlEditor = LibEnv::getEnvHttpGET("isHtmlEditor");

$panelUtils->setHeader($mlText[0]);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
  }
}

$help = $popupUtils->getHelpPopup($mlText[1], 300, 500);
$panelUtils->setHelp($help);
$strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[5]'>", "$gDynpageUrl/deleteImages.php", 600, 600);
$panelUtils->addLine($panelUtils->addCell($strCommand, "nbr"));
$panelUtils->openMultipartForm($PHP_SELF);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[6], $mlText[7], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='imageWidth' value='$imageWidth' size='5' maxlength='5'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($imageSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('max_file_size', $imageSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('isHtmlEditor', $isHtmlEditor);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
