<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$flashUtils->loadPreferences();

$preferenceUtils->init($flashUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
