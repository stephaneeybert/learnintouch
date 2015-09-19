<?PHP

require_once("website.php");

// The administrator may access this page without being logged in if a unique token is used
// This allows an administrator to access this page by clicking on a link in an email
$tokenName = LibEnv::getEnvHttpGET("tokenName");
$tokenValue = LibEnv::getEnvHttpGET("tokenValue");
if (!$uniqueTokenUtils->isValid($tokenName, $tokenValue)) {
  // If no token is used, then
  // check that the administrator is allowed to use the module
  $adminModuleUtils->checkAdminModule(MODULE_SHOP);
}

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $shopOrderId = LibEnv::getEnvHttpPOST("shopOrderId");
  $status = LibEnv::getEnvHttpPOST("status");
  $orderDate = LibEnv::getEnvHttpPOST("orderDate");
  $dueDate = LibEnv::getEnvHttpPOST("dueDate");
  $invoiceNumber = LibEnv::getEnvHttpPOST("invoiceNumber");
  $invoiceNote = LibEnv::getEnvHttpPOST("invoiceNote");
  $invoiceLanguage = LibEnv::getEnvHttpPOST("invoiceLanguage");
  $totalToPay = LibEnv::getEnvHttpPOST("totalToPay");

  $status = LibString::cleanString($status);
  $orderDate = LibString::cleanString($orderDate);
  $dueDate = LibString::cleanString($dueDate);
  $invoiceNumber = LibString::cleanString($invoiceNumber);
  $invoiceNote = LibString::cleanString($invoiceNote);
  $invoiceLanguage = LibString::cleanString($invoiceLanguage);
  $totalToPay = LibString::cleanString($totalToPay);

  // Validate the due date
  if ($dueDate && !$clockUtils->isLocalNumericDateValid($dueDate)) {
    array_push($warnings, $mlText[12] . ' ' . $clockUtils->getDateNumericFormatTip());
  }

  if ($dueDate) {
    $dueDate = $clockUtils->localToSystemDate($dueDate);
  } else {
    $dueDate = $clockUtils->getSystemDate();
  }

  // The due date must be after the invoice date
  if ($dueDate && $orderDate && $clockUtils->systemDateIsGreater($orderDate, $dueDate)) {
    array_push($warnings, $mlText[14]);
  }

  if ($status < 0) {
    $status = '';
  }

  if (count($warnings) == 0) {
    if ($shopOrder = $shopOrderUtils->selectById($shopOrderId)) {
      $shopOrder->setStatus($status);
      $shopOrder->setDueDate($dueDate);
      $shopOrder->setInvoiceNumber($invoiceNumber);
      $shopOrder->setInvoiceNote($invoiceNote);
      $shopOrder->setInvoiceLanguage($invoiceLanguage);
      $shopOrderUtils->update($shopOrder);
    }

    $str = LibHtml::urlRedirect("$gShopUrl/order/admin.php");
    printContent($str);
    return;
  }

} else {

  $shopOrderId = LibEnv::getEnvHttpGET("shopOrderId");

  if ($shopOrder = $shopOrderUtils->selectById($shopOrderId)) {
    $invoiceNumber = $shopOrder->getInvoiceNumber();
    $invoiceNote = $shopOrder->getInvoiceNote();
    $invoiceLanguage = $shopOrder->getInvoiceLanguage();
    $dueDate = $shopOrder->getDueDate();
    $firstname = $shopOrder->getFirstname();
    $lastname = $shopOrder->getLastname();
    $organisation = $shopOrder->getOrganisation();
    $vatNumber = $shopOrder->getVatNumber();
    $email = $shopOrder->getEmail();
    $telephone = $shopOrder->getTelephone();
    $mobilePhone = $shopOrder->getMobilePhone();
    $fax = $shopOrder->getFax();
    $message = $shopOrder->getMessage();
    $currency = $shopOrder->getCurrency();
    $orderDate = $shopOrder->getOrderDate();
    $status = $shopOrder->getStatus();

    $totalToPay = $shopOrderUtils->getTotalToPay($shopOrder);
    $totalToPay = $shopItemUtils->decimalFormat($totalToPay);
  }

}

$orderStatuses = $shopItemUtils->getOrderStatuses();
$strSelectStatus = LibHtml::getSelectList("status", $orderStatuses, $status);

$strAddress = $popupUtils->getDialogPopup($mlText[13], "$gShopUrl/order/viewAddress.php?shopOrderId=$shopOrderId", 800, 600);

$strItems = $popupUtils->getDialogPopup($mlText[15], "$gShopUrl/order/viewItems.php?shopOrderId=$shopOrderId", 800, 600);

$languageNames = array('en' => 'english', 'fr' => 'français');
$strSelectLanguage = LibHtml::getSelectList("invoiceLanguage", $languageNames, $invoiceLanguage);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gShopUrl/order/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[10], $mlText[11], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectStatus);
$panelUtils->addLine();
$orderLocalDate = $clockUtils->systemToLocalNumericDate($orderDate);
$dueDate = $clockUtils->systemToLocalNumericDate($dueDate);
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), $orderLocalDate);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='dueDate' id='dueDate' value='$dueDate' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[18], "nbr"),  "<input type='text' name='invoiceNumber' value='$invoiceNumber' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[19], $mlText[20], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<textarea name='invoiceNote' cols='28' rows='5'>$invoiceNote</textarea>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[21], $mlText[22], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectLanguage);
$panelUtils->addLine();
if ($message) {
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $message);
  $panelUtils->addLine();
}
if ($organisation) {
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $organisation);
  $panelUtils->addLine();
}
if ($vatNumber) {
  $panelUtils->addLine($panelUtils->addCell($mlText[17], "nbr"), $vatNumber);
  $panelUtils->addLine();
}
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "$firstname $lastname $strAddress");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), "$totalToPay $currency $strItems");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $email);
$panelUtils->addLine();
if ($telephone) {
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $telephone);
  $panelUtils->addLine();
}
if ($mobilePhone) {
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $mobilePhone);
  $panelUtils->addLine();
}
if ($fax) {
  $panelUtils->addLine($panelUtils->addCell($mlText[16], "nbr"), $fax);
  $panelUtils->addLine();
}

$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('shopOrderId', $shopOrderId);
$panelUtils->addHiddenField('orderDate', $orderDate);
$panelUtils->addHiddenField('totalToPay', $totalToPay);
$panelUtils->closeForm();

if ($clockUtils->isUSDateFormat()) {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#dueDate").datepicker({ dateFormat:'mm/dd/yy' });
});
</script>
HEREDOC;
} else {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#dueDate").datepicker({ dateFormat:'dd-mm-yy' });
});
</script>
HEREDOC;
}

$languageCode = $languageUtils->getCurrentAdminLanguageCode();
$code = LibJavaScript::renderJQueryDatepickerLanguageCode($languageCode);
$strJsSuggestCloseDate .= <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $.datepicker.setDefaults($.datepicker.regional['$code']);
});
</script>
HEREDOC;
$panelUtils->addContent($strJsSuggestCloseDate);

$str = $panelUtils->render();

printAdminPage($str);

?>
