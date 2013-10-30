<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$backupUtils->loadPreferences();

$preferenceUtils->init($backupUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
