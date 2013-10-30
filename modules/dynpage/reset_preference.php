<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$dynpageUtils->loadPreferences();

$preferenceUtils->init($dynpageUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
