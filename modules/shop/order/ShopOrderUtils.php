<?

class ShopOrderUtils extends ShopOrderDB {

  var $websiteText;

  // The property name holding the message page for the bank details
  var $propertyComputerBankDetailsPage;
  var $propertyPhoneBankDetailsPage;

  var $languageUtils;
  var $preferenceUtils;
  var $addressUtils;
  var $clockUtils;
  var $propertyUtils;
  var $shopItemUtils;
  var $shopOrderItemUtils;

  function __construct() {
    parent::__construct();

    $this->init();
  }

  function init() {
    $this->propertyComputerBankDetailsPage = "SHOP_COMPUTER_BANK_DETAILS_PAGE_";
    $this->propertyPhoneBankDetailsPage = "SHOP_PHONE_BANK_DETAILS_PAGE_";
  }

  function loadLanguageTexts() {
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  // Get the number of items in an order
  function getNbItems($shopOrderId) {
    $orderNbItems = 0;

    if ($shopOrderItems = $this->shopOrderItemUtils->selectByShopOrderId($shopOrderId)) {
      foreach ($shopOrderItems as $shopOrderItem) {
        $quantity = $shopOrderItem->getQuantity();
        $orderNbItems = $orderNbItems + $quantity;
      }
    }

    return($orderNbItems);
  }

  // Get the total amount for the items of an order
  // including all different fees
  function getTotalToPay($shopOrder) {
    $totalToPay = 0;

    $shopOrderId = $shopOrder->getId();
    $handlingFee = $shopOrder->getHandlingFee();
    $discountAmount = $shopOrder->getDiscountAmount();

    if ($shopOrderItems = $this->shopOrderItemUtils->selectByShopOrderId($shopOrderId)) {
      foreach ($shopOrderItems as $shopOrderItem) {
        $price = $shopOrderItem->getPrice();
        $quantity = $shopOrderItem->getQuantity();
        $shippingFee = $shopOrderItem->getShippingFee();
        $vatRate = $shopOrderItem->getVatRate();

        if ($vatRate > 0) {
          $VAT = round($price * $vatRate / 100, 2);
          $priceInclVAT = $price + $VAT;
        } else {
          $VAT = 0;
          $priceInclVAT = $price;
        }

        $totalToPay = $totalToPay + ($priceInclVAT + $shippingFee) * $quantity;
      }
    }

    if ($discountAmount) {
      $totalToPay = $totalToPay - $discountAmount;
    }

    $totalToPay = $totalToPay + $handlingFee;

    return($totalToPay);
  }

  // Delete an order
  function deleteOrder($shopOrderId) {
    // Delete the items of the order
    if ($shopOrderItems = $this->shopOrderItemUtils->selectByShopOrderId($shopOrderId)) {
      foreach ($shopOrderItems as $shopOrderItem) {
        $shopOrderItemId = $shopOrderItem->getId();
        $this->shopOrderItemUtils->delete($shopOrderItemId);
      }
    }

    if ($shopOrder = $this->selectById($shopOrderId)) {
      $invoiceAddressId = $shopOrder->getInvoiceAddressId();
      $shippingAddressId = $shopOrder->getShippingAddressId();

      $this->delete($shopOrderId);

      $this->addressUtils->delete($invoiceAddressId);
      if ($shippingAddressId) {
        $this->addressUtils->delete($shippingAddressId);
      }
    }
  }

  // Delete the cancelled orders
  function deleteCancelledOrders() {
    $shopOrders = $this->selectByStatus(SHOP_ORDER_STATUS_CANCELLED);
    foreach ($shopOrders as $shopOrder) {
      $shopOrderId = $shopOrder->getId();
      $this->deleteOrder($shopOrderId);
    }
  }

  // Cancel the old pending orders
  // A pending order is an order that has not yet been paid for by the customer
  function cancelPendingOrders() {
    $sinceDate = $this->clockUtils->getSystemDate();

    $duration = $this->preferenceUtils->getValue("SHOP_PENDING_DURATION");
    if ($duration) {
      $sinceDate = $this->clockUtils->incrementDays($sinceDate, -1 * $duration);
    }

    $shopOrders = $this->selectByStatus(SHOP_ORDER_STATUS_PENDING);
    foreach ($shopOrders as $shopOrder) {
      $shopOrderId = $shopOrder->getId();
      $orderDate = $shopOrder->getOrderDate();
      $orderDate = substr($orderDate, 0, 10);
      if ($this->clockUtils->systemDateIsSet($orderDate) && $this->clockUtils->systemDateIsGreater($sinceDate, $orderDate)) {
        $shopOrder->setStatus(SHOP_ORDER_STATUS_CANCELLED);
        $this->update($shopOrder);
      }
    }
  }

  // Render a random password
  function createRandomPassword() {
    $password = LibUtils::generateUniqueId(SHOP_NEW_PASSWORD_LENGTH);

    return($password);
  }

  // Get the message page for the bank details
  function getBankDetailsPage($languageCode = '') {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $page = $this->getPhoneBankDetailsPage($languageCode);
    } else {
      $page = $this->getComputerBankDetailsPage($languageCode);
    }

    return($page);
  }

  // Get the message page for the bank details
  function getComputerBankDetailsPage($languageCode = '') {
    $page = '';

    if (!$languageCode) {
      $languageCode = $this->languageUtils->getCurrentLanguageCode();
    }

    if ($languageCode) {
      $page = $this->propertyUtils->retrieve($this->propertyComputerBankDetailsPage . $languageCode);
    }

    return($page);
  }

  // Set the message page for the bank details
  function setComputerBankDetailsPage($languageCode, $page) {
    if ($languageCode) {
      $this->propertyUtils->store($this->propertyComputerBankDetailsPage . $languageCode, $page);
    }
  }

  // Get the message page for the bank details
  function getPhoneBankDetailsPage($languageCode = '') {
    $page = '';

    if (!$languageCode) {
      $languageCode = $this->languageUtils->getCurrentLanguageCode();
    }

    if ($languageCode) {
      $page = $this->propertyUtils->retrieve($this->propertyPhoneBankDetailsPage . $languageCode);
    }

    return($page);
  }

  // Set the message page for the bank details
  function setPhoneBankDetailsPage($languageCode, $page) {
    if ($languageCode) {
      $this->propertyUtils->store($this->propertyPhoneBankDetailsPage . $languageCode, $page);
    }
  }

  function renderOrderContent($shopOrderId) {
    global $gShopUrl;
    global $gPhotoUrl;
    global $gJSNoStatus;

    $this->loadLanguageTexts();

    $str = '';

    $orderStatuses = $this->shopItemUtils->getOrderStatuses();

    if ($shopOrder = $this->selectById($shopOrderId)) {
      $firstname = $shopOrder->getFirstname();
      $lastname = $shopOrder->getLastname();
      $organisation = $shopOrder->getOrganisation();
      $email = $shopOrder->getEmail();
      $telephone = $shopOrder->getTelephone();
      $mobilePhone = $shopOrder->getMobilePhone();
      $fax = $shopOrder->getFax();
      $message = $shopOrder->getMessage();
      $invoiceAddressId = $shopOrder->getInvoiceAddressId();
      $shippingAddressId = $shopOrder->getShippingAddressId();
      $status = $shopOrder->getStatus();
      $statusName = $orderStatuses[$status];

      // Render the address of an order
      if ($invoiceAddress = $this->addressUtils->selectById($invoiceAddressId)) {
        $invoiceAddress1 = $invoiceAddress->getAddress1();
        $invoiceAddress2 = $invoiceAddress->getAddress2();
        $invoiceZipCode = $invoiceAddress->getZipCode();
        $invoiceCity = $invoiceAddress->getCity();
        $invoiceState = $invoiceAddress->getState();
        $invoiceCountry = $invoiceAddress->getCountry();

        $shippingAddress1 = '';
        $shippingAddress2 = '';
        $shippingZipCode = '';
        $shippingCity = '';
        $shippingState = '';
        $shippingCountry = '';

        if ($shippingAddressId) {
          if ($shippingAddress = $this->addressUtils->selectById($shippingAddressId)) {
            $shippingAddress1 = $shippingAddress->getAddress1();
            $shippingAddress2 = $shippingAddress->getAddress2();
            $shippingZipCode = $shippingAddress->getZipCode();
            $shippingCity = $shippingAddress->getCity();
            $shippingState = $shippingAddress->getState();
            $shippingCountry = $shippingAddress->getCountry();
          }
        }
      }

      $str .= "\n<div class='system'>";

      $str .= "\n<div class='system_form'>";

      $str .= "\n<div class='system_comment'>" . $this->websiteText[30] . "</div>";

      $str .= "<div class='system_label'>" . $this->websiteText[29] . "</div>"
        . "<div class='system_field'>" . $statusName . "</div>";

      $str .= "\n<div class='system_label'>" . $this->websiteText[10] . "</div>"
        . "<div class='system_field'>" . $firstname . "</div>";

      $str .= "\n<div class='system_label'>" . $this->websiteText[11] . "</div>"
        . "<div class='system_field'>" . $lastname . "</div>";

      if ($organisation) {
        $str .= "\n<div class='system_label'>" . $this->websiteText[12] . "</div>"
          . "<div class='system_field'>" . $organisation . "</div>";
      }

      $str .= "\n<div class='system_label'>" . $this->websiteText[13] . "</div>"
        . "<div class='system_field'>" . $email . "</div>";

      if ($telephone) {
        $str .= "\n<div class='system_label'>" . $this->websiteText[14] . "</div>"
          . "<div class='system_field'>" . $telephone . "</div>";
      }

      if ($mobilePhone) {
        $str .= "\n<div class='system_label'>" . $this->websiteText[15] . "</div>"
          . "<div class='system_field'>" . $mobilePhone . "</div>";
      }

      if ($fax) {
        $str .= "\n<div class='system_label'>" . $this->websiteText[25] . "</div>"
          . "<div class='system_field'>" . $fax . "</div>";
      }

      $str .= "\n<div class='system_comment'><p /></div>";

      $str .= "\n<div class='system_comment'>" . $this->websiteText[16] . "</div>";

      $str .= "\n<div class='system_label'>" . $this->websiteText[17] . "</div>"
        . "<div class='system_field'>" . $invoiceAddress1 . "</div>";

      if ($invoiceAddress2) {
        $str .= "\n<div class='system_field'>" . $invoiceAddress2 . "</div>";
      }

      $str .= "\n<div class='system_label'>" . $this->websiteText[19] . "</div>"
        ."<div class='system_field'>" . $invoiceZipCode . "</div>";

      $str .= "\n<div class='system_label'>" . $this->websiteText[20] . "</div>"
        ."<div class='system_field'>" . $invoiceCity . "</div>";

      if ($invoiceState) {
        $str .= "\n<div class='system_label'>" . $this->websiteText[21] . "</div>"
          ."<div class='system_field'>" . $invoiceState . "</div>";
      }

      $str .= "\n<div class='system_label'>" . $this->websiteText[22] . "</div>"
        ."<div class='system_field'>" . $invoiceCountry . "</div>";

      if ($shippingAddress1) {
        $str .= "\n<div class='system_label'><p /></div>";

        $str .= "\n<div class='system_comment'>" . $this->websiteText[23] . "</div>";

        $str .= "\n<div class='system_label'>" . $this->websiteText[17] . "</div>"
          . "<div class='system_field'>" . $shippingAddress1 . "</div>";

        if ($shippingAddress2) {
          $str .= "\n<div class='system_field'>" . $shippingAddress2 . "</div>";
        }

        $str .= "\n<div class='system_label'>" . $this->websiteText[19] . "</div>"
          ."<div class='system_field'>" . $shippingZipCode . "</div>";

        $str .= "\n<div class='system_label'>" . $this->websiteText[20] . "</div>"
          ."<div class='system_field'>" . $shippingCity . "</div>";

        if ($shippingState) {
          $str .= "\n<div class='system_label'>" . $this->websiteText[21] . "</div>"
            ."<div class='system_field'>" . $shippingState . "</div>";
        }

        $str .= "\n<div class='system_label'>" . $this->websiteText[22] . "</div>"
          ."<div class='system_field'>" . $shippingCountry . "</div>";
      }

      // Render the items of an order
      $str .= "\n<div class='system_label'><p /></div>";

      $str .= "\n<div class='system_comment'>" . $this->websiteText[31] . "</div>";

      $totalGeneral = 0;
      $totalQuantity = 0;
      $totalPrice = 0;
      $totalFee = 0;

      $handlingFee = $shopOrder->getHandlingFee();
      $currency = $shopOrder->getCurrency();
      if ($shopOrderItems = $this->shopOrderItemUtils->selectByShopOrderId($shopOrderId)) {
        foreach ($shopOrderItems as $shopOrderItem) {
          $shopItemId = $shopOrderItem->getShopItemId();
          $imageUrl = $shopOrderItem->getImageUrl();
          $name = $shopOrderItem->getName();
          $reference = $shopOrderItem->getReference();
          $price = $shopOrderItem->getPrice();
          $shippingFee = $shopOrderItem->getShippingFee();
          $quantity = $shopOrderItem->getQuantity();
          $isGift = $shopOrderItem->getIsGift();
          if ($isGift) {
            $strIsGift = $this->websiteText[8];
          } else {
            $strIsGift = '';
          }

          $totalItemPrice = $quantity * $price;
          $totalQuantity = $totalQuantity + $quantity;
          $totalPrice = $totalPrice + $totalItemPrice;
          $totalFee = $totalFee + ($shippingFee * $quantity);

          if ($shopItemId) {
            $strLabel = "<a href='$gShopUrl/display.php?shopItemId=$shopItemId' $gJSNoStatus title='"
              . $this->websiteText[3] . "'>" . $name . ' ' . $this->websiteText[4] . ' ' . $reference . "</a>";
          } else if ($imageUrl) {
            $strLabel = "<a href='$gHomeUrl/$imageUrl' $gJSNoStatus title='"
              . $this->websiteText[3] . "'>" . $name . ' ' . $this->websiteText[4] . ' ' . $reference . "</a>";
          }

          $price = $this->shopItemUtils->decimalFormat($price);
          $totalItemPrice = $this->shopItemUtils->decimalFormat($totalItemPrice);

          $str .= "\n<div class='system_label'>" . $strLabel . ' : ' . "</div> "
            . "<div class='system_field'>" . $totalItemPrice . ' ' .  $currency . ' (' . $quantity . ' * ' .
            $price . ' ' .  $currency . ') ' . $strIsGift . "</div>";
        }

        $totalFee = $totalFee + $handlingFee;
        $totalGeneral = $totalPrice + $totalFee;
      }

      $str .= "\n<div class='system_label'><p /></div>";

      $str .= "\n<div class='system_comment'>" . $this->websiteText[32] . "</div>";

      $totalPrice = $this->shopItemUtils->decimalFormat($totalPrice);
      $totalFee = $this->shopItemUtils->decimalFormat($totalFee);
      $totalGeneral = $this->shopItemUtils->decimalFormat($totalGeneral);

      $str .= "\n<div class='system_label'>" . $totalQuantity . ' ' . $this->websiteText[5] . " </div>"
        ."<div class='system_field'>" . $totalPrice . ' ' . $currency . "</div>";

      $str .= "\n<div class='system_label'>" . $this->websiteText[7] . "</div> "
        ."<div class='system_field'>" . $totalFee . ' ' . $currency . "</div>";

      $str .= "\n<div class='system_label'>" . $this->websiteText[6] . "</div> "
        ."<div class='system_field'>" . $totalGeneral . ' ' . $currency . "</div>";

      $str .= "\n<div class='system_label'><p /></div>";

      $str .= "\n</div>";

      $str .= "\n</div>";
    }

    return($str);
  }

  // Render an order
  function render($shopOrderId) {
    $str = '';

    $str .= $this->renderPdfInvoiceLink($shopOrderId);

    $str .= $this->renderOrderContent($shopOrderId);

    return($str);
  }

  // Render the address of an order
  function renderPdfInvoiceLink($shopOrderId) {
    global $gImagesUserUrl;
    global $gShopUrl;

    $this->loadLanguageTexts();

    $str = "\n<div class='system'>";

    $str .= "\n<a href='$gShopUrl/order/pdf_invoice.php?shopOrderId=$shopOrderId'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_ORDER_PDF
      . "' class='no_style_image_icon' title='" .  $this->websiteText[9]
      . " 'alt='' /> " . $this->websiteText[9] . "</a>";

    $str .= "\n</div>";

    return($str);
  }

  // Render the list of orders of a user
  function renderList($userId) {
    global $gShopUrl;
    global $gImagesUserUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $str = '';

    $str .= "\n<div class='shop_orders'>";

    $str .= "\n<div class='shop_orders_title'>" . $this->websiteText[26] . "</div>";

    $str .= "\n<div class='shop_orders_instructions'>" . $this->websiteText[27] . "</div>";

    $orderStatuses = $this->shopItemUtils->getOrderStatuses();

    $shopOrders = $this->selectByUserId($userId);

    foreach ($shopOrders as $shopOrder) {
      $shopOrderId = $shopOrder->getId();
      $orderDate = $shopOrder->getOrderDate();
      $status = $shopOrder->getStatus();
      $statusName = $orderStatuses[$status];

      $strDisplay = "<a href='$gShopUrl/order/display.php?shopOrderId=$shopOrderId'>" . "<img
        src='$gImagesUserUrl/" . IMAGE_SHOP_ORDER_VIEW . "' class='no_style_image_icon' title='" .  $this->websiteText[28] . " 'alt='' /></a>";

      $strPdfInvoice = "<a href='$gShopUrl/order/pdf_invoice.php?shopOrderId=$shopOrderId'>" . "<img src='$gImagesUserUrl/" . IMAGE_SHOP_ORDER_PDF . "' class='no_style_image_icon' title='" .  $this->websiteText[9] . " 'alt='' /></a>";

      $str .= "\n<div class='shop_orders_id'>$shopOrderId</div>";
      $str .= "\n<div class='shop_orders_date_time'>$orderDate</div>";
      $str .= "\n<div class='shop_orders_status'>$statusName</div>";
      $str .= "\n<div class='shop_orders_icon'>$strDisplay $strPdfInvoice</div>";
    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForList() {
    $str = "<div class='shop_orders'>The shop orders"
      . "<div class='shop_orders_title'>The title of the page</div>"
      . "<div class='shop_orders_instructions'>Some instructions</div>"
      . "<div class='shop_orders_id'>The order id</div>"
      . "<div class='shop_orders_date_time'>The order date</div>"
      . "<div class='shop_orders_status'>The order status</div>"
      . "<div class='shop_orders_icon'>Some icons</div>"
      . "</div>";

    return($str);
  }

}

?>
