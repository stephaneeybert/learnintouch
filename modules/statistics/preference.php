<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_STATISTICS);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$statisticsVisitUtils->loadPreferences();

$preferenceUtils->init($statisticsVisitUtils->preferences, "$gStatisticsUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
