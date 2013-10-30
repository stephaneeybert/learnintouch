<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$clockUtils->loadPreferences();

$preferenceUtils->init($clockUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
