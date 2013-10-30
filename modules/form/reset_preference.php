<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$formUtils->loadPreferences();

$preferenceUtils->init($formUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
