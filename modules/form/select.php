<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$formId = LibEnv::getEnvHttpPOST("formId");

if ($formId) {
  $str = $templateUtils->renderJsUpdate($formId);
  printMessage($str);

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  exit;
}

$listForms = $formUtils->getListUrls();
array_unshift($listForms, '');
$strSelectForms = LibHtml::getSelectList("formId", $listForms);

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/select.php");
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $strSelectForms);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
