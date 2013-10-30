<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$shopOrderUtils->cancelPendingOrders();

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
$orderStatus = LibEnv::getEnvHttpPOST("orderStatus");
$month = LibEnv::getEnvHttpPOST("month");

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(SHOP_SESSION_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(SHOP_SESSION_SEARCH_PATTERN, $searchPattern);
}

if (!$orderStatus) {
  $orderStatus = LibSession::getSessionValue(SHOP_SESSION_ORDER_STATUS);
} else {
  LibSession::putSessionValue(SHOP_SESSION_ORDER_STATUS, $orderStatus);
}

if (!$month) {
  $month = LibSession::getSessionValue(SHOP_SESSION_MONTH);
} else {
  LibSession::putSessionValue(SHOP_SESSION_MONTH, $month);
}

if ($searchPattern) {
  $orderStatus = '';
  $month = '-1';
  LibSession::putSessionValue(SHOP_SESSION_ORDER_STATUS, '');
  LibSession::putSessionValue(SHOP_SESSION_MONTH, '');
}

$lastMonth = date("m", $clockUtils->getLocalTimeStamp());
if (!$month) {
  $month = $lastMonth;
}
$year = date("Y", $clockUtils->getLocalTimeStamp());

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[2], 300, 400);
$panelUtils->setHelp($help);

$searchPattern = LibString::cleanString($searchPattern);

$panelUtils->openForm($PHP_SELF);
$labelSearch = $popupUtils->getTipPopup($mlText[10], $mlText[11], 300, 300);
$strSearch = "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . $panelUtils->getTinyOk();

$strCommand = "<a href='$gShopUrl/item/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageItem' title='$mlText[23]'></a>"
  . " <a href='$gShopUrl/affiliate/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImagePeople' title='$mlText[13]'></a>"
  . " <a href='$gShopUrl/payment/recipient.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[21]'></a>"
  . " <a href='$gShopUrl/payment/banks.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[19]'></a>"
  . " <a href='$gShopUrl/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[20]'></a>";

$panelUtils->addLine($panelUtils->addCell($labelSearch, "nbr"), $panelUtils->addCell($strSearch, "n"), '', '', '', '', $panelUtils->addCell($strCommand, "nbr"));

error_log("month: $month lastMonth: $lastMonth");
$listMonths = array(-1 => '');
for ($wMonth = 1; $wMonth <= $lastMonth; $wMonth++) {
  $monthName = ucfirst(strftime("%B", strtotime("$year-$wMonth-01")));
  $listMonths[$wMonth] = $monthName;
}
$strSelectMonth = LibHtml::getSelectList("month", $listMonths, $month, true);

$orderStatuses = $shopItemUtils->getOrderStatuses();
$strSelectStatus = LibHtml::getSelectList("orderStatus", $orderStatuses, $orderStatus, true);

$labelStatus = $popupUtils->getTipPopup($mlText[3], $mlText[1], 300, 300);
$labelMonth = $popupUtils->getTipPopup($mlText[17], $mlText[18], 300, 300);
$panelUtils->addLine($panelUtils->addCell($labelStatus, "nbr"), $panelUtils->addCell($strSelectStatus, "n"), '', '', '', '', '');
$panelUtils->addLine($panelUtils->addCell($labelMonth, "nbr"), $panelUtils->addCell($strSelectMonth, "n"), '', '', '', '', '');
$panelUtils->addLine();

$shopCancelledOrders = $shopOrderUtils->selectByStatus(SHOP_ORDER_STATUS_CANCELLED);
if (count($shopCancelledOrders) > 0) {
  $strCommand = "<a href='$gShopUrl/order/deleteCancelled.php' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageGarbage' title='$mlText[4]'></a>";
} else {
  $strCommand = '';
}

$panelUtils->addLine($panelUtils->addCell($mlText[8], "nb"), $panelUtils->addCell("$mlText[15]", "nbc"), $panelUtils->addCell("$mlText[14]", "nb"), $panelUtils->addCell("$mlText[12]", "nb"), $panelUtils->addCell("$mlText[9]", "nbr"), $panelUtils->addCell("$mlText[16]", "nbc"), $panelUtils->addCell($strCommand, "nr"));

$preferenceUtils->init($shopItemUtils->preferences);
$listStep = $preferenceUtils->getValue("SHOP_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $shopOrders = $shopOrderUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else if ($orderStatus && $year > 0 && $month > 0) {
  $shopOrders = $shopOrderUtils->selectByStatusAndYearAndMonth($orderStatus, $year, $month);
} else if ($orderStatus) {
  $shopOrders = $shopOrderUtils->selectByStatus($orderStatus, $listIndex, $listStep);
} else if ($year > 0 && $month > 0) {
  $shopOrders = $shopOrderUtils->selectByYearAndMonth($year, $month);
} else {
  $shopOrders = $shopOrderUtils->selectAll($listIndex, $listStep);
}

$listNbItems = $shopOrderUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($shopOrders as $shopOrder) {
  $shopOrderId = $shopOrder->getId();
  $firstname = $shopOrder->getFirstname();
  $lastname = $shopOrder->getLastname();
  $organisation = $shopOrder->getOrganisation();
  $email = $shopOrder->getEmail();
  $telephone = $shopOrder->getTelephone();
  $mobilePhone = $shopOrder->getMobilePhone();
  $orderDate = $shopOrder->getOrderDate();
  $dueDate = $shopOrder->getDueDate();
  $clientIP = $shopOrder->getClientIP();
  $status = $shopOrder->getStatus();
  $currency = $shopOrder->getCurrency();

  $totalToPay = $shopOrderUtils->getTotalToPay($shopOrder);

  $totalToPay = $shopItemUtils->decimalFormat($totalToPay);

  $strCommand = "<a href='$gShopUrl/order/edit.php?shopOrderId=$shopOrderId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[5]'></a>";
  $strCommand .= " <a href='$gShopUrl/order/pdf_invoice.php?shopOrderId=$shopOrderId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImagePdf' title='$mlText[6]'></a>";
  if ($clientIP) {
    $ipUrl = $commonUtils->mapIP($clientIP);
    $strCommand .= ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[22]'>", "$ipUrl", 800, 800);
  }
  if ($status != SHOP_ORDER_STATUS_CANCELLED) {
    $strCommand .= " <a href='$gShopUrl/order/cancel.php?shopOrderId=$shopOrderId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageCancel' title='$mlText[7]'></a>";
  }

  $strName = $firstname . ' ' . $lastname;

  if ($organisation) {
    $strName .= ' ' . $organisation;
  }

  $strName = "<a href='mailto:$email'>$strName</a>";

  $orderDate = $clockUtils->systemToLocalNumericDate($orderDate);
  $dueDate = $clockUtils->systemToLocalNumericDate($dueDate);

  $panelUtils->addLine($strName, $panelUtils->addCell($shopOrderId, "nc"), $panelUtils->addCell($orderDate, "n"), $panelUtils->addCell($dueDate, "n"), $panelUtils->addCell("$totalToPay $currency", "nr"), $panelUtils->addCell($orderStatuses[$status], "nc"), $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("shop_order_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
