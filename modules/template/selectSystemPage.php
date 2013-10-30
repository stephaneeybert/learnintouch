<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);


$listPages = $templateUtils->getSystemPages();
array_unshift($listPages, '');
$strSelectPages = LibHtml::getSelectList("pageId", $listPages);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $pageId = LibEnv::getEnvHttpPOST("pageId");

  if ($pageId) {
    $str = $templateUtils->renderJsUpdate($pageId);
    printMessage($str);
  }

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/select.php");
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $strSelectPages);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
