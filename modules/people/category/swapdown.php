<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PEOPLE);

$categoryId = LibEnv::getEnvHttpGET("categoryId");

$peopleCategoryUtils->swapWithNext($categoryId);

$str = LibHtml::urlRedirect("$gPeopleUrl/category/admin.php");
printContent($str);
return;

?>
