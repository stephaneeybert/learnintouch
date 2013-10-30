<?php

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$preferenceUtils->init($shopItemUtils->preferences);

$cancelOrder = LibEnv::getEnvHttpPOST("cancelOrder");
$confirmOrder = LibEnv::getEnvHttpPOST("confirmOrder");

if ( $cancelOrder == 1 ) {

  $shopOrderId = LibEnv::getEnvHttpPOST("shopOrderId");

  if ($shopOrder = $shopOrderUtils->selectById($shopOrderId)) {
    $status = $shopOrder->getStatus();
    if ($status == SHOP_ORDER_STATUS_PENDING) {
      $shopOrderUtils->deleteOrder($shopOrderId);
    }
  }

  $str = "\n<div class='system'>"
    . "\n<div class='system_comment'>$websiteText[13]</div>"
    . "\n</div>";
  $str .= LibHtml::urlDisplayRedirect("$gShopUrl/item/displayCart.php", 5);

  $gTemplate->setPageContent($str);
  require_once($gTemplatePath . "render.php");
  return;

}

$shopOrderId = LibEnv::getEnvHttpGET("shopOrderId");
if (!$shopOrderId) {
  $str = LibHtml::urlRedirect("$gShopUrl/order/checkout.php");
  printContent($str);
  return;
}

// Render the payment form
// No bank account nor ordered item related information is visible to the user
$strFormContent = "<input type='hidden' name='continueButton' value='" . $websiteText[22] . "'>";
$strFormContent .= "<input type='hidden' name='shopOrderId' value='$shopOrderId'>";
// The invoice number of the order
$invoiceNumber = $shopOrderId;
$strFormContent .= "<input type='hidden' name='invoice' value='$invoiceNumber'>";

// The address can be preset
if ($shopOrder = $shopOrderUtils->selectById($shopOrderId)) {
  $firstname = $shopOrder->getFirstname();
  $lastname = $shopOrder->getLastname();
  $telephone = $shopOrder->getTelephone();
  $mobilePhone = $shopOrder->getMobilePhone();
  $email = $shopOrder->getEmail();
  $handlingFee = $shopOrder->getHandlingFee();
  $currency = $shopOrder->getCurrency();

  if ($mobilePhone) {
    $telephone = $mobilePhone;
  }

  $strFormContent .= "<input type='hidden' name='firstname' value='$firstname'>";
  $strFormContent .= "<input type='hidden' name='lastname' value='$lastname'>";
  $strFormContent .= "<input type='hidden' name='telephone' value='$telephone'>";
  $strFormContent .= "<input type='hidden' name='email' value='$email'>";
  $strFormContent .= "<input type='hidden' name='handlingFee' value='$handlingFee'>";
  $strFormContent .= "<input type='hidden' name='currency' value='$currency'>";

  $invoiceAddressId = $shopOrder->getInvoiceAddressId();
  if ($address = $addressUtils->selectById($invoiceAddressId)) {
    $address1 = $address->getAddress1();
    $address2 = $address->getAddress2();
    $zipCode = $address->getZipCode();
    $city = $address->getCity();
    $state = $address->getState();
    $strFormContent .= "<input type='hidden' name='address1' value='$address1'>";
    $strFormContent .= "<input type='hidden' name='address2' value='$address2'>";
    $strFormContent .= "<input type='hidden' name='zip' value='$zipCode'>";
    $strFormContent .= "<input type='hidden' name='city' value='$city'>";
    $strFormContent .= "<input type='hidden' name='state' value='$state'>";
  }
}

$bankPaypal = $preferenceUtils->getValue("SHOP_BANK_PAYPAL");
$bankTransfert = $preferenceUtils->getValue("SHOP_BANK_TRANSFERT");

// Check the Paypal bank setup
if ($bankPaypal) {
  $accountEmail = $propertyUtils->retrieve('SHOP_PAYPAL_ID');

  if (!$accountEmail || !LibEmail::validate($accountEmail)) {
    $str = "\n<div class='system'>"
      . "<div class='system_comment'>$websiteText[16]</div>"
      . "</div>";
    $str .= LibHtml::urlDisplayRedirect("$gShopUrl/item/displayCart.php", 10);

    $gTemplate->setPageContent($str);
    require_once($gTemplatePath . "render.php");
    return;
  }
}

if (!$bankPaypal && !$bankTransfert) {

  // If the bank has not been yet specified then refuse payment processing
  $str = "\n<div class='system'>"
    . "<div class='system_comment'>$websiteText[14]</div>"
    . "</div>";
  $str .= LibHtml::urlDisplayRedirect("$gShopUrl/item/displayCart.php", 10);
  $gTemplate->setPageContent($str);
  require_once($gTemplatePath . "render.php");
  return;

}

$listShopItems = array();
$firstname = '';
$lastname = '';
$organisation = '';
$email = '';
$telephone = '';
$mobilePhone = '';
$fax = '';
$message = '';
$handlingFee = '';
$currency = '';
$invoiceAddress1 = '';
$invoiceAddress2 = '';
$invoiceZipCode = '';
$invoiceCity = '';
$invoiceState = '';
$invoiceCountry = '';
$invoicePostalBox = '';
$withShippingAddress = '';
$shippingAddress1 = '';
$shippingAddress2 = '';
$shippingZipCode = '';
$shippingCity = '';
$shippingState = '';
$shippingCountry = '';
$shippingPostalBox = '';

if ($shopOrder = $shopOrderUtils->selectById($shopOrderId)) {
  $firstname = $shopOrder->getFirstname();
  $lastname = $shopOrder->getLastname();
  $organisation = $shopOrder->getOrganisation();
  $email = $shopOrder->getEmail();
  $telephone = $shopOrder->getTelephone();
  $mobilePhone = $shopOrder->getMobilePhone();
  $fax = $shopOrder->getFax();
  $message = $shopOrder->getMessage();
  $handlingFee = $shopOrder->getHandlingFee();
  $discountAmount = $shopOrder->getDiscountAmount();
  $currency = $shopOrder->getCurrency();
  $invoiceAddressId = $shopOrder->getInvoiceAddressId();
  $shippingAddressId = $shopOrder->getShippingAddressId();

  if ($shopOrderItems = $shopOrderItemUtils->selectByShopOrderId($shopOrderId)) {
    foreach ($shopOrderItems as $shopOrderItem) {
      $name = $shopOrderItem->getName();
      $reference = $shopOrderItem->getReference();
      $price = $shopOrderItem->getPrice();
      $shippingFee = $shopOrderItem->getShippingFee();
      $quantity = $shopOrderItem->getQuantity();
      $isGift = $shopOrderItem->getIsGift();
      array_push($listShopItems, array($name, $reference, $price, $shippingFee, $quantity, $isGift));
    }
  }

  if ($invoiceAddress = $addressUtils->selectById($invoiceAddressId)) {
    $invoiceAddress1 = $invoiceAddress->getAddress1();
    $invoiceAddress2 = $invoiceAddress->getAddress2();
    $invoiceZipCode = $invoiceAddress->getZipCode();
    $invoiceCity = $invoiceAddress->getCity();
    $invoiceState = $invoiceAddress->getState();
    $invoiceCountry = $invoiceAddress->getCountry();
    $invoicePostalBox = $invoiceAddress->getPostalBox();

    $shippingAddress1 = $invoiceAddress1;
    $shippingAddress2 = $invoiceAddress2;
    $shippingZipCode = $invoiceZipCode;
    $shippingCity = $invoiceCity;
    $shippingState = $invoiceState;
    $shippingCountry = $invoiceCountry;
    $shippingPostalBox = $invoicePostalBox;

    if ($shippingAddressId) {
      if ($shippingAddress = $addressUtils->selectById($shippingAddressId)) {
        $shippingAddress1 = $shippingAddress->getAddress1();
        $shippingAddress2 = $shippingAddress->getAddress2();
        $shippingZipCode = $shippingAddress->getZipCode();
        $shippingCity = $shippingAddress->getCity();
        $shippingState = $shippingAddress->getState();
        $shippingCountry = $shippingAddress->getCountry();
        $shippingPostalBox = $shippingAddress->getPostalBox();
      }
    }
  }
} else {
  // If the order could not be retrieved then then refuse payment processing
  $str = $websiteText[13];
  $str .= LibHtml::urlDisplayRedirect("$gShopUrl/item/displayCart.php", 10);
  $gTemplate->setPageContent($str);
  require_once($gTemplatePath . "render.php");
  return;
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

if (count($listShopItems) > 0) {
  $str .= "\n<div class='system_comment'>$websiteText[11]</div>";
} else {
  $str .= "\n<div class='system_comment'>$websiteText[20]</div>";
}

$str .= "\n<div class='system_form'>";

// Check that there are some items being ordered
if (count($listShopItems) > 0) {

  $totalQuantity = 0;
  $totalPrice = 0;
  $totalFee = 0;

  $str .= "\n<div class='system_label'>$websiteText[21]</div>";

  foreach ($listShopItems as $listShopItem) {
    $name = $listShopItem[0];
    $reference = $listShopItem[1];
    $price = $listShopItem[2];
    $shippingFee = $listShopItem[3];
    $quantity = $listShopItem[4];
    $isGift = $listShopItem[5];

    $itemsPrice = $price * $quantity;

    $totalItemPrice = $quantity * $price;
    $totalQuantity = $totalQuantity + $quantity;
    $totalPrice = $totalPrice + $totalItemPrice;
    $totalFee = $totalFee + ($shippingFee * $quantity);

    $itemsPrice = $shopItemUtils->decimalFormat($itemsPrice);
    $price = $shopItemUtils->decimalFormat($price);

    $str .= "\n<div class='system_label'>$name</div>";
    $str .= "\n<div class='system_field'>$itemsPrice $currency ( $quantity * $price $currency )</div>";
  }

  $totalFee = $totalFee + $handlingFee;

  $totalToPay = $totalPrice + $totalFee;

  $totalPrice = $shopItemUtils->decimalFormat($totalPrice);

  if ($discountAmount) {
    $totalToPay = $totalToPay - $discountAmount;
  }

  $totalFee = $shopItemUtils->decimalFormat($totalFee);

  $totalToPay = $shopItemUtils->decimalFormat($totalToPay);

  $str .= "\n<div class='system_label'>" . $totalQuantity . ' ' . $websiteText[112] . "</div>"
    . "<div class='system_field'>" . $totalPrice . ' ' . $currency . "</div>";

  if ($discountAmount) {
    $str .= "\n<div class='system_label'>" . $websiteText[23] . "</div>"
      . "<div class='system_field'>" . $discountAmount . ' ' . $currency . "</div>";
  }

  $str .= "\n<div class='system_label'>" . $websiteText[110] . "</div>"
    . "<div class='system_field'>" . $totalFee . ' ' . $currency . "</div>";

  $str .= "\n<div class='system_label'>" . $websiteText[111] . "</div>"
    . "<div class='system_field'>" . $totalToPay . ' ' . $currency . "</div>";

  $str .= "\n<div class='system_label'>$websiteText[19]</div>";

  $str .= "\n<div class='system_label'>$websiteText[1]</div>";
  $str .= "\n<div class='system_field'>$firstname</div>";

  $str .= "\n<div class='system_label'>$websiteText[2]</div>";
  $str .= "\n<div class='system_field'>$lastname</div>";

  if ($organisation) {
    $str .= "\n<div class='system_label'>$websiteText[4]</div>";
    $str .= "\n<div class='system_field'>$organisation</div>";
  }

  $str .= "\n<div class='system_label'>$websiteText[3]</div>";
  $str .= "\n<div class='system_field'>$email</div>";

  if ($telephone) {
    $str .= "\n<div class='system_label'>$websiteText[5]</div>";
    $str .= "\n<div class='system_field'>$telephone</div>";
  }

  if ($mobilePhone) {
    $str .= "\n<div class='system_label'>$websiteText[6]</div>";
    $str .= "\n<div class='system_field'>$mobilePhone</div>";
  }

  if ($fax) {
    $str .= "\n<div class='system_label'>$websiteText[47]</div>";
    $str .= "\n<div class='system_field'>$fax</div>";
  }

  $str .= "\n<div class='system_label'><br /><b>$websiteText[36]</b></div>";
  $str .= "\n<div class='system_field'><br /></div>";

  $str .= "\n<div class='system_label'>$websiteText[30]</div>";
  $str .= "\n<div class='system_field'>$invoiceAddress1</div>";

  if ($invoiceAddress2) {
    $str .= "\n<div class='system_field'>$invoiceAddress2</div>";
  }

  $str .= "\n<div class='system_label'>$websiteText[32]</div>";
  $str .= "\n<div class='system_field'>$invoiceZipCode</div>";

  $str .= "\n<div class='system_label'>$websiteText[33]</div>";
  $str .= "\n<div class='system_field'>$invoiceCity</div>";

  if ($invoiceState) {
    $str .= "\n<div class='system_label'>$websiteText[34]</div>";
    $str .= "\n<div class='system_field'>$invoiceState</div>";
  }

  $str .= "\n<div class='system_label'>$websiteText[35]</div>";
  $str .= "\n<div class='system_field'>$invoiceCountry</div>";

  $str .= "\n<div class='system_label'>$websiteText[17]</div>";
  $str .= "\n<div class='system_field'>$invoicePostalBox</div>";

  $str .= "\n<div class='system_label'><br /><b>$websiteText[37]</b></div>";
  $str .= "\n<div class='system_field'><br /></div>";

  $str .= "\n<div class='system_label'>$websiteText[30]</div>";
  $str .= "\n<div class='system_field'>$shippingAddress1</div>";

  if ($shippingAddress2) {
    $str .= "\n<div class='system_field'>$shippingAddress2</div>";
  }

  $str .= "\n<div class='system_label'>$websiteText[32]</div>";
  $str .= "\n<div class='system_field'>$shippingZipCode</div>";

  $str .= "\n<div class='system_label'>$websiteText[33]</div>";
  $str .= "\n<div class='system_field'>$shippingCity</div>";

  if ($shippingState) {
    $str .= "\n<div class='system_label'>$websiteText[34]</div>";
    $str .= "\n<div class='system_field'>$shippingState</div>";
  }

  $str .= "\n<div class='system_label'>$websiteText[35]</div>";
  $str .= "\n<div class='system_field'>$shippingCountry</div>";

  $str .= "\n<div class='system_label'>$websiteText[17]</div>";
  $str .= "\n<div class='system_field'>$shippingPostalBox</div>";

  if ($message) {
    $str .= "\n<div class='system_label'>$websiteText[7]</div>";
    $str .= "\n<div class='system_field'>$message</div>";
  }

  // Check if only the Paypal bank was specified
  if ($bankPaypal) {
    $str .= "<form action='$gShopUrl/payment/paypal/hiddenForm.php' method='post'>"
      . "<div class='system_label'>$websiteText[8]"
      . $strFormContent
      . " <input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' name='submit' value='$shopOrderId' style='vertical-align:middle;' />"
      . "</div>"
      . "</form>";
  }

  // Check if only the bank transfer was specified
  // For an bank transfer payment, that is, a manual payment, no on-line payment is being made
  // and the order is set a pending
  if ($bankTransfert) {
    $str .= "<form action='$gShopUrl/payment/transfer/notify.php' method='post'>"
      . "<div class='system_label'>$websiteText[15]"
      . $strFormContent
      . " <input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' name='submit' value='$shopOrderId' style='vertical-align:middle;' />"
      . "</div>"
      . "</form>";
  }

}

$hideCancelButton = $preferenceUtils->getValue("SHOP_HIDE_CANCEL_BUTTON");

if (!$hideCancelButton) {
  $str .= "<form action='$gShopUrl/order/confirm.php' method='post'>"
    . "<div class='system_label'>$websiteText[10]"
    . "<input type='image' src='$gImagesUserUrl/" . IMAGE_SHOP_CANCEL . "' />"
    . "</div>"
    . "<input type='hidden' name='cancelOrder' value='1' />"
    . "<input type='hidden' name='shopOrderId' value='$shopOrderId' />"
    . "</form>";
}

$str .= "\n</div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
