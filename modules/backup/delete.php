<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_BACKUP);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $fileName = LibEnv::getEnvHttpPOST("fileName");

  // The file name is required
  if (!$fileName) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    if (file_exists($backupUtils->backupFilePath . $fileName)) {
      unlink($backupUtils->backupFilePath . $fileName);
    }

    $str = LibHtml::urlRedirect("$gBackupUrl/admin.php");
    printMessage($str);
    return;

  }

} else {

  $fileName = LibEnv::getEnvHttpGET("fileName");
  $fileName = urldecode($fileName);

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gBackupUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $fileName);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('fileName', $fileName);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
