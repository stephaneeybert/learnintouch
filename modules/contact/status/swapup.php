<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

$mlText = $languageUtils->getMlText(__FILE__);

$contactStatusId = LibEnv::getEnvHttpGET("contactStatusId");

$contactStatusUtils->swapWithPrevious($contactStatusId);

$str = LibHtml::urlRedirect("$gContactUrl/status/admin.php");
printContent($str);
return;

?>
