<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $accountEmail = LibEnv::getEnvHttpPOST("accountEmail");

  $accountEmail = LibString::cleanString($accountEmail);

  // The email is case insensitive
  $accountEmail = strtolower($accountEmail);

  // The email must have an email format
  if ($accountEmail && !LibEmail::validate($accountEmail)) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    $propertyUtils->store('SHOP_PAYPAL_ID', $accountEmail);

    $str = LibHtml::urlRedirect("$gShopUrl/payment/banks.php");
    printContent($str);
    return;

  }

}

$accountEmail = $propertyUtils->retrieve('SHOP_PAYPAL_ID');

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gShopUrl/payment/banks.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));

// Display the payment complete url for the Paypal Instant Payment Notification system
// This allows Paypal to redirect to the url
// The owner of the Paypal account must setup his account at Paypal with this url
$label = $popupUtils->getTipPopup($mlText[4], $mlText[5], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), SHOP_PAYMENT_PAYPAL_NOTIFY);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[1], $mlText[2], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='accountEmail' value='$accountEmail' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
