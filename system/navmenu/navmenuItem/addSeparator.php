<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$parentNavmenuItemId = LibEnv::getEnvHttpGET("parentNavmenuItemId");

// Add a separator item
$navmenuItemUtils->addSeparator($parentNavmenuItemId);

$str = LibHtml::urlRedirect("$gNavmenuUrl/admin.php");
printMessage($str);
return;

?>
