<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$templateModelId = LibEnv::getEnvHttpGET("templateModelId");
$templateElementId = LibEnv::getEnvHttpGET("templateElementId");

// Get the model content
$strPreview = $templateModelUtils->previewElement($templateModelId, $templateElementId, true);

// Render some dummy content in the current page so as to fill up the elememt
// if the element is the current page
$dummyContent = $templateUtils->getModelPreviewDummyContent();
$gTemplate->setPageContent($dummyContent);

// Update the non cached content
$str = $templateUtils->updateContent($strPreview);

print($str);

?>
