<?php

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$adminUtils->checkForStaffLogin();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ( $formSubmitted == 1 ) {

  $websiteId = LibEnv::getEnvHttpPOST("websiteId");
  $address1 = LibEnv::getEnvHttpPOST("address1");
  $address2 = LibEnv::getEnvHttpPOST("address2");
  $zipCode = LibEnv::getEnvHttpPOST("zipCode");
  $city = LibEnv::getEnvHttpPOST("city");
  $state = LibEnv::getEnvHttpPOST("state");
  $country = LibEnv::getEnvHttpPOST("country");
  $postalBox = LibEnv::getEnvHttpPOST("postalBox");
  $telephone = LibEnv::getEnvHttpPOST("telephone");
  $mobile = LibEnv::getEnvHttpPOST("mobile");
  $fax = LibEnv::getEnvHttpPOST("fax");
  $vatNumber = LibEnv::getEnvHttpPOST("vatNumber");

  $address1 = LibString::cleanString($address1);
  $address2 = LibString::cleanString($address2);
  $zipCode = LibString::cleanString($zipCode);
  $city = LibString::cleanString($city);
  $state = LibString::cleanString($state);
  $country = LibString::cleanString($country);
  $postalBox = LibString::cleanString($postalBox);
  $telephone = LibString::cleanString($telephone);
  $mobile = LibString::cleanString($mobile);
  $fax = LibString::cleanString($fax);
  $vatNumber = LibString::cleanString($vatNumber);

  // The telephone must be a numerical string
  if ($telephone && !is_numeric(LibString::stripSpaces($telephone))) {
    array_push($warnings, $mlText[36]);
  }

  // The mobile must be a numerical string
  if ($mobile && !is_numeric(LibString::stripSpaces($mobile))) {
    array_push($warnings, $mlText[33]);
  }

  // The telephone must be a numerical string
  if ($fax && !is_numeric(LibString::stripSpaces($fax))) {
    array_push($warnings, $mlText[34]);
  }

  if (count($warnings) == 0) {

    if ($websiteAddress = $websiteAddressUtils->selectByWebsite($websiteId)) {
      $websiteAddress->setAddress1($address1);
      $websiteAddress->setAddress2($address2);
      $websiteAddress->setZipCode($zipCode);
      $websiteAddress->setCity($city);
      $websiteAddress->setState($state);
      $websiteAddress->setCountry($country);
      $websiteAddress->setPostalBox($postalBox);
      $websiteAddress->setTelephone($telephone);
      $websiteAddress->setMobile($mobile);
      $websiteAddress->setFax($fax);
      $websiteAddress->setVatNumber($vatNumber);
      $websiteAddress->setWebsite($websiteId);
      $websiteAddressUtils->update($websiteAddress);
    } else {
      $websiteAddress = new WebsiteAddress();
      $websiteAddress->setAddress1($address1);
      $websiteAddress->setAddress2($address2);
      $websiteAddress->setZipCode($zipCode);
      $websiteAddress->setCity($city);
      $websiteAddress->setState($state);
      $websiteAddress->setCountry($country);
      $websiteAddress->setPostalBox($postalBox);
      $websiteAddress->setTelephone($telephone);
      $websiteAddress->setMobile($mobile);
      $websiteAddress->setFax($fax);
      $websiteAddress->setVatNumber($vatNumber);
      $websiteAddress->setWebsite($websiteId);
      $websiteAddressUtils->insert($websiteAddress);
    }

    $str = LibHtml::urlRedirect("$gWebsiteUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $websiteId = LibEnv::getEnvHttpGET("websiteId");

  // If an address exists then get current properties
  $address1 = '';
  $address2 = '';
  $zipCode = '';
  $city = '';
  $state = '';
  $country = '';
  $postalBox = '';
  $telephone = '';
  $mobile = '';
  $fax = '';
  $vatNumber = '';
  if ($websiteAddress = $websiteAddressUtils->selectByWebsite($websiteId)) {
    $address1 = $websiteAddress->getAddress1();
    $address2 = $websiteAddress->getAddress2();
    $zipCode = $websiteAddress->getZipCode();
    $city = $websiteAddress->getCity();
    $state = $websiteAddress->getState();
    $country = $websiteAddress->getCountry();
    $postalBox = $websiteAddress->getPostalBox();
    $telephone = $websiteAddress->getTelephone();
    $mobile = $websiteAddress->getMobile();
    $fax = $websiteAddress->getFax();
    $vatNumber = $websiteAddress->getVatNumber();
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gWebsiteUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[13], "nbr"), "<input type='text' name='address1' value='$address1' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[14], "nbr"), "<input type='text' name='address2' value='$address2' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[15], "nbr"), "<input type='text' name='zipCode' value='$zipCode' size='10' maxlength='10'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[16], "nbr"), "<input type='text' name='city' value='$city' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[17], "nbr"), "<input type='text' name='state' size='30' maxlength='255' value='$state'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[18], "nbr"), "<input type='text' name='country' size='30' maxlength='255' value='$country'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='postalBox' size='30' maxlength='255' value='$postalBox'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[19], "nbr"), "<input type='text' name='telephone' size='20' maxlength='20' value='$telephone'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[20], "nbr"), "<input type='text' name='mobile' size='20' maxlength='20' value='$mobile'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[21], "nbr"), "<input type='text' name='fax' size='20' maxlength='20' value='$fax'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='vatNumber' size='30' maxlength='50' value='$vatNumber'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('websiteId', $websiteId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
