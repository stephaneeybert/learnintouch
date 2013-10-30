<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$templateTagId = LibEnv::getEnvHttpGET("templateTagId");

if ($templateTag = $templateTagUtils->selectById($templateTagId)) {
  $templatePropertySetId = $templateTag->getTemplatePropertySetId();

  $templateModelId = $templateTagUtils->getTemplateModelId($templateTagId);
  $tagId = $templateTagUtils->renderTagID($templateTagId, true);
  $templatePropertySetUtils->setCurrentPropertyTypes($templateElementUtils->getPropertyTypes());
  $templatePropertySetUtils->setCurrentPropertySetId($templatePropertySetId);
  $templatePropertySetUtils->setCurrentModelId($templateModelId);
  $templatePropertySetUtils->setCurrentTagId($tagId);

  $tagID = $templateTag->getTagID();
  $strTitle = $mlText[1] . ' "' . $templateTagUtils->getTagName($tagID) . '"';

  require_once($gTemplateDesignPath . "property/edit.php");
}

?>
