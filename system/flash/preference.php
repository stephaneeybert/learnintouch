<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FLASH);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$flashUtils->loadPreferences();

$preferenceUtils->init($flashUtils->preferences, "$gFlashUrl/intro.php");

require_once($gPreferencePath . "admin.php");

?>
