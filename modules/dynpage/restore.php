<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$dynpageId = LibEnv::getEnvHttpGET("dynpageId");

// Delete from the garbage
$dynpageUtils->restoreFromGarbage($dynpageId);

$str = LibHtml::urlRedirect("$gDynpageUrl/garbage.php");
printMessage($str);
return;

?>
