<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $templateModelId = LibEnv::getEnvHttpPOST("templateModelId");

  $computerEntry = $templateUtils->getComputerEntry();
  $phoneEntry = $templateUtils->getPhoneEntry();
  $computerDefault = $templateUtils->getComputerDefault();
  $phoneDefault = $templateUtils->getPhoneDefault();

  if ($templateModelId == $computerEntry || $templateModelId == $phoneEntry) {
    array_push($warnings, $mlText[5]);
  }

  if ($templateModelId == $computerDefault || $templateModelId == $phoneDefault) {
    array_push($warnings, $mlText[4]);
  }

  if (count($warnings) == 0) {

    $templateModelUtils->deleteTemplateModel($templateModelId);

    $str = LibHtml::urlRedirect("$gTemplateUrl/design/model/admin.php");
    printContent($str);
    return;

  }

} else {

  $templateModelId = LibEnv::getEnvHttpGET("templateModelId");

}

if ($templateModel = $templateModelUtils->selectById($templateModelId)) {
  $name = $templateModel->getName();
  $description = $templateModel->getDescription();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/design/model/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $description);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('templateModelId', $templateModelId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
