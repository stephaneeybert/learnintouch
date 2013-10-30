<?PHP

require_once("website.php");

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$preferenceUtils->init($lexiconEntryUtils->preferences, "$gLexiconUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
