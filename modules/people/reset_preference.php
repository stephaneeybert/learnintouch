<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$peopleUtils->loadPreferences();

$preferenceUtils->init($peopleUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
