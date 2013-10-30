<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_BACKUP);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$backupUtils->loadPreferences();

$preferenceUtils->init($backupUtils->preferences, "$gBackupUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
