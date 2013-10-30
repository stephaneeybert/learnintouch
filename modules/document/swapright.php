<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DOCUMENT);

$mlText = $languageUtils->getMlText(__FILE__);

$documentId = LibEnv::getEnvHttpGET("documentId");

$documentUtils->swapWithNext($documentId);

$str = LibHtml::urlRedirect("$gDocumentUrl/admin.php");
printContent($str);
return;

?>
