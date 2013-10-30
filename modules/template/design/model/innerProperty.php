<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$templateModelId = LibEnv::getEnvHttpGET("templateModelId");

if ($templateModel = $templateModelUtils->selectById($templateModelId)) {
  $templatePropertySetId = $templateModel->getInnerTemplatePropertySetId();

  // Create the property set if none
  if (!$templatePropertySetId) {
    $templatePropertySetId = $templatePropertySetUtils->createPropertySet();
    $templateModel->setInnerTemplatePropertySetId($templatePropertySetId);
    $templateModelUtils->update($templateModel);
  }

  $tagId = $templateModelUtils->renderInnerTagID($templateModelId);
  $templatePropertySetUtils->setCurrentPropertyTypes($templateModelUtils->getPropertyTypes());
  $templatePropertySetUtils->setCurrentPropertySetId($templatePropertySetId);
  $templatePropertySetUtils->setCurrentModelId($templateModelId);
  $templatePropertySetUtils->setCurrentTagId($tagId);

  $strTitle = $mlText[1];

  require_once($gTemplateDesignPath . "property/edit.php");
}

?>
