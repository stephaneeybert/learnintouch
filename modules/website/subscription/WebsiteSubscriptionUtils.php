<?

class WebsiteSubscriptionUtils extends WebsiteSubscriptionDB {

  var $mlText;

  var $languageUtils;
  var $clockUtils;
  var $websiteUtils;
  var $websiteAddressUtils;
  var $addressUtils;
  var $shopItemUtils;
  var $shopOrderUtils;
  var $shopOrderItemUtils;

  function WebsiteSubscriptionUtils() {
    $this->WebsiteSubscriptionDB();
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Check if the last subscription has expired
  function hasExpired($websiteId) {
    if ($websiteSubscriptions = $this->selectByWebsiteId($websiteId)) {
      if (count($websiteSubscriptions) > 0) {
        // Get the last subscription
        $websiteSubscription = $websiteSubscriptions[count($websiteSubscriptions) - 1];
        $openingDate = $websiteSubscription->getOpeningDate();
        $duration = $websiteSubscription->getDuration();
        $terminationDate = $websiteSubscription->getTerminationDate();

        if ($this->clockUtils->systemDateIsSet($terminationDate)) {
          $closingDate = $terminationDate;
        } else {
          $closingDate = $this->clockUtils->incrementMonths($openingDate, $duration);
        }

        // Check if the last month of the subscription has passed
        $systemDate = $this->clockUtils->getSystemDate();
        if ($this->clockUtils->systemDateIsGreaterOrEqual($systemDate, $closingDate)) {
          return(true);
        }
      }
    }

    return(false);
  }

  function createOrder($websiteSubscriptionId) {
    $this->loadLanguageTexts();

    $shopOrderId = '';

    if ($websiteSubscription = $this->selectById($websiteSubscriptionId)) {
      $openingDate = $websiteSubscription->getOpeningDate();
      $terminationDate = $websiteSubscription->getTerminationDate();
      $duration = $websiteSubscription->getDuration();
      $fee = $websiteSubscription->getFee();
      $websiteId = $websiteSubscription->getWebsiteId();

      if (!$this->clockUtils->systemDateIsSet($terminationDate)) {
        $closingDate = $this->clockUtils->incrementMonths($openingDate, $duration);
      } else {
        $closingDate = $terminationDate;
      }
      $localOpeningDate = $this->clockUtils->systemToLocalNumericDate($openingDate);
      $localClosingDate = $this->clockUtils->systemToLocalNumericDate($closingDate);

      if ($website = $this->websiteUtils->selectById($websiteId)) {
        $websiteName = $website->getName();
        $websiteDomainName = $website->getDomainName();
        $firstname = $website->getFirstname();
        $lastname = $website->getLastname();
        $email = $website->getEmail();
        $diskSpace = $website->getDiskSpace();
        $package = $website->getPackage();

        if ($websiteAddress = $this->websiteAddressUtils->selectByWebsite($websiteId)) {
          $address1 = $websiteAddress->getAddress1();
          $address2 = $websiteAddress->getAddress2();
          $zipCode = $websiteAddress->getZipCode();
          $city = $websiteAddress->getCity();
          $state = $websiteAddress->getState();
          $country = $websiteAddress->getCountry();
          $telephone = $websiteAddress->getTelephone();
          $mobile = $websiteAddress->getMobile();
          $fax = $websiteAddress->getFax();
          $vatNumber = $websiteAddress->getVatNumber();
          $postalBox = $websiteAddress->getPostalBox();

          $invoiceAddress = new Address();
          $invoiceAddress->setAddress1($address1);
          $invoiceAddress->setAddress2($address2);
          $invoiceAddress->setZipCode($zipCode);
          $invoiceAddress->setCity($city);
          $invoiceAddress->setState($state);
          $invoiceAddress->setCountry($country);
          $invoiceAddress->setPostalBox($postalBox);
          $this->addressUtils->insert($invoiceAddress);
          $invoiceAddressId = $this->addressUtils->getLastInsertId();

          if ($invoiceAddressId) {
            // The currency for the order amounts
            $currency = $this->shopItemUtils->getCurrency();

            // Save the order
            $shopOrder = new ShopOrder();
            $shopOrder->setFirstname($firstname);
            $shopOrder->setLastname($lastname);
            $shopOrder->setEmail($email);
            $shopOrder->setOrganisation($websiteName);
            $shopOrder->setVatNumber($vatNumber);
            $shopOrder->setTelephone($telephone);
            $shopOrder->setMobilePhone($mobile);
            $shopOrder->setFax($fax);
            $shopOrder->setCurrency($currency);
            $shopOrder->setInvoiceAddressId($invoiceAddressId);
            $systemDate = $this->clockUtils->getSystemDate();
            $shopOrder->setOrderDate($systemDate);
            $dueDate = $this->clockUtils->incrementMonths($systemDate, 3);
            $shopOrder->setDueDate($dueDate);
            $shopOrder->setStatus(SHOP_ORDER_STATUS_PENDING);
            $shopOrder->setPaymentType(SHOP_ORDER_PAYMENT_BANK);
            $this->shopOrderUtils->insert($shopOrder);
            $shopOrderId = $this->shopOrderUtils->getLastInsertId();

            if ($shopOrderId) {
              $shopOrderItem = new ShopOrderItem();
              $shopOrderItem->setName($websiteName);
              $shortDescription = $this->mlText[0] . ' ' . $localOpeningDate . ' ' . $this->mlText[1] . ' ' . $localClosingDate . ' ' . $this->mlText[2] . ' ' . $fee . ' ' . $currency . ' ' . $this->mlText[3] . ' ' . $duration . ' ' . $this->mlText[4];
              $shopOrderItem->setShortDescription($shortDescription);
              $price = $fee * $duration;
              $shopOrderItem->setPrice($price);
              $shopOrderItem->setQuantity(1);
              $shopOrderItem->setShopOrderId($shopOrderId);
              $this->shopOrderItemUtils->insert($shopOrderItem);
              $shopOrderItemId = $this->shopOrderItemUtils->getLastInsertId();
            }
          }
        }
      }
    }

    return($shopOrderId);
  }

  // Renew the subscription
  function renew($websiteId) {
    if ($websiteSubscriptions = $this->selectByWebsiteId($websiteId)) {
      if (count($websiteSubscriptions) > 0) {
        // Get the last subscription
        $websiteSubscription = $websiteSubscriptions[count($websiteSubscriptions) - 1];
        $openingDate = $websiteSubscription->getOpeningDate();
        $fee = $websiteSubscription->getFee();
        $duration = $websiteSubscription->getDuration();
        $terminationDate = $websiteSubscription->getTerminationDate();
        $autoRenewal = $websiteSubscription->getAutoRenewal();

        // Check if it should be renewed
        if ($autoRenewal) {
          // Check if the last subscription has passed
          if ($this->hasExpired($websiteId)) {
            $openingDate = $this->clockUtils->incrementMonths($openingDate, $duration);

            // Create a subscription for the new period
            $websiteSubscription = new WebsiteSubscription();
            $websiteSubscription->setOpeningDate($openingDate);
            $websiteSubscription->setFee($fee);
            $websiteSubscription->setDuration($duration);
            $websiteSubscription->setAutoRenewal($autoRenewal);
            $websiteSubscription->setWebsiteId($websiteId);
            $this->insert($websiteSubscription);
            $websiteSubscriptionId = $this->getLastInsertId();

            // Create an order for the new period
            $this->createOrder($websiteId);
          }
        }
      }
    }
  }

}

?>
