<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$linkUtils->loadPreferences();

$preferenceUtils->init($linkUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
