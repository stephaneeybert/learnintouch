<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$shopItemUtils->loadPreferences();

$preferenceUtils->init($shopItemUtils->preferences, "$gShopUrl/order/admin.php");

require_once($gPreferencePath . "admin.php");

?>
