<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$statisticsVisitUtils->loadPreferences();

$preferenceUtils->init($statisticsVisitUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
