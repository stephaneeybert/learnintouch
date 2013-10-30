<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$listShopCategories = $shopCategoryUtils->getAll();
array_unshift($listShopCategories, '');
$strSelectShopCategory = LibHtml::getSelectList("shopCategoryId", $listShopCategories);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $shopCategoryId = LibEnv::getEnvHttpPOST("shopCategoryId");

  if ($shopCategoryId) {
    $str = $templateUtils->renderJsUpdate($shopCategoryId);
    printMessage($str);
  }

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/select.php");
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $strSelectShopCategory);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
