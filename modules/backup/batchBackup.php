<?PHP

require_once("website.php");

$dbFilename = LibEnv::getEnvHttpGET("dbFilename");
$tableStructure = LibEnv::getEnvHttpGET("tableStructure");
$tableData = LibEnv::getEnvHttpGET("tableData");
$dataFormat = LibEnv::getEnvHttpGET("dataFormat");
$fullInsert = LibEnv::getEnvHttpGET("fullInsert");
$noSecret = LibEnv::getEnvHttpGET("noSecret");

$mlText = $languageUtils->getMlText(__FILE__);

$backupSuccess = $backupUtils->backupDatabase($dbFilename, $tableStructure, $tableData, $dataFormat, $fullInsert, $noSecret);

if ($backupSuccess) {
  $backupFilePath = $backupUtils->renderBackupFilePath();
  $backupSuccess = $backupUtils->backupDataPath($backupFilePath);
}

if ($backupSuccess == false) {
  $webmasterEmail = $profileUtils->getProfileValue("webmaster.email");
  $webmasterName = $profileUtils->getProfileValue("webmaster.name");

  if (!$webmasterName) {
    $webmasterName = $webmasterEmail;
  }

  $strSubject = $mlText[8];
  $strBody = "$mlText[9] <a href='mailto:$webmasterEmail'>$webmasterName</a>";

  if ($webmasterEmail) {
    LibEmail::sendMail($webmasterEmail, $webmasterName, $strSubject, $strBody, $webmasterEmail, $webmasterName);
  }

  $staffEmail = $adminUtils->staffEmail;
  if ($staffEmail) {
    LibEmail::sendMail($staffEmail, $staffEmail, $strSubject, $strBody, $webmasterEmail, $webmasterName);
  }
}

?>
