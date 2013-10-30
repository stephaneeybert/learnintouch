<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PROFILE);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$profileUtils->loadPreferences();

$preferenceUtils->init($profileUtils->preferences, "$gProfileUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
