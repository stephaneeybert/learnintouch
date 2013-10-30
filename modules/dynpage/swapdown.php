<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$mlText = $languageUtils->getMlText(__FILE__);

$dynpageId = LibEnv::getEnvHttpGET("dynpageId");

$dynpageUtils->swapWithNext($dynpageId);

$str = LibHtml::urlRedirect("$gDynpageUrl/admin.php");
printMessage($str);
return;

?>
