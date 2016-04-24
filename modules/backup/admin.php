<?PHP

require_once("website.php");

$isStaffLogin = false;

// The administrator may access this page without being logged in if a unique token is used
// This allows a administrator to access this page by clicking on a link in an email
$tokenName = LibEnv::getEnvHttpGET("tokenName");
$tokenValue = LibEnv::getEnvHttpGET("tokenValue");
if (!$uniqueTokenUtils->isValid($tokenName, $tokenValue)) {
  // If no token is used, then
  // check that the administrator is allowed to use the module
  $adminModuleUtils->checkAdminModule(MODULE_BACKUP);

  $loginSession = $adminUtils->checkAdminLogin();

  // Offer the backup of the common database to a staff admin only
  if ($adminUtils->isStaffLogin($loginSession)) {
    $isStaffLogin = true;
  }
}

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[12], 300, 500);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gBackupUrl/backup.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";

// Offer the backup of the common database to a staff admin only
if ($isStaffLogin) {
  $strCommand .= " <a href='$gBackupUrl/backupCommon.php' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[9]'></a>";
}

// Offer the backup of the language files
if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_LANGUAGE_TRANSLATE)) {
  $strCommand .= " <a href='$gBackupUrl/backupLanguageFiles.php' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[11]'></a>";
}

$strCommand .= " <a href='$gBackupUrl/export.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageExport' title='$mlText[7]'></a>"
. " <a href='$gBackupUrl/preference.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[14]'></a>";
$panelUtils->addLine('', '', '', $panelUtils->addCell($strCommand, "nbr"));

// Display the main backup file
// The download.php script cannot be used for large files
// So the files are downloaded like simple links
$backupFileUrl = $backupUtils->renderBackupFileUrl();
$backupFilePath = $backupUtils->renderBackupFilePath();
if (is_readable($backupFilePath)) {
  // Get the file creation time
  $fileTimestamp = filemtime($backupFilePath);
  $fileTime = date("d/m/Y H:i:s", $fileTimestamp);
  $fileSize = filesize($backupFilePath);
  $strFile = "<span id='backupFile'><a href='$backupFileUrl' $gJSNoStatus title='$mlText[8]'>" . basename($backupFilePath) . "</a> $mlText[15] <span id='backupFileSize' style='color:red;'>$fileSize</span></span>";
} else {
  $strFile = "<span id='backupFile'></span>";
  $fileSize = 0;
}
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $panelUtils->addCell($strFile, "n"), '', '');

// Display the backup file size in a different color
// when the backup is ready to be downloaded
$strRenderFileSize = <<<HEREDOC
<script type='text/javascript'>
var previousFileSize = parseInt($fileSize);
function renderBackupFile(responseText) {
  var response = eval('(' + responseText + ')');
  var fileName = response.fileName;
  var fileSize = parseInt(response.fileSize);
  var fileTime = response.fileTime;
  var fileUrl = response.fileUrl;
  var color = '';
  var title = '';
  if (fileSize > 0) {
    if (fileSize > previousFileSize) {
      color = "red";
      title = '$mlText[13]';
      previousFileSize = fileSize;
    } else {
      color = "green";
      title = '$mlText[8]';
      // Stop the loop
      window.clearInterval(repeat);
    }
    document.getElementById("backupFile").innerHTML = "<a href='" + fileUrl + "' title='" + title + "'><span style='color:" + color + ";'>" + fileName + "</span></a> " + " $mlText[15] <span id='backupFileSize'><span style='color:" + color + ";'>" + fileSize + "</span>";
  } else {
    // If the file size does not change then there is not backup going on
    // and there is no need to refresh the file size
    if (previousFileSize == 0) {
      // Stop the loop
      window.clearInterval(repeat);
    }
  }
}
// Get the size of the backup file being prepared
var repeat = window.setInterval("ajaxAsynchronousRequest('$gBackupUrl/getBackupFileInfo.php', renderBackupFile)", 5000);
</script>
HEREDOC;

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell("$mlText[4]", "nb"), $panelUtils->addCell("$mlText[2]", "nb"), $panelUtils->addCell($mlText[5], "nbc"), '');
$panelUtils->addLine();

$backupDir = opendir($backupUtils->previousBackupFilePath);

$fileTimestamps = Array();
$fileSizes = Array();
while (($fileName = readdir($backupDir)) !== false) {
  // Do not list the current and parent directories
  if (!is_dir($fileName)) {
    // Get the file size
    $fileSize = filesize("$backupUtils->previousBackupFilePath$fileName");

    // Get the file creation time
    $fileTimestamp = filemtime("$backupUtils->previousBackupFilePath$fileName");

    $fileTimestamps[$fileName] = $fileTimestamp;
    $fileSizes[$fileName] = $fileSize;
  }
}

// Sort on the timestamps in reverse order
arsort($fileTimestamps);

$panelUtils->openList();
foreach ($fileTimestamps as $fileName => $fileTimestamp) {

  $fileSize = $fileSizes[$fileName];

  $fileTime = date("d/m/Y H:i:s", $fileTimestamp);

  $fileName = urlencode($fileName);

  $url = $backupUtils->renderSqlFileUrl($fileName);
  $strFile = "<a href='$url' $gJSNoStatus title='$mlText[8]'>$fileName</a>";

  $strCommand = "<a href='$gBackupUrl/delete.php?fileName=$fileName' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($strFile, $fileSize, $panelUtils->addCell($fileTime, "c"), $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

closedir($backupDir);

$str = $panelUtils->render();

printAdminPage($str, $strRenderFileSize);

?>
