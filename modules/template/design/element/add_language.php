<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $templateElementId = LibEnv::getEnvHttpPOST("templateElementId");
  $language = LibEnv::getEnvHttpPOST("language");

  $language = LibString::cleanString($language);

  $templateElement = $templateElementUtils->selectById($templateElementId);

  $elementType = $templateElement->getElementType();

  $objectId = $templateElementLanguageUtils->createElementContent($elementType);

  $templateElementLanguageId = $templateElementLanguageUtils->add($templateElementId, $language, $objectId);

  $editContentUrl = $templateElementLanguageUtils->getEditContentUrl($elementType, $templateElementLanguageId, $objectId, $language);
  $str = LibHtml::urlRedirect($editContentUrl);
  printContent($str);
  exit;

}

$templateElementId = LibEnv::getEnvHttpGET("templateElementId");

$languageNames = $templateElementLanguageUtils->getAvailableLanguages($templateElementId, true);
$strSelectLanguage = LibHtml::getSelectList("language", $languageNames);

$panelUtils->setHeader($mlText[0]);
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $strSelectLanguage);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('templateElementId', $templateElementId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
