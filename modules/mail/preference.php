<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$mailUtils->loadPreferences();

$mailUtils->loadLanguageTexts();

$preferenceUtils->init($mailUtils->preferences, "$gMailUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
