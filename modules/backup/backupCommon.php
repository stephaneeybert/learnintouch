<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_BACKUP);

$adminLogin = $adminUtils->checkAdminLogin();

if (!$adminUtils->isSuperAdmin($adminLogin)) {
  $str = $mlText[10];
  printMessage($str);
  return;
}

$mlText = $languageUtils->getMlText(__FILE__);


// Edit the database name so that my name does not appear in it
$dbName = DB_COMMON_DB_NAME;

// Create the file name
$dbFilename = $backupUtils->backupFilePath . $backupUtils->backupFilePrefix . $dbName . "_" . date("Y-m-d") . "_" . date("H-i") . ".sql";

// Backup the database
$backupUtils->selectCommonDataSource();
$backupSuccess = $backupUtils->backupCommonDatabase($dbFilename);

// Check for the backup success
if ($backupSuccess == false) {
  $webmasterEmail = $profileUtils->getProfileValue("webmaster.email");
  $webmasterName = $profileUtils->getProfileValue("webmaster.name");

  if (!$webmasterName) {
    $webmasterName = $webmasterEmail;
  }

  $strSubject = $mlText[7];
  $strBody = "$mlText[8]<br><br>$mlText[9] <a href='mailto:$webmasterEmail'>$webmasterName</a>";

  $staffEmail = $adminUtils->staffEmail;
  if ($staffEmail) {
    LibEmail::sendMail($staffEmail, $staffEmail, $strSubject, $strBody, $staffEmail, $staffEmail);
  }

  $str = $mlText[8];
  $str .= LibHtml::urlDisplayRedirect("$gBackupUrl/admin.php", $gRedirectDelay);
  printMessage($str);
  return;
}

$str = LibHtml::urlRedirect("$gBackupUrl/admin.php");
printMessage($str);

return;

?>
