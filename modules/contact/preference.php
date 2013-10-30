<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$contactUtils->loadPreferences();

$preferenceUtils->init($contactUtils->preferences, "$gContactUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
