<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $shopDiscountId = LibEnv::getEnvHttpPOST("shopDiscountId");
  $shopAffiliateId = LibEnv::getEnvHttpPOST("shopAffiliateId");
  $discountCode = LibEnv::getEnvHttpPOST("discountCode");
  $discountRate = LibEnv::getEnvHttpPOST("discountRate");

  $discountCode = LibString::cleanString($discountCode);
  $discountRate = LibString::cleanString($discountRate);

  // Check that the discount code is specified
  if (!$discountCode) {
    array_push($warnings, $mlText[3]);
  }

  // Format the amount
  $discountRate = LibString::formatAmount($discountRate);

  // Check that the discount rate is specified
  if (!$discountRate) {
    array_push($warnings, $mlText[5]);
  }

  // Check that the discount code is not already used
  if (!$shopDiscountUtils->selectById($shopDiscountId)) {
    if ($shopDiscount = $shopDiscountUtils->selectByDiscountCode($discountCode)) {
      array_push($warnings, $mlText[4]);
    }
  }

  if (count($warnings) == 0) {

    if ($shopDiscount = $shopDiscountUtils->selectById($shopDiscountId)) {
      $shopDiscount->setDiscountCode($discountCode);
      $shopDiscount->setDiscountRate($discountRate);
      $shopDiscountUtils->update($shopDiscount);
    } else {
      $shopDiscount = new ShopDiscount();
      $shopDiscount->setDiscountCode($discountCode);
      $shopDiscount->setDiscountRate($discountRate);
      $shopDiscount->setShopAffiliateId($shopAffiliateId);
      $shopDiscountUtils->insert($shopDiscount);
    }

    $str = LibHtml::urlRedirect("$gShopUrl/affiliate/discount/admin.php");
    printContent($str);
    return;

  }

} else {

  $shopDiscountId = LibEnv::getEnvHttpGET("shopDiscountId");
  $shopAffiliateId = LibEnv::getEnvHttpGET("shopAffiliateId");

  $discountCode = LibUtils::generateUniqueId(12);
  $discountRate = '';
  if ($shopDiscountId) {
    if ($shopDiscount = $shopDiscountUtils->selectById($shopDiscountId)) {
      $discountCode = $shopDiscount->getDiscountCode();
      $discountRate = $shopDiscount->getDiscountRate();
      $shopAffiliateId = $shopDiscount->getShopAffiliateId();
    }
  }

}

$discountRate = $shopItemUtils->decimalFormat($discountRate);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gShopUrl/affiliate/discount/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='discountCode' value='$discountCode' size='30' maxlength='12'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='discountRate' value='$discountRate' size='6' maxlength='6'> %");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('shopDiscountId', $shopDiscountId);
$panelUtils->addHiddenField('shopAffiliateId', $shopAffiliateId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
