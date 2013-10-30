<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$tableList = $backupUtils->getTableNames();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $tableIndex = LibEnv::getEnvHttpPOST("tableIndex");

  $tableIndex = LibString::cleanString($tableIndex);

  $tableName = $tableList[$tableIndex];

  $filename = $backupUtils->exportFilePath . $tableName . ".csv";

  LibFile::deleteFile($filename);

  $success = $backupUtils->exportTable($filename, $tableName);

  if ($success == false) {
    $webmasterEmail = $profileUtils->getProfileValue("webmaster.email");
    $webmasterName = $profileUtils->getProfileValue("webmaster.name");

    if (!$webmasterName) {
      $webmasterName = $webmasterEmail;
    }

    if ($webmasterEmail) {
      $strSubject = $mlText[8];
      $strBody = "$mlText[9] <a href='mailto:$webmasterEmail'>$webmasterName</a>";
      LibEmail::sendMail($webmasterEmail, $webmasterName, $strSubject, $strBody, $webmasterEmail, $webmasterName);
    }

    $str = $mlText[8];
    $str .= LibHtml::urlDisplayRedirect("$gBackupUrl/admin.php", $gRedirectDelay);
    printMessage($str);
    return;
  }

  if ($filename) {
    LibFile::downloadFile($filename);
  }

  $str = LibHtml::urlRedirect("$gBackupUrl/export.php");
  printMessage($str);
  return;

} else {

  $strSelect = LibHtml::getSelectList("tableIndex", $tableList);

  $panelUtils->setHeader($mlText[0], "$gBackupUrl/admin.php");
  $help = $popupUtils->getHelpPopup($mlText[12], 300, 300);
  $panelUtils->setHelp($help);
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[1], 'br'), '');
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $strSelect);
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
