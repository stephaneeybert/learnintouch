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
  $email = $shopOrder->getEmail();
  $invoiceAddressId = $shopOrder->getInvoiceAddressId();
  $shippingAddressId = $shopOrder->getShippingAddressId();

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

    $panelUtils->setHeader($mlText[0]);
    $panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), $email);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbc"));
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $invoiceAddress1);
    $panelUtils->addLine();
    if ($invoiceAddress2) {
      $panelUtils->addLine('', $invoiceAddress2);
      $panelUtils->addLine();
      }
    $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $invoiceZipCode);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $invoiceCity);
    $panelUtils->addLine();
    if ($invoiceState) {
      $panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $invoiceState);
      $panelUtils->addLine();
      }
    $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $invoiceCountry);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $invoicePostalBox);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[14], "nbc"));
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $shippingAddress1);
    $panelUtils->addLine();
    if ($shippingAddress2) {
      $panelUtils->addLine('', $shippingAddress2);
      $panelUtils->addLine();
      }
    $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $shippingZipCode);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $shippingCity);
    $panelUtils->addLine();
    if ($shippingState) {
      $panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $shippingState);
      $panelUtils->addLine();
      }
    $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $shippingCountry);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $shippingPostalBox);
    $panelUtils->addLine();
    $panelUtils->openForm($PHP_SELF);
    $panelUtils->addLine('', $panelUtils->getCancel());
    $panelUtils->addHiddenField('closeWindow', 1);
    $panelUtils->closeForm();
    $str = $panelUtils->render();

    printAdminPage($str);
    }
  }

?>
