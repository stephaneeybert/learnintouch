<?

class ShopItemUtils extends ShopItemDB {

  var $mlText;
  var $websiteText;

  var $currentCategoryId;
  var $shopItemId;

  // The items of the current selection
  var $currentSelection;

  // The name of the cookie file holding the selection of items
  var $selectionCookieName;

  // Duration of the selection cookie, expressed in seconds
  var $selectionCookieDuration;

  // The content of the cart
  var $cartContent;

  // Duration of the cart cookie, expressed in seconds
  var $cartCookieDuration;

  // The default number of first images
  // displayed for each ad in the list
  var $nbFirstImages;

  // The default currency
  var $defaultCurrency;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $popupUtils;
  var $clockUtils;
  var $photoUtils;
  var $colorboxUtils;
  var $shopCategoryUtils;
  var $photoFormatUtils;
  var $shopItemImageUtils;
  var $shopOrderItemUtils;
  var $shopDiscountUtils;

  function ShopItemUtils() {
    $this->ShopItemDB();

    $this->init();
  }

  function init() {
    $this->currentCategoryId = "shopCurrentCategoryId";
    $this->shopItemId = "shopItemId";
    $this->currentSelection = "shopCurrentSelection";
    $this->selectionCookieName = "shopSelection";
    $this->selectionCookieDuration = 60 * 60 * 24 * 90;
    $this->cartContent = "shopCartContent";
    $this->cartCookieDuration = 60 * 60 * 24 * 90;
    $this->orderCookieDuration = 60 * 15;
    $this->nbFirstImages = 1;
    $this->defaultCurrency = 'EUR';
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function getOrderStatuses() {
    $this->loadLanguageTexts();

    $orderStatuses = array(
      '-1' => '',
      SHOP_ORDER_STATUS_PENDING => $this->mlText[120],
      SHOP_ORDER_STATUS_INVOICED => $this->mlText[73],
      SHOP_ORDER_STATUS_PAID => $this->mlText[121],
      SHOP_ORDER_STATUS_SHIPPED => $this->mlText[122],
      SHOP_ORDER_STATUS_CANCELLED => $this->mlText[123],
      SHOP_ORDER_STATUS_REFUND => $this->mlText[124],
    );

    return($orderStatuses);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $currencies = $this->getCurrencies();

    $this->preferences = array(
      "SHOP_DISPLAY_ALL" =>
      array($this->mlText[88], $this->mlText[89], PREFERENCE_TYPE_BOOLEAN, true),
      "SHOP_SLIDESHOW_SPEED" =>
      array($this->mlText[151], $this->mlText[152], PREFERENCE_TYPE_RANGE, array(1, 60, 5)),
      "SHOP_NO_SLIDESHOW" =>
      array($this->mlText[153], $this->mlText[154], PREFERENCE_TYPE_BOOLEAN, ''),
      "SHOP_NO_ZOOM" =>
      array($this->mlText[149], $this->mlText[150], PREFERENCE_TYPE_BOOLEAN, true),
        "SHOP_CURRENCY" =>
        array($this->mlText[36], $this->mlText[37], PREFERENCE_TYPE_SELECT, $currencies),
          "SHOP_ADD_CURRENCY" =>
          array($this->mlText[83], $this->mlText[84], PREFERENCE_TYPE_SELECT, $currencies),
            "SHOP_CURRENCY_RATE" =>
            array($this->mlText[85], $this->mlText[86], PREFERENCE_TYPE_TEXT, ''),
            "SHOP_VAT_RATE" =>
            array($this->mlText[50], $this->mlText[51], PREFERENCE_TYPE_SELECT, array(9 => "9", 20 => "20", 22 => "22")),
              "SHOP_DECIMAL_SEPARATOR" =>
              array($this->mlText[21], $this->mlText[24], PREFERENCE_TYPE_SELECT, array('' => '', '.' => '.', ',' => ',')),
                "SHOP_SHIPPING_FEE" =>
                array($this->mlText[44], $this->mlText[45], PREFERENCE_TYPE_TEXT, ''),
                  "SHOP_HANDLING_FEE" =>
                  array($this->mlText[96], $this->mlText[97], PREFERENCE_TYPE_TEXT, ''),
                    "SHOP_HIDE_SHIPPING_FEE" =>
                    array($this->mlText[22], $this->mlText[23], PREFERENCE_TYPE_BOOLEAN, ''),
                      "SHOP_HIDE_HANDLING_FEE" =>
                      array($this->mlText[58], $this->mlText[60], PREFERENCE_TYPE_BOOLEAN, ''),
                        "SHOP_HIDE_GIFT_WRAP" =>
                        array($this->mlText[15], $this->mlText[16], PREFERENCE_TYPE_BOOLEAN, ''),
                          "SHOP_BANK_PAYPAL" =>
                          array($this->mlText[62], $this->mlText[63], PREFERENCE_TYPE_BOOLEAN, ''),
                            "SHOP_BANK_TRANSFERT" =>
                            array($this->mlText[127], $this->mlText[128], PREFERENCE_TYPE_BOOLEAN, ''),
                              "SHOP_MAIL_ON_POST" =>
                              array($this->mlText[64], $this->mlText[65], PREFERENCE_TYPE_BOOLEAN, ''),
                                "SHOP_SECURITY_CODE" =>
                                array($this->mlText[78], $this->mlText[79], PREFERENCE_TYPE_BOOLEAN, ''),
                                  "SHOP_REGISTER_USER" =>
                                  array($this->mlText[125], $this->mlText[126], PREFERENCE_TYPE_BOOLEAN, ''),
                                    "SHOP_PENDING_DURATION" =>
                                    array($this->mlText[142], $this->mlText[143], PREFERENCE_TYPE_RANGE, array(0, 90, 0)),
                                      "SHOP_LIST_STEP" =>
                                      array($this->mlText[46], $this->mlText[47], PREFERENCE_TYPE_SELECT, array(10 => "10", 20 => "20", 50 => "50", 100 => "100")),
                                        "SHOP_DEFAULT_NB_FIRST_IMAGES" =>
                                        array($this->mlText[91], $this->mlText[92], PREFERENCE_TYPE_RANGE, array(1, 10, 3)),
                                          "SHOP_HIDE_CANCEL_BUTTON" =>
                                          array($this->mlText[116], $this->mlText[117], PREFERENCE_TYPE_BOOLEAN, ''),
                                            "SHOP_HIDE_SELECTION" =>
                                            array($this->mlText[144], $this->mlText[145], PREFERENCE_TYPE_BOOLEAN, ''),
                                              "SHOP_HIDE_CATEGORY" =>
                                              array($this->mlText[48], $this->mlText[49], PREFERENCE_TYPE_BOOLEAN, ''),
                                                "SHOP_HIDE_PRICE" =>
                                                array($this->mlText[41], $this->mlText[82], PREFERENCE_TYPE_BOOLEAN, ''),
                                                  "SHOP_SEARCH_HIDE_PERIOD" =>
                                                  array($this->mlText[66], $this->mlText[67], PREFERENCE_TYPE_BOOLEAN, ''),
                                                    "SHOP_SEARCH_HIDE_TEXT" =>
                                                    array($this->mlText[68], $this->mlText[69], PREFERENCE_TYPE_BOOLEAN, ''),
                                                      "SHOP_SEARCH_HIDE_REFERENCE" =>
                                                      array($this->mlText[70], $this->mlText[71], PREFERENCE_TYPE_BOOLEAN, ''),
                                                        "SHOP_PAYMENT_COMPLETE" =>
                                                        array($this->mlText[72], $this->mlText[75], PREFERENCE_TYPE_MLTEXT, ''),
                                                          "SHOP_PAYMENT_INCOMPLETE" =>
                                                          array($this->mlText[80], $this->mlText[81], PREFERENCE_TYPE_MLTEXT, ''),
                                                            "SHOP_COMMENT" =>
                                                            array($this->mlText[76], $this->mlText[77], PREFERENCE_TYPE_MLTEXT, ''),
                                                              "SHOP_INVOICE_LEGAL_NOTICE" =>
                                                              array($this->mlText[11], $this->mlText[12], PREFERENCE_TYPE_MLTEXT, ''),
                                                                "SHOP_IMAGE_LENGTH_AXIS" =>
                                                                array($this->mlText[137], $this->mlText[138], PREFERENCE_TYPE_SELECT, array('IMAGE_LENGTH_IS_HEIGHT' => $this->mlText[139], 'IMAGE_LENGTH_IS_WIDTH' => $this->mlText[140])),
                                                                  "SHOP_DEFAULT_MINI_WIDTH" =>
                                                                  array($this->mlText[30], $this->mlText[31], PREFERENCE_TYPE_TEXT, 100),
                                                                    "SHOP_PHONE_DEFAULT_MINI_WIDTH" =>
                                                                    array($this->mlText[90], $this->mlText[93], PREFERENCE_TYPE_TEXT, 70),
                                                                          "SHOP_DEFAULT_LARGE_WIDTH" =>
                                                                          array($this->mlText[34], $this->mlText[35], PREFERENCE_TYPE_TEXT, 400),
                                                                        "SHOP_PHONE_DEFAULT_LARGE_WIDTH" =>
                                                                        array($this->mlText[94], $this->mlText[95], PREFERENCE_TYPE_TEXT, 140),
                                                                          );

    $this->preferenceUtils->init($this->preferences);
  }

  // Get the width of an image
  function getImageWidth() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("SHOP_PHONE_DEFAULT_LARGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("SHOP_DEFAULT_LARGE_WIDTH");
    }

    return($width);
  }

  // Get the next available list order
  function getNextListOrder($categoryId) {
    $listOrder = 1;
    if ($objects = $this->selectByCategoryId($categoryId)) {
      $total = count($objects);
      if ($total > 0) {
        $object = $objects[$total - 1];
        $listOrder = $object->getListOrder() + 1;
      }
    }

    return($listOrder);
  }

  // Swap the curent object with the next one
  function swapWithNext($id) {
    $this->repairListOrder($id);

    $currentObject = $this->selectById($id);
    $currentListOrder = $currentObject->getListOrder();

    // Get the next object and its list order
    if (!$nextObject = $this->selectNext($id)) {
      return;
    }
    $nextListOrder = $nextObject->getListOrder();

    // Update the list orders
    $currentObject->setListOrder($nextListOrder);
    $this->update($currentObject);
    $nextObject->setListOrder($currentListOrder);
    $this->update($nextObject);
  }

  // Swap the curent object with the previous one
  function swapWithPrevious($id) {
    $this->repairListOrder($id);

    $currentObject = $this->selectById($id);
    $currentListOrder = $currentObject->getListOrder();

    // Get the previous object and its list order
    if (!$previousObject = $this->selectPrevious($id)) {
      return;
    }
    $previousListOrder = $previousObject->getListOrder();

    // Update the list orders
    $currentObject->setListOrder($previousListOrder);
    $this->update($currentObject);
    $previousObject->setListOrder($currentListOrder);
    $this->update($previousObject);
  }

  // Repair the order if some order numbers are identical
  // If, by accident, some objects have the same list order
  // (it shouldn't happen) then assign a new list order to each of them
  function repairListOrder($id) {
    if ($shopItem = $this->selectById($id)) {
      $listOrder = $shopItem->getListOrder();
      $categoryId = $shopItem->getCategoryId();
      if ($shopItems = $this->selectByListOrder($categoryId, $listOrder)) {
        if (($listOrder == 0) || (count($shopItems)) > 1) {
          $this->resetListOrder($categoryId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($shopItem = $this->selectById($id)) {
      $listOrder = $shopItem->getListOrder();
      $categoryId = $shopItem->getCategoryId();
      if ($shopItem = $this->selectByNextListOrder($categoryId, $listOrder)) {
        return($shopItem);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($shopItem = $this->selectById($id)) {
      $listOrder = $shopItem->getListOrder();
      $categoryId = $shopItem->getCategoryId();
      if ($shopItem = $this->selectByPreviousListOrder($categoryId, $listOrder)) {
        return($shopItem);
      }
    }
  }

  // Check if the length of the images is considered to be a height
  function imageLengthIsHeight() {
    if ($this->imageLengthIsWidth()) {
      return(false);
    }

    return(true);
  }

  // Check if the length of the images is considered to be a width
  function imageLengthIsWidth() {
    $imageLengthAxis = $this->preferenceUtils->getValue("SHOP_IMAGE_LENGTH_AXIS");

    if ($imageLengthAxis == 'IMAGE_LENGTH_IS_WIDTH') {
      return(true);
    }

    return(false);
  }

  // Check if the selection is to be hidden
  function hideSelection() {
    $hide = $this->preferenceUtils->getValue("SHOP_HIDE_SELECTION");

    return($hide);
  }

  // Get the VAT rate
  function getVatRate() {
    $vatRate = $this->preferenceUtils->getValue("SHOP_VAT_RATE");

    return($vatRate);
  }

  // Get the decimal separator
  function getDecimalSeparator() {
    $separator = $this->preferenceUtils->getValue("SHOP_DECIMAL_SEPARATOR");

    if (!$separator) {
      $separator = '.';
    }

    return($separator);
  }

  // Format an amount with the decimal separator
  function decimalFormat($amount) {
    $decimalSeparator = $this->getDecimalSeparator();

    if ($amount > 0) {
      $amount = number_format($amount, 2, $decimalSeparator, '');
    }

    return($amount);
  }

  // Get the list of currencies
  function getCurrencies() {
    $currencies = array('' => '', "EUR" => "EUR", "FR" => "FR", "GBP" => "GBP", "USD" => "USD", "CAD" => "CAD", "CHF" => "CHF", "SEK" => "SEK", "NOK" => "NOK", "DKK" => "DKK", "FIM" => "FIM", "ITL" => "ITL", "JPY" => "JPY");

    return($currencies);
  }

  // Get the symbol of a currency
  function getCurrencySymbol($code) {
    $symbols = array("EUR" => "&#8364;", "FR" => "&#8355;", "GBP" => "&pound;", "USD" => "US$", "CAD" => "CA$", "CHF" => "Fr", "SEK" => "Sk", "NOK" => "Nk", "DKK" => "Dk", "ITL" => "&#8356;", "JPY" => "&yen;");

    if (array_key_exists($code, $symbols)) {
      $symbol = $symbols[$code];
    } else {
      $symbol = $code;
    }

    return($symbol);
  }

  // Get the content of the selection
  function getSelection() {
    $selection = LibSession::getSessionValue(SHOP_SESSION_SELECTION);
    if (!$selection || $selection == SHOP_SELECTION_EMPTY) {
      $selection = LibCookie::getCookie(SHOP_SESSION_SELECTION);
    }

    $selectionItems = explode(SHOP_SELECTION_SEPARATOR, $selection);

    $listItems = array();

    foreach ($selectionItems as $selectionItem) {
      if (strstr($selectionItem, SHOP_SELECTION_ITEM_SEPARATOR)) {
        list($itemType, $itemId) = explode(SHOP_SELECTION_ITEM_SEPARATOR, $selectionItem);

        if ($itemId) {
          array_push($listItems, array($itemType, $itemId));
        }
      }
    }

    return($listItems);
  }

  // Set the content of the selection
  function setSelection($listItems) {
    $selection = '';
    foreach ($listItems as $item) {
      $itemType = $item[0];
      $itemId = $item[1];
      $selection .= $itemType . SHOP_SELECTION_ITEM_SEPARATOR . $itemId
        . SHOP_SELECTION_SEPARATOR;
    }

    // Avoid an empty value as it bugs down the cookie
    if (!$selection) {
      $selection = SHOP_SELECTION_EMPTY;
    }

    LibCookie::putCookie($this->selectionCookieName, $selection, $this->selectionCookieDuration);
    LibSession::putSessionValue(SHOP_SESSION_SELECTION, $selection);
  }

  // Add an item to the selection
  function addToSelection($itemType, $itemId) {
    $items = $this->getSelection();

    $foundInSelection = false;
    for ($i = 0; $i < count($items); $i++) {
      $wItemType = $items[$i][0];
      $wItemId = $items[$i][1];
      if ($wItemType == $itemType && $wItemId == $itemId) {
        $foundInSelection = true;
      }
    }

    if (!$foundInSelection) {
      array_push($items, array($itemType, $itemId));
    }

    $this->setSelection($items);
  }

  // Delete an item
  function deleteShopItem($shopItemId) {
    if ($shopItemImages = $this->shopItemImageUtils->selectByShopItemId($shopItemId)) {
      foreach ($shopItemImages as $shopItemImage) {
        $shopItemImageId = $shopItemImage->getId();
        $this->shopItemImageUtils->delete($shopItemImageId);
      }
    }

    if ($shopOrderItems = $this->shopOrderItemUtils->selectByShopItemId($shopItemId)) {
      foreach ($shopOrderItems as $shopOrderItem) {
        $shopOrderItem->setShopItemId('');
        $this->shopOrderItemUtils->update($shopOrderItem);
      }
    }

    $this->delete($shopItemId);
  }

  // Delete an item from the selection
  function deleteFromSelection($itemType, $itemId) {
    $items = $this->getSelection();

    for ($i = 0; $i < count($items); $i++) {
      $wItemType = $items[$i][0];
      $wItemId = $items[$i][1];
      if ($wItemType == $itemType && $wItemId == $itemId) {
        unset($items[$i]);
      }
    }

    $this->setSelection($items);
  }

  // Add an item to the cart
  function addToCart($itemType, $itemId) {
    $quantity = $this->getCartItemQuantity($itemType, $itemId);

    $this->updateCart($itemType, $itemId, $quantity + 1, 0, 0);
  }

  // Get the handling fee
  function getHandlingFee() {
    $handlingFee = $this->preferenceUtils->getValue("SHOP_HANDLING_FEE");

    $handlingFee = str_replace(',', '.', $handlingFee);

    return($handlingFee);
  }

  // Get the shipping fee
  function getShippingFee() {
    $shippingFee = $this->preferenceUtils->getValue("SHOP_SHIPPING_FEE");

    return($shippingFee);
  }

  // Get the currency
  function getCurrency() {
    $currency = $this->preferenceUtils->getValue("SHOP_CURRENCY");
    if (!$currency) {
      $currency = $this->defaultCurrency;
    }

    return($currency);
  }

  // Get the quantity of an item in the cart
  function getCartItemQuantity($itemType, $itemId) {
    $quantity = 0;

    $items = $this->getCart();

    for ($i = 0; $i < count($items); $i++) {
      $wItemType = $items[$i][0];
      $wItemId = $items[$i][1];
      if ($wItemType == $itemType && $wItemId == $itemId) {
        if (isset($items[$i][2])) {
          $quantity = $items[$i][2];
        }
      }
    }

    return($quantity);
  }

  // Get the gift wrap of an item in the cart
  function getCartItemIsGift($itemType, $itemId) {
    $isGift = 0;

    $items = $this->getCart();

    for ($i = 0; $i < count($items); $i++) {
      $wItemType = $items[$i][0];
      $wItemId = $items[$i][1];
      if ($wItemType == $itemType && $wItemId == $itemId) {
        if (isset($items[$i][3])) {
          $isGift = $items[$i][3];
        }
      }
    }

    return($isGift);
  }

  // Get the options of an item in the cart
  function getCartItemOptions($itemType, $itemId) {
    $options = '';

    $items = $this->getCart();

    for ($i = 0; $i < count($items); $i++) {
      $wItemType = $items[$i][0];
      $wItemId = $items[$i][1];
      if ($wItemType == $itemType && $wItemId == $itemId) {
        if (isset($items[$i][4])) {
          $options = $items[$i][4];
        }
      }
    }

    return($options);
  }

  // Get the content of the shopping cart
  function getCart() {
    $cart = LibSession::getSessionValue(SHOP_SESSION_CART);
    if (!$cart) {
      $cart = LibCookie::getCookie(SHOP_SESSION_CART);
    }

    $cartItems = explode(SHOP_CART_SEPARATOR, $cart);

    $listItems = array();

    foreach ($cartItems as $cartItem) {
      if (strstr($cartItem, SHOP_CART_ITEM_SEPARATOR)) {
        list($itemType, $itemId, $quantity, $isGift, $options) = explode(SHOP_CART_ITEM_SEPARATOR, $cartItem);

        if ($itemId) {
          if (!$quantity) {
            $quantity = 1;
          }

          array_push($listItems, array($itemType, $itemId, $quantity, $isGift, $options));
        }
      }
    }

    return($listItems);
  }

  // Set the content of the shopping cart
  function setCart($listItems) {
    $cart = '';
    foreach ($listItems as $item) {
      $itemType = $item[0];
      $itemId = $item[1];
      $quantity = $item[2];
      $isGift = $item[3];
      $options = $item[4];
      $cart .= $itemType . SHOP_CART_ITEM_SEPARATOR . $itemId
        . SHOP_CART_ITEM_SEPARATOR . $quantity
        . SHOP_CART_ITEM_SEPARATOR . $isGift
        . SHOP_CART_ITEM_SEPARATOR . $options
        . SHOP_CART_SEPARATOR;
    }

    // Avoid an empty value as it bugs down the cookie
    if (!$cart) {
      $cart = SHOP_CART_EMPTY;
    }

    LibCookie::putCookie(SHOP_SESSION_CART, $cart, $this->cartCookieDuration);
    LibSession::putSessionValue(SHOP_SESSION_CART, $cart);
  }

  // Get the discount
  function getDiscount() {
    $strDiscount = LibSession::getSessionValue(SHOP_SESSION_DISCOUNT);
    if (!$strDiscount) {
      $strDiscount = LibCookie::getCookie(SHOP_SESSION_DISCOUNT);
    }

    $discount = explode(SHOP_CART_SEPARATOR, $strDiscount);

    return($discount);
  }

  // Set the discount
  function setDiscount($discount) {
    if (is_array($discount)) {
      $strDiscount = join(SHOP_CART_SEPARATOR, $discount);

      LibCookie::putCookie(SHOP_SESSION_DISCOUNT, $strDiscount, $this->cartCookieDuration);
      LibSession::putSessionValue(SHOP_SESSION_DISCOUNT, $strDiscount);
    } else {
      LibCookie::deleteCookie(SHOP_SESSION_DISCOUNT);
      LibSession::delSessionValue(SHOP_SESSION_DISCOUNT);
    }
  }

  // Update the cart
  function updateCart($itemType, $itemId, $quantity, $isGift, $options) {
    $items = $this->getCart(true);

    $foundInCart = false;
    for ($i = 0; $i < count($items); $i++) {
      $wItemType = $items[$i][0];
      $wItemId = $items[$i][1];
      if ($wItemType == $itemType && $wItemId == $itemId) {
        if ($quantity > 0) {
          $foundInCart = true;
          $items[$i][2] = $quantity;
        } else {
          unset($items[$i]);
        }
      }
    }

    if (!$foundInCart && $quantity > 0) {
      array_push($items, array($itemType, $itemId, $quantity, $isGift, $options));
    }

    $this->setCart($items);
  }

  // Delete an item from the cart
  function deleteFromCart($itemType, $itemId) {
    $this->updateCart($itemType, $itemId, 0, 0, 0);
  }

  // Empty the cart
  function emptyCart() {
    $cartItems = array();

    $this->setCart($cartItems);
  }

  // Get the list of photo formats
  function getPhotoFormats() {
    $listFormats = array();

    $photoFormats = $this->photoFormatUtils->selectAll();
    foreach ($photoFormats as $photoFormat) {
      $photoFormatId = $photoFormat->getId();
      $name = $photoFormat->getName();
      $listFormats[$photoFormatId] = $name;
    }

    return($listFormats);
  }

  // Get the photo aspect
  function getPhotoAspect() {
    $this->loadLanguageTexts();

    $aspect = array();

    $aspect[SHOP_CART_PHOTO_MATTE] = $this->websiteText[18];
    $aspect[SHOP_CART_PHOTO_SHINY] = $this->websiteText[19];

    return($aspect);
  }

  // Render the options in a text form
  function renderOptions($options) {
    $this->loadLanguageTexts();

    $strOptions = '';

    if (strstr($options, SHOP_CART_OPTION_SEPARATOR)) {
      list($photoFormatId, $frame, $aspect) = explode(SHOP_CART_OPTION_SEPARATOR, $options);

      $listFormats = $this->getPhotoFormats();
      if (count($listFormats) > 0 && array_key_exists($photoFormatId, $listFormats)) {
        $strOptions .= $listFormats[$photoFormatId];
      }

      if ($frame) {
        $strOptions .= ' - ' . $this->websiteText[9];
      }

      $listAspect = $this->getPhotoAspect();
      if (count($listAspect) > 0 && array_key_exists($aspect, $listAspect)) {
        $strAspect = $listAspect[$aspect];
        if ($strAspect) {
          $strOptions .= ' - ' . $strAspect;
        }
      }
    }

    return($strOptions);
  }

  // Render a selection of items
  function renderSelection() {
    global $gShopUrl;
    global $gJSNoStatus;
    global $gImagesUserUrl;

    $this->loadLanguageTexts();

    $str = '';

    // Get the selection items
    $items = $this->getSelection();

    $strViewCart = "\n<a href='$gShopUrl/item/displayCart.php' $gJSNoStatus title='"
      .  $this->websiteText[103] . "'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_CART . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    $str .= "\n<div class='shop_item_icon'>"
      . "\n $strViewCart"
      . "\n</div>";

    $str .= "\n<div class='system_title'>" . $this->websiteText[100] . "</div>";

    if (count($items) > 0) {
      $str .= "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>";

      for ($i = 0; $i < count($items); $i++) {
        $itemType = $items[$i][0];
        $itemId = $items[$i][1];

        $strItem = '';
        if ($itemType == SHOP_CART_ITEM) {
          $strItem = $this->render($itemId);
        } else if ($itemType == SHOP_CART_PHOTO) {
          $strItem = $this->photoUtils->renderSmallPhoto($itemId);
        }

        $strDeleteFromSelection = "\n<a href='$gShopUrl/item/selection.php?itemType=$itemType&amp;itemId=$itemId&amp;delete=1' $gJSNoStatus title='" . $this->websiteText[99] . "'>"
          . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_CART_DELETE . "' class='no_style_image_icon' title='' alt='' /></a>";

        $str .= "\n<tr>"
          ."<td>" . $strItem . "</td>"
          ."<td>" . $strDeleteFromSelection . "</td>"
          . "</tr>";
      }

      $str .= "\n</table>";
    } else {
      $str .= "\n<div class='system_comment'>" . $this->websiteText[141] . "</div>";
    }

    return($str);
  }

  // Render a cart of items
  function renderCart() {
    global $gShopUrl;
    global $gJSNoStatus;
    global $gImagesUserUrl;
    global $gPhotoUrl;
    global $gSeparatorUrl;

    $this->loadLanguageTexts();

    $str = "<div class='shop_item_icon'>";

    $str .= "<a href='$gShopUrl/display_list.php' $gJSNoStatus title='' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_SHOP_ITEM_LIST . "' class='no_style_image_icon' title='" .  $this->websiteText[101] . "' alt='' style='vertical-align:middle;' /> " . $this->websiteText[101] . "</a>";

    if (!$this->hideSelection()) {
      $str .= "<br/><a href='$gShopUrl/item/selection.php' $gJSNoStatus title='' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_SHOP_SELECTION . "' class='no_style_image_icon' title='" . $this->websiteText[102] . "' alt='' style='vertical-align:middle;' /> " . $this->websiteText[102] . "</a>";
    }

    $str .= "</div>";

    $str .= "\n<div class='system_title'>" . $this->websiteText[105] . "</div>";

    // Get the cart items
    $items = $this->getCart();

    if (count($items) > 0) {
      // Get the currency
      $currency = $this->getCurrency();
      $currencySymbol = $this->getCurrencySymbol($currency);

      $str .= <<<HEREDOC
<script type='text/javascript'>
function shopCartRecalculate() {
  document.cart.submit();
}
function shopCartCheckout() {
  document.cart.checkout.value = 1;
  document.cart.submit();
}
</script>
HEREDOC;

      $str .= "\n<form name='cart' id='cart' action='$gShopUrl/item/displayCart.php' method='post'>";

      $str .= "\n<table border='0' width='100%' cellpadding='4' cellspacing='4'>";

      $totalItemsPrice = 0;
      $totalItemsPriceInclVAT = 0;
      $nbTotalItems = 0;
      $totalItemFees = 0;

      $hideIsGift = $this->preferenceUtils->getValue("SHOP_HIDE_GIFT_WRAP");
      $hideShippingFee = $this->preferenceUtils->getValue("SHOP_HIDE_SHIPPING_FEE");
      $hideHandlingFee = $this->preferenceUtils->getValue("SHOP_HIDE_HANDLING_FEE");

      $str .= "\n<tr>"
        . "<td>" . $this->websiteText[132] . "</td>"
        . "<td style='text-align:center;'>" . $this->websiteText[134] . "</td>"
        . "<td style='text-align:center;'>" . $this->websiteText[135] . "</td>"
        . "<td style='text-align:center;'>" . $this->websiteText[136] . "</td>"
        . "<td>&nbsp;</td>"
        . "</tr>";

      $straightLine = "<hr style='text-align:center; width:100%; height:1px; border-width:1px 0px 0px 0px;' />";

      $str .= "\n<tr>" . "<td colspan='5'>" . $straightLine . "</td>" . "</tr>";

      for ($i = 0; $i < count($items); $i++) {
        $itemType = $items[$i][0];
        $itemId = $items[$i][1];
        $quantity = $items[$i][2];
        $isGift = $items[$i][3];
        $options = $items[$i][4];

        $strDeleteFromCart = "\n<a href='$gShopUrl/item/displayCart.php?itemType=$itemType&amp;itemId=$itemId&amp;quantity=0' $gJSNoStatus title='" . $this->websiteText[106] . "'>" . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_CART_DELETE . "' class='no_style_image_icon' title='' alt='' /></a>";

        $strUpdateQuantity = "<input type='text' style='text-align:center;' name='quantity_$itemType$itemId' value='$quantity' size='2' maxlength='2' onchange='shopCartRecalculate(); return false;' />";

        if (!$hideIsGift) {
          if ($isGift) {
            $strCheckedIsGift = "checked='checked'";
          } else {
            $strCheckedIsGift = '';
          }

          $strIsGift = "<input type='checkbox' name='isGift_$itemType$itemId' $strCheckedIsGift value='1' /> " . $this->websiteText[17];
        } else {
          $strIsGift = '';
        }

        // Retrieve the item or remove it from the cart
        $name = '';
        $reference = '';
        $price = '';
        $shippingFee = '';
        $VAT = '';
        $priceInclVAT = '';
        if ($itemType == SHOP_CART_ITEM) {
          $strOptions = '';

          if ($shopItem = $this->selectById($itemId)) {
            $name = $shopItem->getName();
            $reference = $shopItem->getReference();
            $price = $shopItem->getPrice();
            $vatRate = $shopItem->getVatRate();
            if (!$vatRate) {
              $vatRate = $this->getVatRate();
            }
            $priceInclVAT = $price;
            if ($vatRate > 0) {
              $VAT = round($price * $vatRate / 100, 2);
              $priceInclVAT = $price + $VAT;
            }
            $shippingFee = $shopItem->getShippingFee();
          }

          // Get the first image
          $strImage = $this->renderSmallImages($itemId, 1);

          $strLabel = "<a href='$gShopUrl/display.php?shopItemId=$itemId' $gJSNoStatus style='text-decoration:none;' title='" . $this->websiteText[56] . "'>" . $name;
          if ($reference) {
            $strLabel .= '<br/>' . $this->websiteText[59]. ' ' . $reference;
          }
          $strLabel .= "</a>";
        } else if ($itemType == SHOP_CART_PHOTO) {
          $photoFormatId = '';
          $frame = '';
          $aspect = '';
          if ($options) {
            if (strstr($options, SHOP_CART_OPTION_SEPARATOR)) {
              list($photoFormatId, $frame, $aspect) = explode(SHOP_CART_OPTION_SEPARATOR, $options);
            }
          }

          if ($photo = $this->photoUtils->selectById($itemId)) {
            $name = $photo->getName();
            $reference = $photo->getReference();
            $price = $this->photoUtils->getPhotoPrice($itemId, $photoFormatId);
            $shippingFee = $this->getShippingFee();
          }

          // Get the first image
          $strImage = $this->photoUtils->renderSmallImage($itemId);

          $strLabel = "<a href='$gPhotoUrl/display_photo.php?photoId=$itemId' $gJSNoStatus style='text-decoration:none;' title='" . $this->websiteText[56] . "'>" . $name;
          if ($reference) {
            $strLabel .= '<br/>' . $this->websiteText[59] . $reference;
          }
          $strLabel .= "</a>";

          if ($frame) {
            $strCheckedFrame = "checked='checked'";
          } else {
            $strCheckedFrame = '';
          }

          $strFrame = "<input type='checkbox' name='frame_$itemType$itemId' $strCheckedFrame value='1' /> " . $this->websiteText[9];

          $listFormats = $this->getPhotoFormats();
          $strFormat = $this->websiteText[14] . ' ' . LibHtml::getSelectList("photoFormatId_$itemType$itemId", $listFormats, $photoFormatId);

          $listAspect = $this->getPhotoAspect();
          $strAspect = $this->websiteText[20] . ' ' . LibHtml::getSelectList("aspect_$itemType$itemId", $listAspect, $aspect);

          $strOptions = $strFormat
            . $straightLine
            . $strAspect
            . $straightLine
            . $strFrame;
        }

        if ($name || $reference) {
          $itemTotalPrice = $quantity * $price;
          $itemTotalPriceInclVAT = $quantity * $priceInclVAT;
          $totalItemsPrice = $totalItemsPrice + $itemTotalPrice;
          $totalItemsPriceInclVAT = $totalItemsPriceInclVAT + $itemTotalPriceInclVAT;
          $nbTotalItems = $nbTotalItems + $quantity;

          $strFees = '';
          if (!$hideShippingFee) {
            if (!$shippingFee) {
              $shippingFee = $this->getShippingFee();
            }
            $itemFees = $quantity * $shippingFee;
            $totalItemFees = $totalItemFees + $itemFees;

            if ($shippingFee > 0) {
              $shippingFee = $this->decimalFormat($shippingFee);
              $itemFees = $this->decimalFormat($itemFees);

              $strFees = $shippingFee . ' ' . $currencySymbol . ' * ' . $quantity . ' = ' . $itemFees . ' ' . $currencySymbol . ' ' . $this->websiteText[25];
            }
          }

          $price = $this->decimalFormat($price);
          $priceInclVAT = $this->decimalFormat($priceInclVAT);
          $itemTotalPrice = $this->decimalFormat($itemTotalPrice);
          $itemTotalPriceInclVAT = $this->decimalFormat($itemTotalPriceInclVAT);

          $str .= "\n<tr>"
            . "<td style='vertical-align:middle;'>"
            . $strLabel
            . "</td>"
            . "<td style='text-align:right; vertical-align:middle; white-space:nowrap;'>"
            . $priceInclVAT . ' ' . $currencySymbol . "</td>"
            . "<td style='text-align:center; vertical-align:middle;'>" . $strUpdateQuantity . "</td>"
            . "<td style='text-align:right; vertical-align:middle; white-space:nowrap;'>"
            . $itemTotalPriceInclVAT . ' ' . $currencySymbol . "</td>"
            . "<td style='vertical-align:middle;'>" . $strDeleteFromCart . "</td>"
            . "</tr>";

          $str .= "\n<tr>"
            . "<td>"
            . $strIsGift
            . "</td>"
            . "<td colspan='4' style='white-space:nowrap;'>"
            . $strFees
            . "</td>"
            . "</tr>";

          $str .= "\n<tr>" . "<td colspan='5'>" . $straightLine . "</td>" . "</tr>";

        } else if ($itemId) {
          $this->deleteFromCart($itemType, $itemId);
        }
      }

      $totalPrice = $totalItemsPriceInclVAT;

      $discountCode = LibEnv::getEnvHttpPOST("discountCode");

      $discountAmount = 0;
      if ($discountCode) {
        if ($shopDiscount = $this->shopDiscountUtils->selectByDiscountCode($discountCode)) {
          $discountRate = $shopDiscount->getDiscountRate();
          $discountAmount = $totalPrice * $discountRate / 100;
          $discountAmount = $this->decimalFormat($discountAmount);
          $totalPrice = $totalPrice - $discountAmount;
        }
      }
      $this->setDiscount(array($discountCode, $discountAmount));

      $totalToPay = $totalPrice;

      $strDiscountCode = "<input type='text' name='discountCode' value='$discountCode' size='12' maxlength='12' onchange='shopCartRecalculate(); return false;' />";

      $strDiscountButton = "<a href='#' onclick=\"shopCartRecalculate(); return false;\" style='text-decoration:none;' title='" . $this->websiteText[147] . "' style='vertical-align:middle;'>" . $this->websiteText[146] . '</a> ' . $strDiscountCode . " <a href='#' onclick=\"shopCartRecalculate(); return false;\" style='text-decoration:none;' title='" . $this->websiteText[147] . "' style='vertical-align:middle;'> <img border='0' src='$gImagesUserUrl/" . IMAGE_SHOP_CART_UPDATE . "' style='vertical-align:middle;'>" . "</a>";

      $str .= "\n<tr><td colspan='5'><div style='white-space:nowrap; text-align:right; vertical-align:middle;'>" . $strDiscountButton . "</div></td></tr>";

      $strUpdateButton = "<a href='#' onclick=\"shopCartRecalculate(); return false;\" style='text-decoration:none;' title='" . $this->websiteText[107] . "' style='vertical-align:middle;'>" . $this->websiteText[114] . " <img border='0' src='$gImagesUserUrl/" . IMAGE_SHOP_CART_UPDATE . "' style='vertical-align:middle;'>" . "</a>";

      $str .= "\n<tr><td colspan='5'><div style='white-space:nowrap; text-align:right; vertical-align:middle;'>" . $strUpdateButton . "</div></td></tr>";

      $totalItemsPrice = $this->decimalFormat($totalItemsPrice);
      $totalItemsPriceInclVAT = $this->decimalFormat($totalItemsPriceInclVAT);

      $str .= "\n<tr>"
        . "<td>" . $nbTotalItems . ' ' . $this->websiteText[112] . "</td>"
        . "<td colspan='2' style='white-space:nowrap; text-align:right; vertical-align:top;'>" . $this->websiteText[113] . "</td>"
        . "<td style='white-space:nowrap; text-align:right; vertical-align:top;'>" . $totalItemsPriceInclVAT . ' ' . $currencySymbol . "</td>"
        . "<td>&nbsp;</td>"
        . "</tr>";

      if ($discountAmount) {
        $str .= "\n<tr>"
          . "<td colspan='3' style='white-space:nowrap; text-align:right; vertical-align:top;'>" . $this->websiteText[148] . "</td>"
          . "<td style='white-space:nowrap; text-align:right; vertical-align:top;'>" . $discountAmount . ' ' . $currencySymbol . "</td>"
          . "<td>&nbsp;</td>"
          . "</tr>";

      }

      $totalToPay += $totalItemFees;

      if (!$hideShippingFee) {
        $totalItemFees = $this->decimalFormat($totalItemFees);

        $str .= "\n<tr>"
          . "<td>&nbsp;</td>"
          . "<td colspan='2' style='white-space:nowrap; text-align:right;'>" . $this->websiteText[118] . "</td>"
          . "<td style='white-space:nowrap; text-align:right;'>" . $totalItemFees . ' ' . $currencySymbol . "</td>"
          . "<td>&nbsp;</td>"
          . "</tr>";
      }

      $handlingFee = $this->getHandlingFee();
      if (!$hideHandlingFee && $handlingFee > 0) {
        $totalToPay += $handlingFee;
        $handlingFee = $this->decimalFormat($handlingFee);

        $str .= "\n<tr>"
          . "<td>&nbsp;</td>"
          . "<td colspan='2' style='white-space:nowrap; text-align:right;'>" . $this->websiteText[130] . "</td>"
          . "<td style='white-space:nowrap; text-align:right;'>" . $handlingFee . ' ' . $currencySymbol . "</td>"
          . "<td>&nbsp;</td>"
          . "</tr>";
      } else {
        $handlingFee = 0;
      }

      $totalToPay = $this->decimalFormat($totalToPay);
      $str .= "\n<tr>"
        . "<td>&nbsp;</td>"
        . "<td colspan='2' style='white-space:nowrap; text-align:right;'>" . $this->websiteText[119] . "</td>"
        . "<td style='white-space:nowrap; text-align:right;'>" . $totalToPay . ' ' . $currencySymbol . "</td>"
        . "<td>&nbsp;</td>"
        . "</tr>";

      $strCheckoutLink = "<a href='#' onclick='javascript:shopCartCheckout(); return false;' style='text-decoration:none;' title='" . $this->websiteText[108] . "' style='vertical-align:middle;'>" . $this->websiteText[108] . " <img border='0' src='$gImagesUserUrl/" . IMAGE_SHOP_CART_CHECKOUT . "' style='vertical-align:middle;'>" . "</a>";

      $str .= "\n<tr><td colspan='5'><div style='white-space:nowrap; text-align:right; vertical-align:middle;'>" . $strCheckoutLink . "</div></td></tr>";

      $str .= "\n</table>";

      $str .= "<input type='hidden' name='updateCart' value='1' />";
      $str .= "<input type='hidden' name='checkout' value='0' />";

      $str .= "\n</form>";

      $str .= "<input type='hidden' name='updateCart' value='1' />";
    } else {
      $str .= "\n<div class='system_comment'>" . $this->websiteText[115] . "</div>";
    }

    return($str);
  }

  // Render the first small images of an item
  function renderSmallImages($shopItemId, $nbFirstImages = '') {
    global $gUtilsUrl;
    global $gShopUrl;
    global $gJSNoStatus;
    global $gIsPhoneClient;

    // Get the first images
    $images = array();

    if (!$nbFirstImages) {
      $nbFirstImages = $this->preferenceUtils->getValue("SHOP_DEFAULT_NB_FIRST_IMAGES");
      if (!$nbFirstImages) {
        $nbFirstImages = $this->nbFirstImages;
      }
    }

    if ($shopItemImages = $this->shopItemImageUtils->selectByShopItemId($shopItemId)) {
      foreach ($shopItemImages as $i => $shopItemImage) {
        if ($i < $nbFirstImages) {
          $images[$i] = $shopItemImage->getImage();
        }
      }
    }

    $imageFilePath = $this->shopItemImageUtils->imageFilePath;
    $imageFileUrl = $this->shopItemImageUtils->imageFileUrl;

    $str = "\n<div class='shop_item_images'>";
    foreach ($images as $image) {
      if ($image && file_exists($imageFilePath . $image)) {
        if (!LibImage::isGif($image)) {
          $filename = $imageFilePath . $image;

          if ($gIsPhoneClient) {
            $width = $this->preferenceUtils->getValue("SHOP_PHONE_DEFAULT_MINI_WIDTH");
          } else {
            $width = $this->preferenceUtils->getValue("SHOP_DEFAULT_MINI_WIDTH");
          }

          $imageLengthIsHeight = $this->imageLengthIsHeight();
          if ($imageLengthIsHeight) {
            $width = LibImage::getWidthFromHeight($filename, $width);
          }

          $filename = urlencode($filename);

          $imageUrl = $gUtilsUrl . "/printImage.php?filename=" . $filename . "&amp;width=$width&amp;height=";
        } else {
          $imageUrl = "$imageFileUrl/$image";
        }

        if ($gIsPhoneClient || $this->preferenceUtils->getValue("SHOP_NO_SLIDESHOW")) {
          $str .= " <a href='$gShopUrl/display.php?shopItemId=$shopItemId' $gJSNoStatus>"
            . "<span class='shop_item_image'><img class='shop_item_image_file' src='$imageUrl' class='no_style_image_icon' title='" . $this->websiteText[56] . "' alt='' /></span></a>";
        } else {
          $str .= " <a href='$imageFileUrl/$image' rel='no_style_colorbox' $gJSNoStatus>"
            . "<span class='shop_item_image'><img class='shop_item_image_file' src='$imageUrl' class='no_style_image_icon' title='" . $this->websiteText[56] . "' alt='' /></span></a>";
        }
      }
    }
    $str .= "\n</div>";

    return($str);
  }

  // Render an item
  function render($shopItemId) {
    global $gUtilsUrl;
    global $gShopUrl;
    global $gJSNoStatus;

    if (!$shopItem = $this->selectById($shopItemId)) {
      return;
    }

    $this->loadLanguageTexts();

    $name = $shopItem->getName();
    $shortDescription = $shopItem->getShortDescription();

    // Get the first images
    $strImages = $this->renderSmallImages($shopItemId);

    if ($name) {
      $strName = "<a href='$gShopUrl/display.php?shopItemId=$shopItemId' $gJSNoStatus title='"
        . $this->websiteText[56] . "'>" . $name . "</a><br />";
    } else if (!$image) {
      $strName = "<a href='$gShopUrl/display.php?shopItemId=$shopItemId' $gJSNoStatus title='"
        . $this->websiteText[56] . "'>" . $this->websiteText[57] . "</a><br />";
    } else {
      $strName = '';
    }

    $str = "\n<div class='shop_item'>"
      . "<div class='shop_item_name'>$strName</div>"
      . "<div class='shop_item_short_description'> $shortDescription</div>"
      . "<div>$strImages</div>"
      . "</div>";

    return($str);
  }

  // Render the item properties
  function renderProperties($shopItem) {
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $name = $shopItem->getName();
    $longDescription = $shopItem->getLongDescription();
    $reference = $shopItem->getReference();
    $weight = $shopItem->getWeight();
    $price = $shopItem->getPrice();
    $vatRate = $shopItem->getVatRate();
    if (!$vatRate) {
      $vatRate = $this->getVatRate();
    }
    $currency = $this->getCurrency();
    $currencySymbol = $this->getCurrencySymbol($currency);
    $additionalCurrency = $this->preferenceUtils->getValue("SHOP_ADD_CURRENCY");
    $currencyRate = $this->preferenceUtils->getValue("SHOP_CURRENCY_RATE");
    $priceConverted = round(($price * $currencyRate), 0);

    $price = $this->decimalFormat($price);
    $priceConverted = $this->decimalFormat($priceConverted);

    $strPrice = "$price $currencySymbol";

    $VAT = '';
    $priceInclVAT = $price;
    if ($vatRate > 0) {
      $VAT = round($price * $vatRate / 100, 2);
      $priceInclVAT = $price + $VAT;
    }

    if ($additionalCurrency && $currencyRate > 0) {
      $strPrice .= " ($priceConverted $additionalCurrency)";
    }

    if (!$gIsPhoneClient) {
      $separator = "\n</td><td>";
    } else {
      $separator = '';
    }

    $str = "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>";

    $str .= "\n<tr><td valign='top'><div class='shop_item_label'>"
      . $this->websiteText[7]
      . "</div>"
      .  $separator
      . "<div class='shop_item_field'>$longDescription</div></td></tr>";

    if ($price) {
      if ($VAT) {
        $str .= "\n<tr><td><div class='shop_item_label'>"
          . $this->websiteText[42]
          . "</div>"
          .  $separator
          . "<div class='shop_item_field'>$strPrice</div></td></tr>";
        $str .= "\n<tr><td><div class='shop_item_label'>"
          . $this->websiteText[40]
          . "</div>"
          .  $separator
          . "<div class='shop_item_field'>$VAT $currencySymbol</div></td></tr>";
        $str .= "\n<tr><td><div class='shop_item_label'>"
          . $this->websiteText[39]
          . "</div>"
          .  $separator
          . "<div class='shop_item_field'>$priceInclVAT $currencySymbol</div></td></tr>";
      } else {
        $str .= "\n<tr><td><div class='shop_item_label'>"
          . $this->websiteText[3]
          . "</div>"
          .  $separator
          . "<div class='shop_item_field'>$strPrice</div></td></tr>";
      }
    }

    if ($reference) {
      $str .= "\n<tr><td><div class='shop_item_label'>"
        . $this->websiteText[2]
        . "</div>"
        .  $separator
        . "<div class='shop_item_field'>$reference</div></td></tr>";
    }

    if ($weight) {
      $str .= "\n<tr><td><div class='shop_item_label'>"
        . $this->websiteText[13]
        . "</div>"
        .  $separator
        . "<div class='shop_item_field'>$weight</div></td></tr>";
    }

    $str .= "\n</table>";

    return($str);
  }

  // Render an item
  function renderItem($shopItemId, $shopItemImageId = '') {
    global $gUtilsUrl;
    global $gShopUrl;
    global $gJSNoStatus;
    global $gImagesUserUrl;
    global $gContactUrl;

    $this->loadLanguageTexts();

    $shopItem = $this->selectById($shopItemId);

    $shopItemId = $shopItem->getId();
    $name = $shopItem->getName();
    $reference = $shopItem->getReference();

    $strImg = $this->renderImage($shopItemId, $shopItemImageId);

    $strPreviousItem = '';
    if ($previousShopItem = $this->selectPrevious($shopItemId)) {
      $previousShopItemId = $previousShopItem->getId();
      if ($previousShopItemId > 0) {
        $strPreviousItem = "\n<a href='$gShopUrl/display.php?shopItemId=$previousShopItemId' $gJSNoStatus title='" . $this->websiteText[5] . "'>"
          . "<img src='$gImagesUserUrl/" . IMAGE_COMMON_LEFT . "' class='no_style_image_icon' title='' alt='' /></a>";
      }
    }

    $strNextItem = '';
    if ($nextShopItem = $this->selectNext($shopItemId)) {
      $nextShopItemId = $nextShopItem->getId();
      if ($nextShopItemId > 0) {
        $strNextItem .= "\n<a href='$gShopUrl/display.php?shopItemId=$nextShopItemId' $gJSNoStatus title='" . $this->websiteText[4] . "'>"
          . "<img src='$gImagesUserUrl/" . IMAGE_COMMON_RIGHT . "' class='no_style_image_icon' title='' alt='' /></a>";
      }
    }

    $strBack = "\n<a href='$gShopUrl/display_list.php' $gJSNoStatus title='"
      . $this->websiteText[1] . "'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_COMMON_UP . "' class='no_style_image_icon' title='' alt='' /></a>";

    // Get the first image if none is specified
    if (!$shopItemImageId) {
      if ($shopItemImages = $this->shopItemImageUtils->selectByShopItemId($shopItemId)) {
        $shopItemImage = $shopItemImages[0];
        $shopItemImageId = $shopItemImage->getId();
      }
    }

    $strPreviousImage = '';
    if ($previousShopItemImage = $this->shopItemImageUtils->selectPrevious($shopItemImageId)) {
      $previousShopItemImageId = $previousShopItemImage->getId();
      if ($previousShopItemImageId > 0) {
        $strPreviousImage = "\n<a href='$gShopUrl/display.php?shopItemId=$shopItemId&amp;shopItemImageId=$previousShopItemImageId' $gJSNoStatus title='" . $this->websiteText[27] . "'>"
          . "<img src='$gImagesUserUrl/" . IMAGE_COMMON_LEFT . "' class='no_style_image_icon' title='' alt='' /></a>";
      }
    }

    $strNextImage = '';
    if ($nextShopItemImage = $this->shopItemImageUtils->selectNext($shopItemImageId)) {
      $nextShopItemImageId = $nextShopItemImage->getId();
      if ($nextShopItemImageId > 0) {
        $strNextImage = "\n<a href='$gShopUrl/display.php?shopItemId=$shopItemId&amp;shopItemImageId=$nextShopItemImageId' $gJSNoStatus title='" . $this->websiteText[26] . "'>"
          . "<img src='$gImagesUserUrl/" . IMAGE_COMMON_RIGHT . "' class='no_style_image_icon' title='' alt='' /></a>";
      }
    }

    $str = '';

    $str .= "\n<div class='shop_item'>";

    $strSearch = "\n<a href='$gShopUrl/search.php' $gJSNoStatus title='"
      .  $this->websiteText[52] .  "'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_SEARCH_ITEM . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    if (!$this->hideSelection()) {
      $strViewSelection = "\n<a href='$gShopUrl/item/selection.php' $gJSNoStatus title='" .  $this->websiteText[102] . "'>"
        . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_SELECTION . "' class='no_style_image_icon' title='' alt='' />" . "</a>";
    } else {
      $strViewSelection = '';
    }

    $strViewCart = "\n<a href='$gShopUrl/item/displayCart.php' $gJSNoStatus title='"
      .  $this->websiteText[103] . "'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_CART . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    $strViewList = "\n<a href='$gShopUrl/display_list.php' $gJSNoStatus title='"
      .  $this->websiteText[101] . "'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_ITEM_LIST . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    $str .= "\n<div class='shop_item_icon'>"
      . "\n $strSearch"
      . "\n $strViewList"
      . "\n $strViewSelection"
      . "\n $strViewCart"
      . "\n</div>";

    $str .= "\n<div class='shop_item_button'>$strBack $strPreviousItem";
    $str .= "\n $strNextItem</div>";
    $str .= "\n<div class='shop_item_name'>$name</div>";
    $str .= "\n<div class='shop_item_image'>$strImg</div>";
    $str .= "\n<div class='shop_item_image_button'>$strPreviousImage";
    $str .= "\n$strNextImage</div>";

    $str .= $this->renderProperties($shopItem);

    $itemType = SHOP_CART_ITEM;
    $str .=  "\n<div style='white-space:nowrap; text-align:right; vertical-align:middle;'><a href='$gShopUrl/item/addToCart.php?itemType=$itemType&amp;itemId=$shopItemId&amp;quantity=1' $gJSNoStatus title='' style='text-decoration:none; vertical-align:middle;'>" . $this->websiteText[104] . " <img src='$gImagesUserUrl/" . IMAGE_SHOP_ADD_TO_CART . "' class='no_style_image_icon' title='" . $this->websiteText[104] . "' alt='' style='vertical-align:middle;' />" . "</a></div>";

    if (!$this->hideSelection()) {
      $str .= "\n<div style='white-space:nowrap; text-align:right; vertical-align:middle;'><a href='$gShopUrl/item/selection.php?itemType=$itemType&amp;itemId=$shopItemId' $gJSNoStatus title='' style='text-decoration:none; vertical-align:middle;'>" . $this->websiteText[98] . " <img src='$gImagesUserUrl/" . IMAGE_SHOP_ADD_TO_SELECTION . "' class='no_style_image_icon' title='" . $this->websiteText[98] . "' alt='' style='vertical-align:middle;' />" . "</a></div>";
    }

    $str .= "\n</div>";

    return($str);
  }

  // Print an item
  function printShopItem($shopItemId, $shopItemImageId = '') {
    if (!$shopItem = $this->selectById($shopItemId)) {
      return;
    }

    $shopItemId = $shopItem->getId();
    $name = $shopItem->getName();
    $longDescription = $shopItem->getLongDescription();
    $nearby = $shopItem->getNearby();
    $reference = $shopItem->getReference();

    $str = '';

    $str = LibJavaScript::getJSLib();
    $str .= "\n<script type='text/javascript'>printPage();</script>";

    $str .= "\n<div class='shop_item'>";

    $str .= "\n<div class='shop_item_name'>$name</div>";

    $strImg = $this->renderImage($shopItemId, $shopItemImageId);

    $str .= "\n<div class='shop_item_image'>$strImg</div>";

    $str .= $this->renderProperties($shopItem);

    $str .= "\n</div>";

    return($str);
  }

  // Render an image
  function renderImage($shopItemId, $shopItemImageId) {
    global $gUtilsUrl;
    global $gShopUrl;
    global $gJSNoStatus;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    // Get the first image if none is specified
    $image = '';
    $description = '';
    if (!$shopItemImageId) {
      if ($shopItemImages = $this->shopItemImageUtils->selectByShopItemId($shopItemId)) {
        $shopItemImage = $shopItemImages[0];
        $shopItemImageId = $shopItemImage->getId();
        $image = $shopItemImage->getImage();
        $description = $shopItemImage->getDescription();
      }
    } else {
      if ($shopItemImage = $this->shopItemImageUtils->selectById($shopItemImageId)) {
        $image = $shopItemImage->getImage();
        $description = $shopItemImage->getDescription();
      }
    }

    $imageFilePath = $this->shopItemImageUtils->imageFilePath;
    $imageFileUrl = $this->shopItemImageUtils->imageFileUrl;

    if ($image && file_exists($imageFilePath . $image)) {
      $imageUrl = "$imageFileUrl/$image";

      if (!$gIsPhoneClient && !$this->preferenceUtils->getValue("SHOP_NO_ZOOM")) {
        $strImg = "<div style='overflow: auto;'><a href='$imageFileUrl/$image' class='zoomable' title='$description'>"
          . "<img class='shop_item_image_file' src='$imageUrl' title='$description' alt='' />"
          . "</a></div>";
      } else {
        $strImg = "<img class='shop_item_image_file' src='$imageUrl' title='' alt='' />";
      }
    } else {
      $strImg = "&nbsp;";
    }

    return($strImg);
  }

  // Render a list of items
  function renderList($shopCategoryId = '') {
    global $gImagesUserUrl;
    global $gShopUrl;
    global $gContactUrl;
    global $gJSNoStatus;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $str = '';

    $displayAll = $this->preferenceUtils->getValue("SHOP_DISPLAY_ALL");

    $str .= "\n<div class='shop_items'>";

    $strSearch = "\n<a href='$gShopUrl/search.php' $gJSNoStatus title='"
      .  $this->websiteText[52] .  "'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_SEARCH_ITEM . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    if (!$this->hideSelection()) {
      $strViewSelection = "\n<a href='$gShopUrl/item/selection.php' $gJSNoStatus title='"
        .  $this->websiteText[102] . "'>"
        . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_SELECTION . "' class='no_style_image_icon' title='' alt='' />" . "</a>";
    } else {
      $strViewSelection = '';
    }

    $strViewCart = "\n<a href='$gShopUrl/item/displayCart.php' $gJSNoStatus title='"
      .  $this->websiteText[103] . "'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_CART . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    $str .= "\n<div class='shop_item_icon'>"
      . "\n $strSearch"
      . "\n $strViewSelection"
      . "\n $strViewCart"
      . "\n</div>";

    $str .= "\n<div class='shop_list_search_link'>"
      . "\n<a href='$gShopUrl/search.php' $gJSNoStatus title='" . $this->websiteText[52] . "'>"
      . $this->websiteText[53] . "</a>"
      . "</div>";

    $str .= "\n<form action='$gShopUrl/display_list.php' method='post'>";

    $hideCategory = $this->preferenceUtils->getValue("SHOP_HIDE_CATEGORY");

    if (!$hideCategory && $this->shopCategoryUtils->countAll() > 1) {
      $categoryList = array('-1' => '');
      if ($categories = $this->shopCategoryUtils->getCategoryNames()) {
        foreach ($categories as $wCategoryId => $wName) {
          $categoryList[$wCategoryId] = $wName;
        }
      }
      $strSelect = LibHtml::getSelectList("shopCategoryId", $categoryList, $shopCategoryId, true);

      $str .= "\n<div class='shop_list_category_selector'>"
        . $this->websiteText[6];
      if ($gIsPhoneClient) {
        $str .= "<br />";
      }
      $str .= ' ' . $strSelect . "</div>";
    }

    $str .= "\n</form>";

    $systemDate = $this->clockUtils->getSystemDateTime();

    $shopItems = array();

    $shopItems = $this->selectByCategoryId($shopCategoryId);

    $nbShopItems = count($shopItems);

    $str .= "\n<div class='shop_list_nb_result'>"
      . "\n$nbShopItems " . $this->websiteText[87]
      . "</div>";

    if (count($shopItems) > 0) {
      if (!$gIsPhoneClient && !$this->preferenceUtils->getValue("SHOP_NO_SLIDESHOW")) {
        $slideshowSpeed = $this->preferenceUtils->getValue("SHOP_SLIDESHOW_SPEED");
        $str .= $this->colorboxUtils->renderJsColorbox() . $this->colorboxUtils->renderWebsiteColorbox($slideshowSpeed);
      }

      foreach ($shopItems as $shopItem) {
        $shopItemId = $shopItem->getId();
        $hide = $shopItem->getHide();
        $available = $shopItem->getAvailable();

        if ($hide) {
          continue;
        }

        if ($this->clockUtils->systemDateIsSet($available) && $this->clockUtils->systemDateIsGreater($available, $systemDate)) {
          continue;
        }

        $str .= $this->render($shopItemId);

        $itemType = SHOP_CART_ITEM;
        $str .=  "\n<span style='vertical-align:middle;'><a href='$gShopUrl/item/addToCart.php?itemType=$itemType&amp;itemId=$shopItemId&amp;quantity=1' $gJSNoStatus title='' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_SHOP_ADD_TO_CART . "' class='no_style_image_icon' title='" . $this->websiteText[104] . "' alt='' style='vertical-align:middle;' />" . "</a></span>";

        if (!$this->hideSelection()) {
          $str .= "\n<span style='vertical-align:middle;'><a href='$gShopUrl/item/selection.php?itemType=$itemType&amp;itemId=$shopItemId' $gJSNoStatus title='' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_SHOP_ADD_TO_SELECTION . "' class='no_style_image_icon' title='" . $this->websiteText[98] . "' alt='' style='vertical-align:middle;' />" . "</a></span>";
        }
      }
    }

    $str .= "\n</div>";

    return($str);
  }

  // Render a list of items
  function renderSearchList($reference, $pattern, $priceMin, $priceMax, $available, $shopCategoryId) {
    global $gImagesUserUrl;
    global $gShopUrl;
    global $gJSNoStatus;

    $this->loadLanguageTexts();

    $str = '';

    $str .= "\n<div class='shop_item_list'>";

    $strSearch = "\n<a href='$gShopUrl/search.php' $gJSNoStatus title='"
      .  $this->websiteText[52] .  "'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_SEARCH_ITEM . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    $strViewList = "\n<a href='$gShopUrl/display_list.php' $gJSNoStatus title='"
      .  $this->websiteText[101] . "'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_ITEM_LIST . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    if (!$this->hideSelection()) {
      $strViewSelection = "\n<a href='$gShopUrl/item/selection.php' $gJSNoStatus title='"
        .  $this->websiteText[102] . "'>"
        . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_SELECTION . "' class='no_style_image_icon' title='' alt='' />" . "</a>";
    } else {
      $strViewSelection = '';
    }

    $strViewCart = "\n<a href='$gShopUrl/item/displayCart.php' $gJSNoStatus title='"
      .  $this->websiteText[103] . "'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_CART . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    $str .= "\n<div class='shop_item_icon'>"
      . "\n $strSearch"
      . "\n $strViewList"
      . "\n $strViewSelection"
      . "\n $strViewCart"
      . "\n</div>";

    $str .= "\n<div class='shop_list_search_button'>"
      . "\n<a href='$gShopUrl/search.php' $gJSNoStatus title='"
      . $this->websiteText[54] . "'>" . $this->websiteText[55] . "</a></div>";

    $systemDate = $this->clockUtils->getSystemDateTime();

    // Get the items of the category
    if ($pattern) {
      $shopItems = $this->selectLikePattern($pattern);
    } else {
      $shopItems = $this->selectByCategoryId($shopCategoryId);
    }

    if (count($shopItems > 0)) {
      $shopItemList = array();

      foreach ($shopItems as $shopItem) {
        if ($shopItem->getHide()) {
          continue;
        }

        if ($this->clockUtils->systemDateIsSet($shopItem->getAvailable()) && $this->clockUtils->systemDateIsGreater($shopItem->getAvailable(), $systemDate)) {
          continue;
        }

        if ($reference && $shopItem->getReference() != $reference) {
          continue;
        }
        if ($priceMin && $shopItem->getPrice() < $priceMin) {
          continue;
        }
        if ($priceMax && $shopItem->getPrice() > $priceMax) {
          continue;
        }

        // Get the date since which to display
        $availableDate = '';
        if (trim($available)) {
          $systemDate = $this->clockUtils->getSystemDate();
          $availableDate = $this->clockUtils->incrementDays($systemDate, -1 * $available);
        }

        if ($this->clockUtils->systemDateIsSet($available) && $this->clockUtils->systemDateIsGreaterOrEqual($available, $shopItem->getAvailable())) {
          continue;
        }

        array_push($shopItemList, $shopItem);
      }

      $nbShopItems = count($shopItemList);

      $str .= "\n<div class='shop_list_nb_result'>"
        . "\n$nbShopItems " . $this->websiteText[87]
        . "</div>";

      foreach ($shopItemList as $shopItem) {
        $shopItemId = $shopItem->getId();
        $str .= $this->render($shopItemId);

        $itemType = SHOP_CART_ITEM;
        $str .=  "\n<span style='vertical-align:middle;'><a href='$gShopUrl/item/addToCart.php?itemType=$itemType&amp;itemId=$shopItemId&amp;quantity=1' $gJSNoStatus title='' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_SHOP_ADD_TO_CART . "' class='no_style_image_icon' title='" . $this->websiteText[104] . "' alt='' style='vertical-align:middle;' />" . "</a></span>";

        if (!$this->hideSelection()) {
          $str .= "\n<span style='vertical-align:middle;'><a href='$gShopUrl/item/selection.php?itemType=$itemType&amp;itemId=$shopItemId' $gJSNoStatus title='' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_SHOP_ADD_TO_SELECTION . "' class='no_style_image_icon' title='" . $this->websiteText[98] . "' alt='' style='vertical-align:middle;' />" . "</a></span>";
        }
      }

    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForList() {
    global $gStylingImage;
    global $gImagesUserUrl;

    $str = "<div class='shop_items'>The shop items"
      . "<div class='shop_item_icon'>Some icons"
      . "<input type='image' src='$gImagesUserUrl/" . IMAGE_SHOP_SEARCH_ITEM . "' class='no_style_image_icon' title='An icon' />"
      . "</div>"
      . "<div class='shop_list_search_link'>The link to the item search page</div>"
      . "<div class='shop_list_category_selector'>Link category: A category</div>"
      . "<div class='shop_list_nb_result'>The number of search results</div>"
      . "<div class='shop_item'>An item"
      . "<div class='shop_item_name'>The name of the item</div>"
      . "<div class='shop_item_short_description'>The short description of the item</div>"
      . "<div class='shop_item_images'>The images of an item "
      . "<div class='shop_item_image'>An image of an item "
      . "<img class='shop_item_image_file' src='$gStylingImage' title='The border of the image of the item' alt='' />"
      . "</div>"
      . "</div>"
      . "</div>"
      . "</div>";

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForItem() {
    global $gStylingImage;

    $str = "<div class='shop_item'>An item"
      . "<div class='shop_item_name'>The name of the item</div>"
      . "<div class='shop_item_short_description'>The short description of the item</div>"
      . "<div class='shop_item_images'>The images of an item "
      . "<div class='shop_item_image'>An image of an item "
      . "<img class='shop_item_image_file' src='$gStylingImage' title='The border of the image of the item' alt='' />"
      . "</div>"
      . "</div>"
      . "<div class='shop_item_label'>The label of an item property</div>"
      . "<div class='shop_item_field'>The value of an item property</div>"
      . "</div>";

    return($str);
  }
}

?>
