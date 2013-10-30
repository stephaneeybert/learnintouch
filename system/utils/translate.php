<?PHP

require_once("website.php");


$fromLanguageCode = 'en';
$toLanguageCode = 'se';
$toGoogleLanguageCode = 'sv';

$filePath = "/home/stephane/dev/php/sites/thalasoft/engine/modules/elearning/exercise/admin.php";

$languageUtils->translateFile($filePath, $toLanguageCode, $toGoogleLanguageCode);

?>
