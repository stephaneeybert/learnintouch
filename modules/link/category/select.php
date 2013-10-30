<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);


// Get the list
$listLinkCategories = $linkCategoryUtils->getAll();
array_unshift($listLinkCategories, '');
$strSelectLinkCategory = LibHtml::getSelectList("linkCategoryId", $listLinkCategories);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $linkCategoryId = LibEnv::getEnvHttpPOST("linkCategoryId");

  if ($linkCategoryId) {
    $str = $templateUtils->renderJsUpdate($linkCategoryId);
    printMessage($str);
    }

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
  }

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/select.php");
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $strSelectLinkCategory);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
