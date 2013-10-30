<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LINK);

$mlText = $languageUtils->getMlText(__FILE__);

$linkId = LibEnv::getEnvHttpGET("linkId");

$linkUtils->swapWithPrevious($linkId);

$str = LibHtml::urlRedirect("$gLinkUrl/admin.php");
printContent($str);
return;

?>
