<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LINK);

$mlText = $languageUtils->getMlText(__FILE__);

$categoryId = LibEnv::getEnvHttpGET("categoryId");

$linkCategoryUtils->swapWithNext($categoryId);

$str = LibHtml::urlRedirect("$gLinkUrl/category/admin.php");
printContent($str);
return;

?>
