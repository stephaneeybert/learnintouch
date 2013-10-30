<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$smsUtils->loadPreferences();

$preferenceUtils->init($smsUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
