<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$templateUtils->loadPreferences();

$preferenceUtils->init($templateUtils->preferences, "$gTemplateUrl/design/model/admin.php");

require_once($gPreferencePath . "admin.php");

?>
