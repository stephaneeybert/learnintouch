<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PEOPLE);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$peopleUtils->loadPreferences();

$preferenceUtils->init($peopleUtils->preferences, "$gPeopleUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
