<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$mailUtils->loadPreferences();

$preferenceUtils->init($mailUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
