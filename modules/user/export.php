<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_USER);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  // Create the file name
  $filename = $backupUtils->exportFilePath . $userUtils->tableName . ".csv";

  // Delete the existing file if any
  LibFile::deleteFile($filename);

  // Export the users
  $success = $userUtils->exportCSV($filename);

  // Display a popup window to save the backup file onthe local computer
  if ($success) {
    LibFile::downloadFile($filename);
    }

  } else {

  $panelUtils->setHeader($mlText[0], "$gUserUrl/admin.php");
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "rb"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
