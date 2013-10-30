<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$guestbookUtils->loadPreferences();

$preferenceUtils->init($guestbookUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
