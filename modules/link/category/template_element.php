<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  require_once($gTemplateDesignPath . "element/edit_controller.php");

}

$templateElementLanguageId = LibEnv::getEnvHttpGET("templateElementLanguageId");
$linkCategoryId = LibEnv::getEnvHttpGET("linkCategoryId");

$name = '';
if ($linkCategoryId) {
  if ($linkCategory = $linkCategoryUtils->selectById($linkCategoryId)) {
    $name = $linkCategory->getName();
  }
}

$linkCategories = $linkCategoryUtils->selectAll();
$linkCategoryList = Array('' => '');
foreach ($linkCategories as $linkCategory) {
  $wLinkCategoryId = $linkCategory->getId();
  $wName = $linkCategory->getName();
  $linkCategoryList[$wLinkCategoryId] = $wName;
}
$strSelect = LibHtml::getSelectList("objectId", $linkCategoryList, $linkCategoryId);

$panelUtils->setHeader($mlText[0]);
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $strSelect);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('templateElementLanguageId', $templateElementLanguageId);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
