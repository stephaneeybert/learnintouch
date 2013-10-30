<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$templateContainerId = LibEnv::getEnvHttpGET("templateContainerId");

if ($templateContainer = $templateContainerUtils->selectById($templateContainerId)) {
  $templatePropertySetId = $templateContainer->getTemplatePropertySetId();

  $templateContainer = $templateContainerUtils->getTemplateModelId($templateContainerId);
  $tagId = $templateContainerUtils->renderTagID($templateContainerId);
  $templatePropertySetUtils->setCurrentPropertyTypes($templateContainerUtils->getPropertyTypes());
  $templatePropertySetUtils->setCurrentPropertySetId($templatePropertySetId);
  $templatePropertySetUtils->setCurrentModelId($templateContainer);
  $templatePropertySetUtils->setCurrentTagId($tagId);
  $templatePropertySetUtils->stephane = "Steph";

  $strTitle = $mlText[1];

  require_once($gTemplateDesignPath . "property/edit.php");
}

?>
