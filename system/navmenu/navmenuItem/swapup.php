<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$navmenuItemId = LibEnv::getEnvHttpGET("navmenuItemId");

$navmenuItemUtils->swapWithPrevious($navmenuItemId);

$str = LibHtml::urlRedirect("$gNavmenuUrl/admin.php");
printMessage($str);
return;

?>
