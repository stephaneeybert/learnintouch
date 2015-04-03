<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $shopItemId = LibEnv::getEnvHttpPOST("shopItemId");
  $name = LibEnv::getEnvHttpPOST("name");
  $shortDescription = LibEnv::getEnvHttpPOST("shortDescription");
  $clearOrders = LibEnv::getEnvHttpPOST("clearOrders");

  if (!$clearOrders) {
    if ($shopOrderItems = $shopOrderItemUtils->selectByShopItemId($shopItemId)) {
      $strShopOrders = '';
      if (count($shopOrderItems) > 0) {
        $strShopOrders .= "<table border='0' cellpadding='2' cellspacing='2'>"
          . "<tr><td nowrap align='center'><b>$mlText[6]</b></td><td nowrap align='center'><b>$mlText[7]</b></td><td nowrap align='center'><b>$mlText[8]</b></td><td nowrap align='center'><b>$mlText[9]</b></td><td nowrap align='center'><b>$mlText[10]</b></td></tr>";
        foreach ($shopOrderItems as $shopOrderItem) {
          $shopOrderId = $shopOrderItem->getShopOrderId();
          if ($shopOrder = $shopOrderUtils->selectById($shopOrderId)) {
            $firstname = $shopOrder->getFirstname();
            $lastname = $shopOrder->getLastname();
            $email = $shopOrder->getEmail();
            $orderDate = $shopOrder->getOrderDate();
            $status = $shopOrder->getStatus();
            $strShopOrders .= "<tr><td>$firstname</td><td>$firstname</td><td>$firstname</td><td align='center'>$orderDate</td><td align='center'>$status</td></tr>";
          }
        }
        $strShopOrders .= "</table>";
      }
      array_push($warnings, $mlText[4]);
      array_push($warnings, $strShopOrders);
    }
  }

  if (count($warnings) == 0) {

    $shopItemUtils->deleteShopItem($shopItemId);

    $str = LibHtml::urlRedirect("$gShopUrl/item/admin.php");
    printContent($str);
    return;

  }

} else {

  $shopItemId = LibEnv::getEnvHttpGET("shopItemId");

  $name = '';
  $shortDescription = '';
  if ($shopItem = $shopItemUtils->selectById($shopItemId)) {
    $name = $shopItem->getName();
    $shortDescription = $shopItem->getShortDescription();
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gShopUrl/item/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $shortDescription);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
if ($shopOrderItems = $shopOrderItemUtils->selectByShopItemId($shopItemId)) {
  $panelUtils->addLine($panelUtils->addCell($mlText[11], "nbr"), "<input type='checkbox' name='clearOrders' value='1'>");
  $panelUtils->addLine();
}
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('shopItemId', $shopItemId);
$panelUtils->addHiddenField('name', $name);
$panelUtils->addHiddenField('shortDescription', $shortDescription);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
