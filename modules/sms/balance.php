<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {
  $str = LibHtml::urlRedirect("$gSmsUrl/admin.php");
  printContent($str);
  return;
}

$panelUtils->setHeader($mlText[0], "$gSmsUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[3], 300, 400);
$panelUtils->setHelp($help);

$preferenceUtils->init($smsUtils->preferences);
$gateways = $preferenceUtils->getSelectOptions("SMS_GATEWAY");
$gateway = $preferenceUtils->getValue("SMS_GATEWAY");

$balance = $smsGatewayUtils->checkBalance();

if (is_array($gateways) && count($gateways) > 0 && in_array($gateway, $gateways)) {
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $gateways[$gateway]);
  $panelUtils->addLine();
}
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $balance . ' ' . $mlText[4]);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
