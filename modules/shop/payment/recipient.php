<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $shopName = LibEnv::getEnvHttpPOST("shopName");
  $shopRegistrationNumber = LibEnv::getEnvHttpPOST("shopRegistrationNumber");
  $shopVATNumber = LibEnv::getEnvHttpPOST("shopVATNumber");
  $shopAddress = LibEnv::getEnvHttpPOST("shopAddress");
  $shopZipCode = LibEnv::getEnvHttpPOST("shopZipCode");
  $shopCountry = LibEnv::getEnvHttpPOST("shopCountry");
  $shopTelephone = LibEnv::getEnvHttpPOST("shopTelephone");
  $shopMobilePhone = LibEnv::getEnvHttpPOST("shopMobilePhone");
  $shopEmail = LibEnv::getEnvHttpPOST("shopEmail");
  $shopBankName = LibEnv::getEnvHttpPOST("shopBankName");
  $shopBankAccount = LibEnv::getEnvHttpPOST("shopBankAccount");
  $shopBankIBAN = LibEnv::getEnvHttpPOST("shopBankIBAN");
  $shopBankBIC = LibEnv::getEnvHttpPOST("shopBankBIC");

  $shopName = LibString::cleanString($shopName);
  $shopRegistrationNumber = LibString::cleanString($shopRegistrationNumber);
  $shopVATNumber = LibString::cleanString($shopVATNumber);
  $shopAddress = LibString::cleanString($shopAddress);
  $shopZipCode = LibString::cleanString($shopZipCode);
  $shopCountry = LibString::cleanString($shopCountry);
  $shopTelephone = LibString::cleanString($shopTelephone);
  $shopMobilePhone = LibString::cleanString($shopMobilePhone);
  $shopEmail = LibString::cleanString($shopEmail);
  $shopBankName = LibString::cleanString($shopBankName);
  $shopBankAccount = LibString::cleanString($shopBankAccount);
  $shopBankIBAN = LibString::cleanString($shopBankIBAN);
  $shopBankBIC = LibString::cleanString($shopBankBIC);

  // The email is case insensitive
  $shopEmail = strtolower($shopEmail);

  // The email must have an email format
  if ($shopEmail && !LibEmail::validate($shopEmail)) {
    array_push($warnings, $mlText[4]);
  }

  if (count($warnings) == 0) {

    // Save the properties
    $propertyUtils->store('SHOP_NAME', $shopName);
    $propertyUtils->store('SHOP_REGISTRATION_NUMBER', $shopRegistrationNumber);
    $propertyUtils->store('SHOP_VAT_NUMBER', $shopVATNumber);
    $propertyUtils->store('SHOP_ADDRESS', $shopAddress);
    $propertyUtils->store('SHOP_ZIPCODE', $shopZipCode);
    $propertyUtils->store('SHOP_COUNTRY', $shopCountry);
    $propertyUtils->store('SHOP_TELEPHONE', $shopTelephone);
    $propertyUtils->store('SHOP_MOBILE_PHONE', $shopMobilePhone);
    $propertyUtils->store('SHOP_EMAIL', $shopEmail);
    $propertyUtils->store('SHOP_BANK_NAME', $shopBankName);
    $propertyUtils->store('SHOP_BANK_ACCOUNT', $shopBankAccount);
    $propertyUtils->store('SHOP_BANK_IBAN', $shopBankIBAN);
    $propertyUtils->store('SHOP_BANK_BIC', $shopBankBIC);

    $str = LibHtml::urlRedirect("$gShopUrl/order/admin.php");
    printContent($str);
    return;

  }

}

$shopName = $propertyUtils->retrieve('SHOP_NAME');
$shopRegistrationNumber = $propertyUtils->retrieve('SHOP_REGISTRATION_NUMBER');
$shopVATNumber = $propertyUtils->retrieve('SHOP_VAT_NUMBER');
$shopAddress = $propertyUtils->retrieve('SHOP_ADDRESS');
$shopZipCode = $propertyUtils->retrieve('SHOP_ZIPCODE');
$shopCountry = $propertyUtils->retrieve('SHOP_COUNTRY');
$shopTelephone = $propertyUtils->retrieve('SHOP_TELEPHONE');
$shopMobilePhone = $propertyUtils->retrieve('SHOP_MOBILE_PHONE');
$shopEmail = $propertyUtils->retrieve('SHOP_EMAIL');

$shopBankName = $propertyUtils->retrieve('SHOP_BANK_NAME');
$shopBankAccount = $propertyUtils->retrieve('SHOP_BANK_ACCOUNT');
$shopBankIBAN = $propertyUtils->retrieve('SHOP_BANK_IBAN');
$shopBankBIC = $propertyUtils->retrieve('SHOP_BANK_BIC');

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gShopUrl/order/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[2], $mlText[3], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='shopName' value='$shopName' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[23], $mlText[24], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='shopRegistrationNumber' value='$shopRegistrationNumber' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[25], $mlText[26], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='shopVATNumber' value='$shopVATNumber' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[5], $mlText[6], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='shopAddress' value='$shopAddress' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[21], "nbr"), "<input type='text' name='shopZipCode' value='$shopZipCode' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[22], "nbr"), "<input type='text' name='shopCountry' value='$shopCountry' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[7], $mlText[8], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='shopTelephone' value='$shopTelephone' size='20' maxlength='20'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[9], $mlText[10], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='shopMobilePhone' value='$shopMobilePhone' size='20' maxlength='20'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[11], $mlText[12], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='shopEmail' value='$shopEmail' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[13], $mlText[14], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='shopBankName' value='$shopBankName' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[15], $mlText[16], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='shopBankAccount' value='$shopBankAccount' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[17], $mlText[18], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='shopBankIBAN' value='$shopBankIBAN' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[19], $mlText[20], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='shopBankBIC' value='$shopBankBIC' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
