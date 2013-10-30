<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $shopCategoryId = LibEnv::getEnvHttpPOST("shopCategoryId");
  $parentCategoryId = LibEnv::getEnvHttpPOST("parentCategoryId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  if (count($warnings) == 0) {

    if ($shopCategory = $shopCategoryUtils->selectById($shopCategoryId)) {
      $shopCategory->setName($name);
      $shopCategory->setDescription($description);
      $shopCategoryUtils->update($shopCategory);
    } else {
      $shopCategory = new ShopCategory();
      $shopCategory->setName($name);
      $shopCategory->setDescription($description);

      $listOrder = $shopCategoryUtils->getNextListOrder($parentCategoryId);
      $shopCategory->setListOrder($listOrder);
      $shopCategory->setParentCategoryId($parentCategoryId);
      $shopCategoryUtils->insert($shopCategory);
    }

    $str = LibHtml::urlRedirect("$gShopUrl/category/admin.php");
    printContent($str);
    return;

  }

} else {

  $shopCategoryId = LibEnv::getEnvHttpGET("shopCategoryId");

  $parentCategoryId = LibEnv::getEnvHttpGET("parentCategoryId");

  $name = '';
  $description = '';
  if ($shopCategoryId) {
    if ($shopCategory = $shopCategoryUtils->selectById($shopCategoryId)) {
      $name = $shopCategory->getName();
      $description = $shopCategory->getDescription();
      $parentCategoryId = $shopCategory->getParentCategoryId();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gShopUrl/category/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('shopCategoryId', $shopCategoryId);
$panelUtils->addHiddenField('parentCategoryId', $parentCategoryId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
