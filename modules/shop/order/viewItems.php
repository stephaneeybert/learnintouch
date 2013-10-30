<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$shopOrderId = LibEnv::getEnvHttpGET("shopOrderId");

if (!$shopOrderId) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

if ($shopOrder = $shopOrderUtils->selectById($shopOrderId)) {
  $handlingFee = $shopOrder->getHandlingFee();
  $discountAmount = $shopOrder->getDiscountAmount();

  $currency = $shopItemUtils->defaultCurrency;

  $panelUtils->setHeader($mlText[0]);
  $panelUtils->addLine();

  $totalToPay = 0;
  $totalQuantity = 0;
  $totalPrice = 0;
  $totalPriceInclVAT = 0;
  $totalFee = 0;

  $panelUtils->addLine($panelUtils->addCell($mlText[2], "nb"), $panelUtils->addCell($mlText[3], "nb"), $panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($mlText[9], "nb"));
  $panelUtils->addLine();

  if ($shopOrderItems = $shopOrderItemUtils->selectByShopOrderId($shopOrderId)) {

    foreach ($shopOrderItems as $shopOrderItem) {
      $name = $shopOrderItem->getName();
      $reference = $shopOrderItem->getReference();
      $shortDescription = $shopOrderItem->getShortDescription();
      $price = $shopOrderItem->getPrice();
      $vatRate = $shopOrderItem->getVatRate();
      $shippingFee = $shopOrderItem->getShippingFee();
      $quantity = $shopOrderItem->getQuantity();
      $isGift = $shopOrderItem->getIsGift();
      $options = $shopOrderItem->getOptions();

      if ($vatRate > 0) {
        $VAT = round($price * $vatRate / 100, 2);
        $priceInclVAT = $price + $VAT;
      } else {
        $VAT = 0;
        $priceInclVAT = $price;
      }

      $totalItemPrice = $quantity * $price;
      $totalItemPriceInclVAT = $quantity * $priceInclVAT;
      $totalQuantity = $totalQuantity + $quantity;
      $totalPrice = $totalPrice + $totalItemPrice;
      $totalPriceInclVAT = $totalPriceInclVAT + $totalItemPriceInclVAT;
      $totalFee = $totalFee + ($shippingFee * $quantity);

      $totalItemPrice = $shopItemUtils->decimalFormat($totalItemPrice);
      $price = $shopItemUtils->decimalFormat($price);

      $strItemTotal = "$totalItemPriceInclVAT $currency ($quantity * $priceInclVAT $currency)";

      $panelUtils->addLine($name, $reference, $shortDescription, $panelUtils->addCell($strItemTotal, "n"), $VAT);

      if ($isGift || $options) {
        $panelUtils->addLine('', $mlText[1], $options, '', '');
      }

    }

    $totalFee = $totalFee + $handlingFee;

    $totalToPay = $totalPriceInclVAT + $totalFee;

    $totalFee = $shopItemUtils->decimalFormat($totalFee);

    $panelUtils->addLine();
    $panelUtils->addLine('', '', $panelUtils->addCell($mlText[6], "nbr"), $panelUtils->addCell("$totalFee $currency", "n"), '');

    if ($discountAmount) {
      $totalToPay = $totalToPay - $discountAmount;

      $discountAmount = $shopItemUtils->decimalFormat($discountAmount);

      $panelUtils->addLine();
      $panelUtils->addLine('', '', $panelUtils->addCell($mlText[8], "nbr"), $panelUtils->addCell("$discountAmount $currency", "n"), '');
    }

    $totalToPay = $shopItemUtils->decimalFormat($totalToPay);

    $panelUtils->addLine();
    $panelUtils->addLine('', '', $panelUtils->addCell($mlText[7], "nbr"), $panelUtils->addCell("$totalToPay $currency", "n"), '');

    $panelUtils->addLine();
    $panelUtils->openForm($PHP_SELF);
    $panelUtils->addLine($panelUtils->addCell($panelUtils->getOk(), "c"));
    $panelUtils->addHiddenField('closeWindow', 1);
    $panelUtils->closeForm();
    $str = $panelUtils->render();

    printAdminPage($str);
  }

}

?>
