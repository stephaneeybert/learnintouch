<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$documentUtils->loadPreferences();

$preferenceUtils->init($documentUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
