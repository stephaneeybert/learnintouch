<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$newsStoryUtils->loadPreferences();

$preferenceUtils->init($newsStoryUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
