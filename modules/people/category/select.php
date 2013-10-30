<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$listPeopleCategories = $peopleCategoryUtils->getAll();
array_unshift($listPeopleCategories, '');
$strSelectPeopleCategory = LibHtml::getSelectList("peopleCategoryId", $listPeopleCategories);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $peopleCategoryId = LibEnv::getEnvHttpPOST("peopleCategoryId");

  if ($peopleCategoryId) {
    $str = $templateUtils->renderJsUpdate($peopleCategoryId);
    printMessage($str);
  }

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;

}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/select.php");
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $strSelectPeopleCategory);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
