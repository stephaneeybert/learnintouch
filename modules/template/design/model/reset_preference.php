<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$templateUtils->loadPreferences();

$preferenceUtils->init($templateUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
