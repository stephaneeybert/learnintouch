<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$shopCategoryId = LibEnv::getEnvHttpGET("shopCategoryId");

$shopCategoryUtils->swapWithPrevious($shopCategoryId);

$str = LibHtml::urlRedirect("$gShopUrl/category/admin.php");
printContent($str);
return;

?>
