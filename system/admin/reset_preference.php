<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$adminUtils->loadPreferences();

$preferenceUtils->init($adminUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
