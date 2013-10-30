<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$templateContainerUtils = new TemplateContainerUtils;

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $templateElementId = LibEnv::getEnvHttpPOST("templateElementId");
  $templateContainerId = LibEnv::getEnvHttpPOST("templateContainerId");

  if (!$templateContainerId) {
    array_push($warnings, $mlText[2]);
  }

  if (count($warnings) == 0) {

    if ($templateElement = $templateElementUtils->selectById($templateElementId)) {
      $templateElementUtils->duplicate($templateElement, $templateContainerId);
    }

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;

  }

} else {

  $templateElementId = LibEnv::getEnvHttpGET("templateElementId");

}

$elementDescription = '';
$templateModelId = '';
if ($templateElementId) {
  if ($templateElement = $templateElementUtils->selectById($templateElementId)) {
    $elementType = $templateElement->getElementType();
    $elementDescription = $templateElementUtils->getDescription($elementType);
    $templateContainerId = $templateElement->getTemplateContainerId();
    if ($templateContainer = $templateContainerUtils->selectById($templateContainerId)) {
      $templateModelId = $templateContainer->getTemplateModelId();
    }
  }
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

if ($templateContainers = $templateContainerUtils->selectByTemplateModelId($templateModelId)) {
  $strContainers = "<table border='0' cellpadding='2' cellspacing='2' style='width:100%; border-style:solid; border-width:1px;'><tr>";
  $previousRow = -1;
  foreach ($templateContainers as $templateContainer) {
    $wTemplateContainerId = $templateContainer->getId();
    $row = $templateContainer->getRow();
    if ($row > $previousRow && $previousRow >= 0) {
      $strContainers .= "</tr></table><table border='0' cellpadding='2' cellspacing='2' style='width:100%; border-style:solid; border-width:1px;'><tr>";
    }
    $strCell = "<input type='radio' name='templateContainerId' value='$wTemplateContainerId'>";
    $strContainers .= "<td style='padding:4px; text-align:center; border-style:solid; border-width:1px;'>$strCell</td>";
    $previousRow = $row;
  }
  $strContainers .= "</tr></table>";
}

$panelUtils->setHeader($mlText[0]);
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $elementDescription);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[3], $mlText[4], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strContainers);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('templateElementId', $templateElementId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
