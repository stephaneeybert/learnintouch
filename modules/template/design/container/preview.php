<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$templateModelUtils->templateContainerUtils = $templateContainerUtils;
$templateContainerUtils->templateModelUtils = $templateModelUtils;

$templateModelId = LibEnv::getEnvHttpGET("templateModelId");
$templateContainerId = LibEnv::getEnvHttpGET("templateContainerId");

// Get the container content
$strPreview = $templateModelUtils->previewContainer($templateModelId, $templateContainerId);

// Render some dummy content in the current page so as to fill up the container
// if the container contains the current page element
$dummyContent = $templateUtils->getModelPreviewDummyContent();
$gTemplate->setPageContent($dummyContent);

// Update the non cached content
$str = $templateUtils->updateContent($strPreview);

print($str);

?>
