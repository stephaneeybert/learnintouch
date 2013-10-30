<?PHP

$specific = '';
if ($argc == 3) {
  $specific = $argv[1];
  $filePath = $argv[2];
} else {
  die("Some arguments are missing for the file $PHP_SELF");
}

if (!@is_file($specific)) {
  die("The file $specific is missing for the file $PHP_SELF");
}
include($specific);

require_once("cli.php");

$toLanguageCode = 'se';

$languageUtils->translateFile($filePath, $toLanguageCode);

?>
