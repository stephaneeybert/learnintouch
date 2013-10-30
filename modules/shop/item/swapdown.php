<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$shopItemId = LibEnv::getEnvHttpGET("shopItemId");

$shopItemUtils->swapWithNext($shopItemId);

$str = LibHtml::urlRedirect("$gShopUrl/item/admin.php");
printContent($str);
return;

?>
