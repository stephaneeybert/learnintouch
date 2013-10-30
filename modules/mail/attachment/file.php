<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$fileUploadUtils->loadLanguageTexts();

$fileSize = $mailUtils->fileSize;
$filePath = $mailUtils->filePath;
$fileUrl = $mailUtils->fileUrl;

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $mailId = LibEnv::getEnvHttpPOST("mailId");

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
    } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $fileSize)) {
    array_push($warnings, $str);
    } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $filePath)) {
    // Check if the file has been copied to the directory
    array_push($warnings, $str);
    }

  // Update the file
  $filename = $userfile_name;

  if (count($warnings) == 0) {
    // Add the attached file to the mail
    $mailUtils->addAttachment($mailId, $filename);

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;
    }

  }

$mailId = LibEnv::getEnvHttpGET("mailId");
if (!$mailId) {
  $mailId = LibEnv::getEnvHttpPOST("mailId");
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
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($fileSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('max_file_size', $fileSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('mailId', $mailId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
