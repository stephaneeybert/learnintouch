<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_USER);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$userUtils->loadPreferences();

$preferenceUtils->init($userUtils->preferences, "$gUserUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
