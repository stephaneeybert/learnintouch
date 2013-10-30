<?PHP

require_once("website.php");

// Set the Paypal variables needed to request a payment

// Render the Paypal payment form
// This form is automatically sent just after it has been requested.
// It is a redirection and is to avoid directly sending the confirmation form from
// the client to the payment gateway provider. Doing so would risk having users temper
// with the form content.

// Have Paypal Account Optional turned on to accept payments from users that do not
// yet have an account. This option must be turned on by the merchant on his Paypal account.
// Also, set the different options in the payment reception preferences.

$continueButton = LibEnv::getEnvHttpPOST("continueButton");
$shopOrderId = LibEnv::getEnvHttpPOST("shopOrderId");
$firstname = LibEnv::getEnvHttpPOST("firstname");
$lastname = LibEnv::getEnvHttpPOST("lastname");
$telephone = LibEnv::getEnvHttpPOST("telephone");
$email = LibEnv::getEnvHttpPOST("email");
$address1 = LibEnv::getEnvHttpPOST("address1");
$address2 = LibEnv::getEnvHttpPOST("address2");
$zip = LibEnv::getEnvHttpPOST("zip");
$city = LibEnv::getEnvHttpPOST("city");
$state = LibEnv::getEnvHttpPOST("state");
$currency = LibEnv::getEnvHttpPOST("currency");
$handlingFee = LibEnv::getEnvHttpPOST("handlingFee");

// The email address identifying the account
$accountEmail = $propertyUtils->retrieve('SHOP_PAYPAL_ID');

// Have Paypal Account Optional turned on to accept payments from users that do not yet have an account
// This option must be turned on by the merchant on his Paypal account
$str = '';
// The real account
$str .= "\n<form action='https://www.paypal.com/cgi-bin/webscr' method='post' id='sendingForm' name='sendingForm'>";
// The sandbox test account
//$str .= "\n<form action='https://www.sandbox.paypal.com/cgi-bin/webscr' method='post' id='sendingForm' name='sendingForm'>";

// To be redirected to the payment page
$str .= "\n<input type='hidden' name='cmd' value='_cart'>";
$str .= "\n<input type='hidden' name='upload' value='1'>";

// Do not ask for a shipping address
$str .= "\n<input type='hidden' name='no_shipping' value='1'>";

// Do not display a field to type in a note
$str .= "\n<input type='hidden' name='no_note' value='1'>";

// Do not allow the user to change the quantity of each item
$str .= "\n<input type='hidden' name='undefined_quantity' value=''>";

// The text in a navigation buttons
$str .= "\n<input type='hidden' name='cbt' value='$continueButton'>";

// An image and a color scheme to customize the display
$logo = $profileUtils->getLogoFilename();
$imageUrl = $profileUtils->fileUrl . '/' . $logo;
$str .= "\n<input type='hidden' name='image_url' value='$imageUrl'>";

// The url receiving the notification from Paypal that the payment has been completed
$notifyUrl = SHOP_PAYMENT_PAYPAL_NOTIFY;
$str .= "\n<input type='hidden' name='notify_url' value='$notifyUrl'>";

// The url the user is redirected to after his payment has completed
$redirectComplete = SHOP_PAYMENT_PAYPAL_COMPLETE;
$str .= "\n<input type='hidden' name='return' value='$redirectComplete'>";

// The url the user is redirected to after his payment has cancelled
$redirectCancel = SHOP_PAYMENT_PAYPAL_CANCEL;
$str .= "\n<input type='hidden' name='cancel_return' value='$redirectCancel'>";

$str .= "\n<input type='hidden' name='business' value='$accountEmail'>";
$str .= "\n<input type='hidden' name='currency_code' value='$currency'>";

// The order id is passed to paypal so that paypal can pass it back
// to the complete or cancel pages when the payment has been processes
// This is needed to ensure the order is really paid
// The invoice number is the actual order id
$str .= "\n<input type='hidden' name='invoice' value='$shopOrderId'>";

// The customer details
$str .= "\n<input type='hidden' name='first_name' value='$firstname'>";
$str .= "\n<input type='hidden' name='last_name' value='$lastname'>";
$str .= "\n<input type='hidden' name='night_phone_b' value='$telephone'>";
$str .= "\n<input type='hidden' name='email' value='$email'>";

// The address
$str .= "\n<input type='hidden' name='address1' value='$address1'>";
$str .= "\n<input type='hidden' name='address2' value='$address2'>";
$str .= "\n<input type='hidden' name='zip' value='$zip'>";
$str .= "\n<input type='hidden' name='city' value='$city'>";
$str .= "\n<input type='hidden' name='state' value='$state'>";

// For each item of the cart
if ($shopOrderItems = $shopOrderItemUtils->selectByShopOrderId($shopOrderId)) {
  for ($i = 0; $i < count($shopOrderItems); $i++) {
    $shopOrderItem = $shopOrderItems[$i];
    $shopOrderItemId = $shopOrderItem->getId();
    $name = $shopOrderItem->getName();
    $reference = $shopOrderItem->getReference();
    $price = $shopOrderItem->getPrice();
    $vatRate = $shopOrderItem->getVatRate();
    if ($vatRate > 0) {
      $VAT = round($price * $vatRate / 100, 2);
      $priceInclVAT = $price + $VAT;
    } else {
      $VAT = 0;
      $priceInclVAT = $price;
    }

    $priceInclVAT = $shopItemUtils->decimalFormat($priceInclVAT);

    $quantity = $shopOrderItem->getQuantity();
    $shippingFee = $shopOrderItem->getShippingFee();

    // The name must not be empty
    if (!$name) {
      $name = $reference;
    }

    $j = $i + 1;
    $str .= "\n<input type='hidden' name='item_name_$j' value='$name'>";
    $str .= "\n<input type='hidden' name='item_number_$j' value='$reference'>";
    $str .= "\n<input type='hidden' name='quantity_$j' value='$quantity'>";
    $str .= "\n<input type='hidden' name='amount_$j' value='$priceInclVAT'>";
    $str .= "\n<input type='hidden' name='shipping_$j' value='$shippingFee'>";
    $str .= "\n<input type='hidden' name='shipping2_$j' value='$shippingFee'>";
  }
}

if ($shopOrder = $shopOrderUtils->selectById($shopOrderId)) {
  $discountAmount = $shopOrder->getDiscountAmount();

  if ($discountAmount) {
    $str .= "\n<input type='hidden' name='discount_amount_1' value='$discountAmount'>";
  }
}

// The handling fees
$str .= "\n<input type='hidden' name='handling_1' value='$handlingFee'>";

$str .= "\n</form>";

printContent($str, '', "javascript:document.sendingForm.submit();");

?>
