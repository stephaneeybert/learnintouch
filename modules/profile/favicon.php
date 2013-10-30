<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PROFILE);

$mlText = $languageUtils->getMlText(__FILE__);

$fileSize = $profileUtils->fileSize;
$filePath = $profileUtils->filePath;
$fileUrl = $profileUtils->fileUrl;

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $deleteFile = LibEnv::getEnvHttpPOST("deleteFile");

  if ($deleteFile == 1) {
    $filename = '';
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
    $fileUploadUtils->loadLanguageTexts();
    if ($str = $fileUploadUtils->checkFileName($userfile_name)) {
      array_push($warnings, $str);
      } else if ($str = $fileUploadUtils->checkFaviconFileType($userfile_name)) {
      // Check if the file name has a correct file type
      array_push($warnings, $str);
      } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $fileSize)) {
      array_push($warnings, $str);
      } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $filePath)) {
      // Check if the file has been copied to the directory
      array_push($warnings, $str);
      }

    // Update the file
    $filename = $userfile_name;
    }

  if (count($warnings) == 0) {

    $profileUtils->setFaviconFilename($filename);

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;
    }
  }

$filename = $profileUtils->getFaviconFilename();

$panelUtils->setHeader($mlText[0]);

$help = $popupUtils->getHelpPopup($mlText[10], 300, 300);
$panelUtils->setHelp($help);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
    }
  }

$panelUtils->openMultipartForm($PHP_SELF);

if ($filename) {
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $filename);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='checkbox' name='deleteFile' value='1'>");
  }

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('max_file_size', $fileSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
