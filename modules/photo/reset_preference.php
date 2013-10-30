<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$photoUtils->loadPreferences();

$preferenceUtils->init($photoUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
