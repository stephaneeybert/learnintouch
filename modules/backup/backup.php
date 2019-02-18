<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_BACKUP);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $tableStructure = LibEnv::getEnvHttpPOST("tableStructure");
  $tableData = LibEnv::getEnvHttpPOST("tableData");
  $dataFormat = LibEnv::getEnvHttpPOST("dataFormat");
  $fullInsert = LibEnv::getEnvHttpPOST("fullInsert");

  $tableStructure = LibString::cleanString($tableStructure);
  $tableData = LibString::cleanString($tableData);
  $dataFormat = LibString::cleanString($dataFormat);
  $fullInsert = LibString::cleanString($fullInsert);

  $dbName = DB_NAME;

  // Create the file name
  $dbFilename = $backupUtils->latestBackupFilePath . $backupUtils->backupFilePrefix . $dbName . "_" . date("Y-m-d") . "_" . date("H-i") . ".sql";
  $dbFilename = str_replace("__", "_", $dbFilename);

  if (!$tableStructure) {
    $tableStructure = '0';
  }

  if (!$tableData) {
    $tableData = '0';
  }

  if (!$dataFormat) {
    $dataFormat = '0';
  }

  if (!$fullInsert) {
    $fullInsert = '0';
  }

  if ($adminUtils->isStaff()) {
    $noSecret = '1';
  } else {
    $noSecret = '0';
  }

  $backupUtils->deleteBackup();

  $scriptUrl = $gBackupUrl . "/batchBackup.php?dbFilename=$dbFilename&tableStructure=$tableStructure&tableData=$tableData&dataFormat=$dataFormat&fullInsert=$fullInsert&noSecret=$noSecret";
  $scriptUrl = str_replace(parse_url($scriptUrl, PHP_URL_SCHEME), "http", $scriptUrl);
  $scriptUrl = str_replace(parse_url($scriptUrl, PHP_URL_HOST), "127.0.0.1", $scriptUrl);
  $scriptUrl = str_replace(parse_url($scriptUrl, PHP_URL_PORT), "80", $scriptUrl);
  $commonUtils->execlCLIwget($scriptUrl);

  $str = $mlText[4];
  $str .= LibHtml::urlRedirect("$gBackupUrl/admin.php");
  printMessage($str);
  return;

} else {

  $panelUtils->setHeader($mlText[0], "$gBackupUrl/admin.php");
  $help = $popupUtils->getHelpPopup($mlText[1], 200, 300);
  $panelUtils->setHelp($help);
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine();

  if ($adminUtils->isStaff()) {
    $label = $popupUtils->getTipPopup($mlText[2], $mlText[13], 300, 100);
    $panelUtils->addLine($panelUtils->addCell($label, "br"), "<input type='checkbox' name='tableStructure' CHECKED value='1'>");
    $panelUtils->addLine();
    $label = $popupUtils->getTipPopup($mlText[3], $mlText[12], 300, 100);
    $panelUtils->addLine($panelUtils->addCell($label, "br"), "<input type='checkbox' name='tableData' CHECKED value='1'>");
    $panelUtils->addLine();
    $strSelect = LibHtml::getSelectList("dataFormat", $backupUtils->backupDataFormats, 0);
    $label = $popupUtils->getTipPopup($mlText[5], $mlText[11], 300, 200);
    $panelUtils->addLine($panelUtils->addCell($label, "br"), $strSelect);
    $panelUtils->addLine();
    $label = $popupUtils->getTipPopup($mlText[6], $mlText[10], 300, 100);
    $panelUtils->addLine($panelUtils->addCell($label, "br"), "<input type='checkbox' name='fullInsert' value='1'>");
    $panelUtils->addLine();
  } else {
    $panelUtils->addHiddenField('tableStructure', 0);
    $panelUtils->addHiddenField('tableData', 1);
    $panelUtils->addHiddenField('dataFormat', 0);
    $panelUtils->addHiddenField('fullInsert', 0);
  }

  $panelUtils->addLine($panelUtils->addCell($mlText[15], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
