<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$shopItemImageId = LibEnv::getEnvHttpGET("shopItemImageId");

$shopItemImageUtils->swapWithNext($shopItemImageId);

$str = LibHtml::urlRedirect("$gShopUrl/item/image/admin.php");
printContent($str);
return;

?>
