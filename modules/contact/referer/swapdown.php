<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

$mlText = $languageUtils->getMlText(__FILE__);

$contactRefererId = LibEnv::getEnvHttpGET("contactRefererId");

$contactRefererUtils->swapWithNext($contactRefererId);

$str = LibHtml::urlRedirect("$gContactUrl/referer/admin.php");
printContent($str);
return;

?>
