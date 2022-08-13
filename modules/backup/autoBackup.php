<?PHP

$mlText = $languageUtils->getMlText(__FILE__);

// Edit the database name so that my name does not appear in it
$dbName = DB_NAME;
$dbName = str_replace("stephane", '', $dbName);
$dbName = str_replace("eybert", '', $dbName);

$backupUtils->deleteBackup();

// Create the file name
$dbFilename = $backupUtils->latestBackupFilePath . $backupUtils->backupFilePrefix . $dbName . "_" . date("Y-m-d") . "_" . date("H-i") . ".sql";
$dbFilename = str_replace("__", "_", $dbFilename);

// Backup the database
$backupSuccess = $backupUtils->backupDatabase($dbFilename, false);

// Check for the backup success
if ($backupSuccess) {
  $backupFilePath = $backupUtils->renderBackupTarFilePath();
  $backupSuccess = $backupUtils->backupDataPath($backupFilePath);
}

$webmasterEmail = $profileUtils->getProfileValue("webmaster.email");
$webmasterName = $profileUtils->getProfileValue("webmaster.name");
$websiteName = $profileUtils->getProfileValue("website.name");

if (!$webmasterName) {
  $webmasterName = $webmasterEmail;
}

// Check for the backup success
if ($backupSuccess == false) {
  $strSubject = $mlText[8];

  $strBody = "$mlText[9] <a href='mailto:$webmasterEmail'>$webmasterName</a>";

  if ($webmasterEmail) {
    LibEmail::sendMail($webmasterEmail, $webmasterName, $strSubject, $strBody, $webmasterEmail, $webmasterName);
  }
} else {
  // Generate a unique token and keep it for later use
  $tokenName = BACKUP_TOKEN_NAME;
  $tokenDuration = $adminUtils->getLoginTokenDuration();
  $tokenValue = $uniqueTokenUtils->create($tokenName, $tokenDuration);

  $strLink = "<a href='$gBackupUrl/admin.php?tokenName=$tokenName&tokenValue=$tokenValue' $gJSNoStatus>" .  $mlText[4] . "</a>";
  $strSubject = $mlText[1] . ' ' . $websiteName;
  $strBody = $mlText[2] . "<br /><br />" . $mlText[3] . ' ' . $strLink;

  $preferenceUtils->init($backupUtils->preferences);

  if ($backupUtils->mailOnBackup()) {
    LibEmail::sendMail($webmasterEmail, $webmasterName, $strSubject, $strBody, $webmasterEmail, $webmasterName);
  }
}

?>
