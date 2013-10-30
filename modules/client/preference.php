<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CLIENT);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$clientUtils->loadPreferences();

$preferenceUtils->init($clientUtils->preferences, "$gClientUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
