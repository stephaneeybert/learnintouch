<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$fileUploadUtils->loadLanguageTexts();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $deleteImage = LibEnv::getEnvHttpPOST("deleteImage");

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
    }

  if ($fileUploadUtils->isImageType($userfile_name)) {
    if ($str = $fileUploadUtils->checkFileSize($userfile_size, $imageSize)) {
      array_push($warnings, $str);
      } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $imagePath)) {
      // Check if the file has been copied to the directory
      array_push($warnings, $str);
      }
    } else if ($fileUploadUtils->isFlashType($userfile_name)) {
    if ($str = $fileUploadUtils->checkFileSize($userfile_size, $fileSize)) {
      array_push($warnings, $str);
      } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $filePath)) {
      // Check if the file has been copied to the directory
      array_push($warnings, $str);
      }
    }

  if (count($warnings) == 0) {
    $panelUtils->setHeader($mlText[0]);
    $panelUtils->openForm($PHP_SELF);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[3], "nb"));
    $panelUtils->addLine();

    // Update the parent window input fields
    if ($fileUploadUtils->isImageType($userfile_name)) {
      $imageSource = $imageUrl . "/" . $userfile_name;
      $imageSource = str_replace($gHomeUrl, '', $imageSource);

      $str = <<<HEREDOC
<script language="javascript" type="text/javascript">

window.opener.document.getElementById("inpImgURL").value = "$imageSource";
// The onchange tag attribute used in the html source code of the image popup window is not triggered
// programatically due to the design of the javascript language
// The onchange is therefore explicitly triggered with the following function call
window.opener.document.getElementById("inpImgURL").onchange();

</script>
HEREDOC;
      } else if ($fileUploadUtils->isFlashType($userfile_name)) {
      $fileSource = $fileUrl . "/" . $userfile_name;
      $fileSource = str_replace($gHomeUrl, '', $fileSource);

      $str = <<<HEREDOC
<script language="javascript" type="text/javascript">

window.opener.document.getElementById("inpSwfURL").value = "$fileSource";
// The onchange tag attribute used in the html source code of the image popup window is not triggered
// programatically due to the design of the javascript language
// The onchange is therefore explicitly triggered with the following function call
window.opener.document.getElementById("inpSwfURL").onchange();

</script>
HEREDOC;
      }

    $str .= LibJavascript::autoCloseWindow();
    printContent($str);
    return;
    }

  }

$panelUtils->setHeader($mlText[0]);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
    }
  }

$help = $popupUtils->getHelpPopup($mlText[1], 300, 500);
$panelUtils->setHelp($help);
$panelUtils->openMultipartForm($PHP_SELF);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($imageSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('max_file_size', $imageSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
