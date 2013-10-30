<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$profileUtils->loadPreferences();

$preferenceUtils->init($profileUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
