<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$listLanguages = $languageUtils->getLanguages();
array_unshift($listLanguages, '');
$strSelectLanguages = LibHtml::getSelectList("languageId", $listLanguages);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $languageId = LibEnv::getEnvHttpPOST("languageId");

  if ($languageId) {
    $str = $templateUtils->renderJsUpdate($languageId);
    printMessage($str);
  }

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/select.php");
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $strSelectLanguages);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
