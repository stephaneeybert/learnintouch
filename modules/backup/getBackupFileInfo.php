<?PHP

require_once("website.php");

LibHtml::preventCaching();

$backupFileUrl = $backupUtils->renderBackupTarFileUrl();
$backupTarFilePath = $backupUtils->renderBackupTarFilePath();

$fileName = basename($backupTarFilePath);

if (is_readable($backupTarFilePath)) {
  $fileTimestamp = filemtime($backupTarFilePath);
  $fileSize = filesize($backupTarFilePath);
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
