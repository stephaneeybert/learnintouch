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

$websiteUtils->checkWebsiteSize();

$templatePropertyUtils->cleanup();

$templatePropertySetUtils->cleanup();

$statisticsVisitUtils->deleteOldVisits();

$contactUtils->deleteOldMessages();

$newsPublicationUtils->archiveOldNewspapers();

$newsPublicationUtils->deleteOldNewspapers();

$mailUtils->deleteOldMails();

$elearningResultUtils->deleteOldResults();

error_log("The data tables were cleaned up successfully.");

?>
