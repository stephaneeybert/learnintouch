<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$clientUtils->loadPreferences();

$preferenceUtils->init($clientUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
