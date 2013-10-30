<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $shopDiscountId = LibEnv::getEnvHttpPOST("shopDiscountId");

  if (count($warnings) == 0) {
    $shopDiscountUtils->delete($shopDiscountId);

    $str = LibHtml::urlRedirect("$gShopUrl/affiliate/discount/admin.php");
    printContent($str);
    return;
  }

} else {

  $shopDiscountId = LibEnv::getEnvHttpGET("shopDiscountId");

}

$discountCode = '';
$discountRate = '';
if ($shopAffiliate = $shopDiscountUtils->selectById($shopDiscountId)) {
  $discountCode = $shopAffiliate->getDiscountCode();
  $discountRate = $shopAffiliate->getDiscountRate();
}

$panelUtils->setHeader($mlText[0], "$gShopUrl/affiliate/discount/admin.php");
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $discountCode);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $discountRate);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('shopDiscountId', $shopDiscountId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
