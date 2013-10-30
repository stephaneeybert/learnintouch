<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $shopOrderUtils->deleteCancelledOrders();

  $str = LibHtml::urlRedirect("$gShopUrl/order/admin.php");
  printContent($str);
  return;

  } else {

  $panelUtils->setHeader($mlText[0], "$gShopUrl/order/admin.php");
  $panelUtils->openForm($PHP_SELF);

  $panelUtils->addLine($panelUtils->addCell("$mlText[3]", "nb"), $panelUtils->addCell("$mlText[5]", "nbc"), $panelUtils->addCell("$mlText[4]", "nbr"));
  $panelUtils->addLine();

  $shopOrders = $shopOrderUtils->selectByStatus(SHOP_ORDER_STATUS_CANCELLED);
  $panelUtils->openList();
  foreach ($shopOrders as $shopOrder) {
    $shopOrderId = $shopOrder->getId();
    $firstname = $shopOrder->getFirstname();
    $lastname = $shopOrder->getLastname();
    $organisation = $shopOrder->getOrganisation();
    $email = $shopOrder->getEmail();
    $orderDate = $shopOrder->getOrderDate();

    if ($organisation) {
      $strOrganisation = "- $organisation";
      } else {
      $strOrganisation = '';
      }

    $strName = "<a href='mailto:$email'>$firstname $lastname</a>";

    $panelUtils->addLine("$strName $strOrganisation", $panelUtils->addCell($shopOrderId, "nc"), $panelUtils->addCell($orderDate, "nr"));
    }
  $panelUtils->closeList();

  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[1], $mlText[2], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->getOk(), '');
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);

  }

?>
