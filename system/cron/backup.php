<?PHP

$specific = '';
if ($argc == 2) {
  $specific = $argv[1];
} else {
  die("Some arguments are missing for the file $PHP_SELF");
}

if (!is_file($specific)) {
  die("The file $specific is missing for the file $PHP_SELF");
}
include($specific);

require_once("cli.php");

$backupUtils->deleteBackup();

require_once($gBackupPath . "autoBackup.php");

?>
