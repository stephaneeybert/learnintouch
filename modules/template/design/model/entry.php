<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $computerEntryModel = LibEnv::getEnvHttpPOST("computerEntryModel");
  $phoneEntryModel = LibEnv::getEnvHttpPOST("phoneEntryModel");
  $computerDefaultModel = LibEnv::getEnvHttpPOST("computerDefaultModel");
  $phoneDefaultModel = LibEnv::getEnvHttpPOST("phoneDefaultModel");

  $computerEntryModel = LibString::cleanString($computerEntryModel);
  $phoneEntryModel = LibString::cleanString($phoneEntryModel);
  $computerDefaultModel = LibString::cleanString($computerDefaultModel);
  $phoneDefaultModel = LibString::cleanString($phoneDefaultModel);

  $templateUtils->setComputerEntry($computerEntryModel);
  $templateUtils->setPhoneEntry($phoneEntryModel);
  $templateUtils->setComputerDefault($computerDefaultModel);
  $templateUtils->setPhoneDefault($phoneDefaultModel);

  $str = LibHtml::urlRedirect("$gTemplateUrl/design/model/admin.php");
  printContent($str);
  return;

  } else {

  // Get the entry model
  $computerEntryModel = $templateUtils->getComputerEntry();
  $phoneEntryModel = $templateUtils->getPhoneEntry();
  $computerDefaultModel = $templateUtils->getComputerDefault();
  $phoneDefaultModel = $templateUtils->getPhoneDefault();

  $modelList = $templateModelUtils->getAllModels();
  $strSelectComputerEntryModel = LibHtml::getSelectList("computerEntryModel", $modelList, $computerEntryModel);
  $strSelectPhoneEntryModel = LibHtml::getSelectList("phoneEntryModel", $modelList, $phoneEntryModel);
  $strSelectComputerDefaultModel = LibHtml::getSelectList("computerDefaultModel", $modelList, $computerDefaultModel);
  $strSelectPhoneDefaultModel = LibHtml::getSelectList("phoneDefaultModel", $modelList, $phoneDefaultModel);

  $panelUtils->setHeader($mlText[0], "$gTemplateUrl/design/model/admin.php");
  $help = $popupUtils->getHelpPopup($mlText[5], 300, 400);
  $panelUtils->setHelp($help);
  $panelUtils->openForm($PHP_SELF, "edit");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $strSelectComputerEntryModel);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "br"), $strSelectComputerDefaultModel);
  $panelUtils->addLine();

  if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_PHONE_MODEL)) {
    $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $strSelectPhoneEntryModel);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[4], "br"), $strSelectPhoneDefaultModel);
    $panelUtils->addLine();
    }

  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
