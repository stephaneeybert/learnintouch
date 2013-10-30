<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$templateModelUtils->templateContainerUtils = $templateContainerUtils;
$templateContainerUtils->templateModelUtils = $templateModelUtils;

$templateModelId = LibEnv::getEnvHttpGET("templateModelId");

$strPreview = $templateModelUtils->preview($templateModelId);

$dummyContent = $templateUtils->getModelPreviewDummyContent();
$gTemplate->setPageContent($dummyContent);

$str = $templateUtils->updateContent($strPreview);

print($str);

?>
