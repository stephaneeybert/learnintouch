<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LINK);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$formUtils->loadPreferences();

$preferenceUtils->init($formUtils->preferences, "$gFormUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
