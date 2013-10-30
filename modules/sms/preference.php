<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$smsUtils->loadPreferences();

$preferenceUtils->init($smsUtils->preferences, "$gSmsUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
