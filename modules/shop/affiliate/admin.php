<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(SHOP_SESSION_AFFILIATE_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(SHOP_SESSION_AFFILIATE_SEARCH_PATTERN, $searchPattern);
}
$searchPattern = LibString::cleanString($searchPattern);

$panelUtils->setHeader($mlText[0], "$gShopUrl/order/admin.php");

$help = $popupUtils->getHelpPopup($mlText[7], 300, 200);
$panelUtils->setHelp($help);

$labelSearch = $popupUtils->getTipPopup($mlText[8], $mlText[9], 300, 300);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . $panelUtils->getTinyOk()
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "</form>";
$panelUtils->addLine($panelUtils->addCell($labelSearch, "nbr"), $panelUtils->addCell($strSearch, "n"), '');

$strCommand = "<a href='$gShopUrl/affiliate/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[4]'></a>";

$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[1]", "nb"), $panelUtils->addCell($strCommand, "nbr"));

$preferenceUtils->init($shopItemUtils->preferences);
$listStep = $preferenceUtils->getValue("SHOP_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $shopAffiliates = $shopAffiliateUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else {
  $shopAffiliates = $shopAffiliateUtils->selectAll($listIndex, $listStep);
}

$listNbItems = $shopAffiliateUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($shopAffiliates as $shopAffiliate) {
  $shopAffiliateId = $shopAffiliate->getId();
  $firstname = $shopAffiliateUtils->getFirstname($shopAffiliateId);
  $lastname = $shopAffiliateUtils->getLastname($shopAffiliateId);
  $email = $shopAffiliateUtils->getEmail($shopAffiliateId);
  $email = $shopAffiliateUtils->renderEmail($email);

  $discountCodes = $shopAffiliateUtils->getDiscountCodes($shopAffiliateId);

  $strCommand = "<a href='$gShopUrl/affiliate/discount/admin.php?shopAffiliateId=$shopAffiliateId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[6]'></a>"
    . " <a href='$gShopUrl/affiliate/delete.php?shopAffiliateId=$shopAffiliateId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($firstname . ' ' . $lastname, $discountCodes, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("shop_affiliate_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
