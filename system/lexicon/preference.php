<?PHP

require_once("website.php");

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$lexiconEntryUtils->loadPreferences();

$preferenceUtils->init($lexiconEntryUtils->preferences, "$gLexiconUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
