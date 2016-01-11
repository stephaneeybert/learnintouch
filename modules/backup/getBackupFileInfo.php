<?PHP

require_once("website.php");

LibHtml::preventCaching();

$backupFileUrl = $backupUtils->renderBackupFileUrl();
$backupFilePath = $backupUtils->renderBackupFilePath();

$fileName = basename($backupFilePath);

if (is_readable($backupFilePath)) {
  $fileTimestamp = filemtime($backupFilePath);
  $fileSize = filesize($backupFilePath);
  $fileTime = date("d/m/Y H:i:s", $fileTimestamp);
  $fileUrl = $backupFileUrl;
} else {
  $fileSize = '0';
  $fileTime = '';
  $fileUrl = '';
}

$responseText = <<<HEREDOC
{
"fileName" : "$fileName",
"fileSize" : "$fileSize",
"fileTime" : "$fileTime",
"fileUrl" : "$fileUrl"
}
HEREDOC;

print($responseText);

?>
