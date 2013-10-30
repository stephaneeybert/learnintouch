<?php

require_once("website.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormat.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatDao.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatDB.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatUtils.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$preferenceUtils->init($shopItemUtils->preferences);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ( $formSubmitted == 1 ) {

  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");
  $organisation = LibEnv::getEnvHttpPOST("organisation");
  $email = LibEnv::getEnvHttpPOST("email");
  $telephone = LibEnv::getEnvHttpPOST("telephone");
  $mobilePhone = LibEnv::getEnvHttpPOST("mobilePhone");
  $fax = LibEnv::getEnvHttpPOST("fax");
  $message = LibEnv::getEnvHttpPOST("message");
  $securityCode = LibEnv::getEnvHttpPOST("securityCode");
  $invoiceAddress1 = LibEnv::getEnvHttpPOST("invoiceAddress1");
  $invoiceAddress2 = LibEnv::getEnvHttpPOST("invoiceAddress2");
  $invoiceZipCode = LibEnv::getEnvHttpPOST("invoiceZipCode");
  $invoiceCity = LibEnv::getEnvHttpPOST("invoiceCity");
  $invoiceState = LibEnv::getEnvHttpPOST("invoiceState");
  $invoiceCountry = LibEnv::getEnvHttpPOST("invoiceCountry");
  $invoicePostalBox = LibEnv::getEnvHttpPOST("invoicePostalBox");
  $withShippingAddress = LibEnv::getEnvHttpPOST("withShippingAddress");
  $shippingAddress1 = LibEnv::getEnvHttpPOST("shippingAddress1");
  $shippingAddress2 = LibEnv::getEnvHttpPOST("shippingAddress2");
  $shippingZipCode = LibEnv::getEnvHttpPOST("shippingZipCode");
  $shippingCity = LibEnv::getEnvHttpPOST("shippingCity");
  $shippingState = LibEnv::getEnvHttpPOST("shippingState");
  $shippingCountry = LibEnv::getEnvHttpPOST("shippingCountry");
  $shippingPostalBox = LibEnv::getEnvHttpPOST("shippingPostalBox");

  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);
  $organisation = LibString::cleanString($organisation);
  $email = LibString::cleanString($email);
  $telephone = LibString::cleanString($telephone);
  $mobilePhone = LibString::cleanString($mobilePhone);
  $fax = LibString::cleanString($fax);
  $message = LibString::cleanString($message);
  $securityCode = LibString::cleanString($securityCode);
  $invoiceAddress1 = LibString::cleanString($invoiceAddress1);
  $invoiceAddress2 = LibString::cleanString($invoiceAddress2);
  $invoiceZipCode = LibString::cleanString($invoiceZipCode);
  $invoiceCity = LibString::cleanString($invoiceCity);
  $invoiceState = LibString::cleanString($invoiceState);
  $invoiceCountry = LibString::cleanString($invoiceCountry);
  $invoicePostalBox = LibString::cleanString($invoicePostalBox);
  $withShippingAddress = LibString::cleanString($withShippingAddress);
  $shippingAddress1 = LibString::cleanString($shippingAddress1);
  $shippingAddress2 = LibString::cleanString($shippingAddress2);
  $shippingZipCode = LibString::cleanString($shippingZipCode);
  $shippingCity = LibString::cleanString($shippingCity);
  $shippingState = LibString::cleanString($shippingState);
  $shippingCountry = LibString::cleanString($shippingCountry);
  $shippingPostalBox = LibString::cleanString($shippingPostalBox);

  // The firstname is required
  if (!$firstname) {
    array_push($warnings, $websiteText[12]);
  }

  // The lastname is required
  if (!$lastname) {
    array_push($warnings, $websiteText[9]);
  }

  // The email is required
  if (!$email) {
    array_push($warnings, $websiteText[10]);
  } else if ($email && !LibEmail::validate($email)) {
    array_push($warnings, $websiteText[11]);
  } else if ($email && !LibEmail::validateDomain($email)) {
    // The email domain must be registered as a mail domain
    array_push($warnings, $websiteText[18]);
  }

  // Check for a security code
  if ($preferenceUtils->getValue("SHOP_SECURITY_CODE")) {
    $randomSecurityCode = LibSession::getSessionValue(UTILS_SESSION_RANDOM_SECURITY_CODE);
    if (!$securityCode) {
      // The security code is required
      array_push($warnings, $websiteText[44]);
    } else if ($securityCode != $randomSecurityCode) {
      // The security code is incorrect
      array_push($warnings, $websiteText[45]);
    }
  }

  // The invoice address is required
  if (!$invoiceAddress1) {
    array_push($warnings, $websiteText[38]);
  }
  if (!$invoiceZipCode) {
    array_push($warnings, $websiteText[39]);
  }
  if (!$invoiceCity) {
    array_push($warnings, $websiteText[41]);
  }
  if (!$invoiceCountry) {
    array_push($warnings, $websiteText[42]);
  }

  // The shipping address must be complete if any
  if ($withShippingAddress) {
    if (!$shippingAddress1) {
      array_push($warnings, $websiteText[38]);
    }
    if (!$shippingZipCode) {
      array_push($warnings, $websiteText[39]);
    }
    if ($shippingZipCode && !is_numeric(LibString::stripSpaces($shippingZipCode))) {
      array_push($warnings, $websiteText[40]);
    }
    if (!$shippingCity) {
      array_push($warnings, $websiteText[41]);
    }
    if (!$shippingCountry) {
      array_push($warnings, $websiteText[42]);
    }
  }

  // Get the cart items
  $items = $shopItemUtils->getCart();

  // Get the discount
  list($discountCode, $discountAmount) = $shopItemUtils->getDiscount();

  // Check that the shopping cart is not empty
  if (count($items) == 0) {
    array_push($warnings, $websiteText[27]);
  }

  if (count($warnings) == 0) {

    // Save the invoice address
    $invoiceAddress = new Address();
    $invoiceAddress->setAddress1($invoiceAddress1);
    $invoiceAddress->setAddress2($invoiceAddress2);
    $invoiceAddress->setZipCode($invoiceZipCode);
    $invoiceAddress->setCity($invoiceCity);
    $invoiceAddress->setState($invoiceState);
    $invoiceAddress->setCountry($invoiceCountry);
    $invoiceAddress->setPostalBox($invoicePostalBox);
    $addressUtils->insert($invoiceAddress);
    $invoiceAddressId = $addressUtils->getLastInsertId();

    // Save the shipping address if any
    $shippingAddressId = '';
    if ($withShippingAddress) {
      $shippingAddress = new Address();
      $shippingAddress->setAddress1($shippingAddress1);
      $shippingAddress->setAddress2($shippingAddress2);
      $shippingAddress->setZipCode($shippingZipCode);
      $shippingAddress->setCity($shippingCity);
      $shippingAddress->setState($shippingState);
      $shippingAddress->setCountry($shippingCountry);
      $shippingAddress->setPostalBox($shippingPostalBox);
      $addressUtils->insert($shippingAddress);
      $shippingAddressId = $addressUtils->getLastInsertId();
    }

    // The currency for the order amounts
    $currency = $shopItemUtils->getCurrency();

    // Handling fees for administrative work
    $handlingFee = $shopItemUtils->getHandlingFee();

    // Save the order
    $shopOrder = new ShopOrder();
    $shopOrder->setFirstname($firstname);
    $shopOrder->setLastname($lastname);
    $shopOrder->setEmail($email);
    $shopOrder->setOrganisation($organisation);
    $shopOrder->setTelephone($telephone);
    $shopOrder->setMobilePhone($mobilePhone);
    $shopOrder->setFax($fax);
    $shopOrder->setMessage($message);
    $shopOrder->setHandlingFee($handlingFee);
    $shopOrder->setDiscountCode($discountCode);
    $shopOrder->setDiscountAmount($discountAmount);
    $shopOrder->setCurrency($currency);
    $languageCode = $languageUtils->getCurrentLanguageCode();
    if ($languageCode == 'fr' || $languageCode == 'en') {
      $shopOrder->setInvoiceLanguage($languageCode);
    }
    $shopOrder->setInvoiceAddressId($invoiceAddressId);
    $shopOrder->setShippingAddressId($shippingAddressId);
    $systemDate = $clockUtils->getSystemDate();
    $shopOrder->setOrderDate($systemDate);
    $shopOrder->setDueDate($systemDate);
    $shopOrder->setClientIP($REMOTE_ADDR);
    $shopOrder->setStatus(SHOP_ORDER_STATUS_PENDING);
    $shopOrder->setPaymentType(SHOP_ORDER_PAYMENT_CARD);
    $shopOrderUtils->insert($shopOrder);
    $shopOrderId = $shopOrderUtils->getLastInsertId();

    for ($i = 0; $i < count($items); $i++) {
      $itemType = $items[$i][0];
      $itemId = $items[$i][1];
      $quantity = $items[$i][2];
      $isGift = $items[$i][3];
      $options = $items[$i][4];
      $strOptions = $shopItemUtils->renderOptions($options);

      // Get the item properties
      if ($itemType == SHOP_CART_ITEM && $shopItem = $shopItemUtils->selectById($itemId)) {
        $name = $shopItem->getName();
        $shortDescription = $shopItem->getShortDescription();
        $reference = $shopItem->getReference();
        $price = $shopItem->getPrice();
        $vatRate = $shopItem->getVatRate();
        $shippingFee = $shopItem->getShippingFee();
        if (!$shippingFee) {
          $shippingFee = $shopItemUtils->getShippingFee();
        }
        $shopItemId = $itemId;
        $imageUrl = '';
      } else if ($itemType == SHOP_CART_PHOTO && $photo = $photoUtils->selectById($itemId)) {
        $photoFormatId = '';
        $frame = '';
        $aspect = '';
        if ($options) {
          if (strstr($options, SHOP_CART_OPTION_SEPARATOR)) {
            list($photoFormatId, $frame, $aspect) = explode(SHOP_CART_OPTION_SEPARATOR, $options);
          }
        }

        $name = $photo->getName();
        $shortDescription = $photo->getDescription();
        $reference = $photo->getReference();
        $price = $photoUtils->getPhotoPrice($itemId, $photoFormatId);
        $vatRate = 0;
        $shippingFee = $shopItemUtils->getShippingFee();
        $shopItemId = '';
        $imageUrl = "display_photo.php?photoId=$itemId";
      }

      if ($quantity > 0) {
        $shopOrderItem = new ShopOrderItem();
        $shopOrderItem->setName($name);
        $shopOrderItem->setShortDescription($shortDescription);
        $shopOrderItem->setReference($reference);
        $shopOrderItem->setPrice($price);
        $shopOrderItem->setVatRate($vatRate);
        $shopOrderItem->setShippingFee($shippingFee);
        $shopOrderItem->setQuantity($quantity);
        $shopOrderItem->setIsGift($isGift);
        $shopOrderItem->setOptions($strOptions);
        $shopOrderItem->setShopOrderId($shopOrderId);
        $shopOrderItem->setShopItemId($shopItemId);
        $shopOrderItem->setImageUrl($imageUrl);
        $shopOrderItemUtils->insert($shopOrderItem);
        $shopOrderItemId = $shopOrderItemUtils->getLastInsertId();
      }
    }

    LibSession::putSessionValue(SHOP_SESSION_ORDER, $shopOrderId);

    $str = LibHtml::urlRedirect("$gShopUrl/order/confirm.php?shopOrderId=$shopOrderId");
    printContent($str);
    exit;
  }

}

// Init the unset variables
if (!$formSubmitted) {
  $firstname = '';
  $lastname = '';
  $organisation = '';
  $email = '';
  $telephone = '';
  $mobilePhone = '';
  $fax = '';
  $message = '';
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
}

$str = '';

$str .= "\n<div class='system'>";

$strViewCart = "\n<a href='$gShopUrl/item/displayCart.php' $gJSNoStatus title='" .  $websiteText[48] . "'>" . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_CART . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

$str .= "\n<div class='shop_item_icon'>" . $strViewCart . "\n</div>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$comment = $preferenceUtils->getValue("SHOP_COMMENT");
if (!$comment) {
  $comment = $websiteText[8];
}

$str .= "\n<div class='system_comment'>$comment</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$securityCodeFontSize = $templateUtils->getSecurityCodeFontSize($gIsPhoneClient);

$str .= "\n<form name='checkout' id='checkout' action='$gShopUrl/order/checkout.php' method='post'>";

$str .= "\n<div class='system_form'>";

$str .= "\n<div class='system_label'>$websiteText[1]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='firstname' value='$firstname' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[2]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='lastname' value='$lastname' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[4]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='organisation' value='$organisation' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[3]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='email' value='$email' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[5]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='telephone' value='$telephone' size='25' maxlength='20' /></div>";

$str .= "\n<div class='system_label'>$websiteText[6]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='mobilePhone' value='$mobilePhone' size='25' maxlength='20' /></div>";

$str .= "\n<div class='system_label'>$websiteText[47]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='fax' value='$fax' size='25' maxlength='20' /></div>";

$securityCode = $preferenceUtils->getValue("SHOP_SECURITY_CODE");
if ($securityCode) {
  $randomSecurityCode = LibUtils::generateUniqueId();
  LibSession::putSessionValue(UTILS_SESSION_RANDOM_SECURITY_CODE, $randomSecurityCode);
  $url = $gUtilsUrl . "/printNumberImage.php?securityCodeFontSize=$securityCodeFontSize";
  $label = $userUtils->getTipPopup($websiteText[23], $websiteText[24], 300, 200);

  $str .= "\n<div class='system_label'>$label</div>";
  $str .= "\n<div class='system_field'><input class='system_input' type='text' name='securityCode' size='5' maxlength='5' value='' /> <img src='$url' title='$websiteText[22]' alt='' /></div>";
}

$str .= "\n<div class='system_label'>$websiteText[36]</div>";
$str .= "\n<div class='system_field'></div>";

$str .= "\n<div class='system_label'>$websiteText[30] *</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='invoiceAddress1' value='$invoiceAddress1' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_field'><input class='system_input' type='text' name='invoiceAddress2' value='$invoiceAddress2' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[32] *</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='invoiceZipCode' value='$invoiceZipCode' size='25' maxlength='10' /></div>";

$str .= "\n<div class='system_label'>$websiteText[33] *</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='invoiceCity' value='$invoiceCity' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[34]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='invoiceState' value='$invoiceState' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[35] *</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='invoiceCountry' value='$invoiceCountry' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[26]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='invoicePostalBox' value='$invoicePostalBox' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_comment'>$websiteText[29]</div>";

if ($withShippingAddress) {
  $strCheckedWithShippingAddress = "checked='checked'";
} else {
  $strCheckedWithShippingAddress = '';
}

$label = $userUtils->getTipPopup($websiteText[37], $websiteText[46], 300, 200);
$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'><input type='checkbox' name='withShippingAddress' $strCheckedWithShippingAddress value='1' /></div>";

$str .= "\n<div class='system_label'>$websiteText[30]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='shippingAddress1' value='$shippingAddress1' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_field'><input class='system_input' type='text' name='shippingAddress2' value='$shippingAddress2' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[32]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='shippingZipCode' value='$shippingZipCode' size='25' maxlength='10' /></div>";

$str .= "\n<div class='system_label'>$websiteText[33]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='shippingCity' value='$shippingCity' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[34]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='shippingState' value='$shippingState' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[35]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='shippingCountry' value='$shippingCountry' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[26]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='shippingPostalBox' value='$shippingPostalBox' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_comment'>$websiteText[43]</div>";

$str .= "\n<div class='system_label'>$websiteText[7]</div>";
$str .= "\n<div class='system_field'><textarea class='system_input' name='message' cols='23' rows='5'>$message</textarea></div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['checkout'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[25]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
