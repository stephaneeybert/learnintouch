<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DOCUMENT);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$documentUtils->loadPreferences();

$preferenceUtils->init($documentUtils->preferences, "$gDocumentUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
