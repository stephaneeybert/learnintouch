<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$userUtils->loadPreferences();

$preferenceUtils->init($userUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
