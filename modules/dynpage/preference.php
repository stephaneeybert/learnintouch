<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$dynpageUtils->loadPreferences();

$preferenceUtils->init($dynpageUtils->preferences, "$gDynpageUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
