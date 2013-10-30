<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$contactUtils->loadPreferences();

$preferenceUtils->init($contactUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
