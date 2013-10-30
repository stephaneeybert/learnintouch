<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$shopItemUtils->loadPreferences();

$preferenceUtils->init($shopItemUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
