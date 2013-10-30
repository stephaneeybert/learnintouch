<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$navbarItemId = LibEnv::getEnvHttpGET("navbarItemId");

$navbarItemUtils->swapWithNext($navbarItemId);

$str = LibHtml::urlRedirect("$gNavbarUrl/admin.php");
printContent($str);
return;

?>
