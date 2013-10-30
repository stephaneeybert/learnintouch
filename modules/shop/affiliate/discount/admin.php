<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$shopAffiliateId = LibEnv::getEnvHttpGET("shopAffiliateId");
if (!$shopAffiliateId) {
  $shopAffiliateId = LibSession::getSessionValue(SHOP_SESSION_AFFILIATE);
} else {
  LibSession::putSessionValue(SHOP_SESSION_AFFILIATE, $shopAffiliateId);
}

$panelUtils->setHeader($mlText[0], "$gShopUrl/affiliate/admin.php");

$help = $popupUtils->getHelpPopup($mlText[7], 300, 200);
$panelUtils->setHelp($help);

$strCommand = "<a href='$gShopUrl/affiliate/discount/edit.php?shopAffiliateId=$shopAffiliateId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[4]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[1]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$shopDiscounts = $shopDiscountUtils->selectByAffiliateId($shopAffiliateId);

$panelUtils->openList();
foreach ($shopDiscounts as $shopDiscount) {
  $shopDiscountId = $shopDiscount->getId();
  $discountCode = $shopDiscount->getDiscountCode();
  $discountRate = $shopDiscount->getDiscountRate();

  $strCommand = "<a href='$gShopUrl/affiliate/discount/edit.php?shopDiscountId=$shopDiscountId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[6]'></a>"
    . " <a href='$gShopUrl/affiliate/discount/delete.php?shopDiscountId=$shopDiscountId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($discountCode, $discountRate, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("shop_affiliate_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
