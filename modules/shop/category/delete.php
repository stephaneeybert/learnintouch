<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $shopCategoryId = LibEnv::getEnvHttpPOST("shopCategoryId");

  // Delete the category only if it is not used
  if ($shopItems = $shopItemUtils->selectByCategoryId($shopCategoryId)) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    $shopCategoryUtils->delete($shopCategoryId);

    $str = LibHtml::urlRedirect("$gShopUrl/category/admin.php");
    printContent($str);
    return;

  }

} else {

  $shopCategoryId = LibEnv::getEnvHttpGET("shopCategoryId");

}

if ($category = $shopCategoryUtils->selectById($shopCategoryId)) {
  $name = $category->getName();
  $description = $category->getDescription();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gShopUrl/category/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('shopCategoryId', $shopCategoryId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
