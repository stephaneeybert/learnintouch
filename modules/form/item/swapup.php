<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FORM);

$mlText = $languageUtils->getMlText(__FILE__);

$formItemId = LibEnv::getEnvHttpGET("formItemId");

$formItemUtils->swapWithPrevious($formItemId);

$str = LibHtml::urlRedirect("$gFormUrl/item/admin.php");
printContent($str);
exit;

?>
