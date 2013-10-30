<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);


$templateElementId = LibEnv::getEnvHttpGET("templateElementId");
$elementType = LibEnv::getEnvHttpGET("elementType");
$objectId = LibEnv::getEnvHttpGET("objectId");

$editContentUrl = $templateElementUtils->getEditContentUrl($templateElementId, $elementType, $objectId);

$str = LibHtml::urlRedirect($editContentUrl);
printContent($str)

?>
