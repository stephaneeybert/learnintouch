<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DOCUMENT);

$mlText = $languageUtils->getMlText(__FILE__);

$categoryId = LibEnv::getEnvHttpGET("categoryId");

$documentCategoryUtils->swapWithPrevious($categoryId);

$str = LibHtml::urlRedirect("$gDocumentUrl/category/admin.php");
printContent($str);
return;

?>
