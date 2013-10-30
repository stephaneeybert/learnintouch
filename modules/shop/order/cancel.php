<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $shopOrderId = LibEnv::getEnvHttpPOST("shopOrderId");

  if ($shopOrder = $shopOrderUtils->selectById($shopOrderId)) {
    $shopOrder->setStatus(SHOP_ORDER_STATUS_CANCELLED);
    $shopOrderUtils->update($shopOrder);
    }

  $str = LibHtml::urlRedirect("$gShopUrl/order/admin.php");
  printContent($str);
  return;

  } else {

  $shopOrderId = LibEnv::getEnvHttpGET("shopOrderId");

  if ($shopOrder = $shopOrderUtils->selectById($shopOrderId)) {
    $firstname = $shopOrder->getFirstname();
    $lastname = $shopOrder->getLastname();
    $organisation = $shopOrder->getOrganisation();
    $email = $shopOrder->getEmail();
    $orderDate = $shopOrder->getOrderDate();
    $currency = $shopOrder->getCurrency();

    $totalToPay = $shopOrderUtils->getTotalToPay($shopOrder);

    $panelUtils->setHeader($mlText[0], "$gShopUrl/order/admin.php");
    $panelUtils->openForm($PHP_SELF);
    $panelUtils->addLine();
    if ($organisation) {
      $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $organisation);
      $panelUtils->addLine();
      }
    $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "$firstname $lastname");
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), "$totalToPay $currency");
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $email);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), $orderDate);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $panelUtils->getOk());
    $panelUtils->addHiddenField('formSubmitted', 1);
    $panelUtils->addHiddenField('shopOrderId', $shopOrderId);
    $panelUtils->closeForm();
    $str = $panelUtils->render();

    printAdminPage($str);
    }

  }

?>
