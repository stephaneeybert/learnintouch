<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CLOCK);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$clockUtils->loadPreferences();

$preferenceUtils->init($clockUtils->preferences, "$gClockUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
