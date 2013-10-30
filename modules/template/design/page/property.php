<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$templatePageTagId = LibEnv::getEnvHttpGET("templatePageTagId");

if ($templatePageTag = $templatePageTagUtils->selectById($templatePageTagId)) {
  $templatePropertySetId = $templatePageTag->getTemplatePropertySetId();

  $templateModelId = $templatePageTagUtils->getTemplateModelId($templatePageTagId);
  $tagId = $templatePageTagUtils->renderTagID($templatePageTagId, true);
  $templatePropertySetUtils->setCurrentPropertyTypes($templatePageUtils->getPropertyTypes());
  $templatePropertySetUtils->setCurrentPropertySetId($templatePropertySetId);
  $templatePropertySetUtils->setCurrentModelId($templateModelId);
  $templatePropertySetUtils->setCurrentTagId($tagId);

  $tagID = $templatePageTag->getTagID();
  $strTitle = $mlText[1] . ' ' . $tagID;

  require_once($gTemplateDesignPath . "property/edit.php");
}

?>
