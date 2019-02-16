<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $templateModelId = LibEnv::getEnvHttpPOST("templateModelId");

  if (count($warnings) == 0) {

    $templateModelUtils->exportXML($templateModelId);

    $str = LibHtml::urlRedirect("$gTemplateUrl/design/model/admin.php");
    printContent($str);
    return;

  }

} else {

  $templateModelId = LibEnv::getEnvHttpGET("templateModelId");

  $name = '';
  $description = '';
  if ($templateModel = $templateModelUtils->selectById($templateModelId)) {
    $name = $templateModel->getName();
    $description = $templateModel->getDescription();
  }

}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/design/model/admin.php");
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $description);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('templateModelId', $templateModelId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
