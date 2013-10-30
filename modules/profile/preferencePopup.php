<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PROFILE);

// Edit the module preferences
$preferenceUtils->init($profileUtils->preferences);
require_once($gPreferencePath . "admin.php");

?>
