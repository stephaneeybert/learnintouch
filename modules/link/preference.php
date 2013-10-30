<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LINK);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$linkUtils->loadPreferences();

$preferenceUtils->init($linkUtils->preferences, "$gLinkUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
