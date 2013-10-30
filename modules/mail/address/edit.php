<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $mailAddressId = LibEnv::getEnvHttpPOST("mailAddressId");
  $email = LibEnv::getEnvHttpPOST("email");
  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");
  $comment = LibEnv::getEnvHttpPOST("comment");
  $country = LibEnv::getEnvHttpPOST("country");
  $subscribe = LibEnv::getEnvHttpPOST("subscribe");
  $validateDomainName = LibEnv::getEnvHttpPOST("validateDomainName");

  $email = LibString::cleanString($email);
  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);
  $comment = LibString::cleanString($comment);
  $country = LibString::cleanString($country);
  $subscribe = LibString::cleanString($subscribe);
  $validateDomainName = LibString::cleanString($validateDomainName);

  // The email is required
  if (!$email) {
    array_push($warnings, $mlText[10]);
  }

  // Validate the email
  if (!LibEmail::validate($email)) {
    array_push($warnings, $mlText[11]);
  }

  // Validate the email domain name
  if ($validateDomainName) {
    if (!LibEmail::validateDomain($email)) {
      array_push($warnings, $mlText[12]);
    }
  }

  // Make sure the email address is not yet used by another
  if ($mailAddress = $mailAddressUtils->selectByEmail($email)) {
    if (!$mailAddressId) {
      array_push($warnings, $mlText[7]);
    } else if ($mailAddressId != $mailAddress->getId()) {
      array_push($warnings, $mlText[7]);
    }
  }

  // Remove the address from the lists if any
  if (!$subscribe) {
    if ($mailListAddresses = $mailListAddressUtils->selectByMailAddressId($mailAddressId)) {
      foreach ($mailListAddresses as $mailListAddress) {
        $mailListAddressId = $mailListAddress->getId();
        $mailListAddressUtils->delete($mailListAddressId);
      }
    }
  }

  if (count($warnings) == 0) {

    $systemDateTime = $clockUtils->getSystemDateTime();

    if ($mailAddress = $mailAddressUtils->selectById($mailAddressId)) {
      $mailAddress->setEmail($email);
      $mailAddress->setFirstname($firstname);
      $mailAddress->setLastname($lastname);
      $mailAddress->setComment($comment);
      $mailAddress->setCountry($country);
      $mailAddress->setSubscribe($subscribe);
      $mailAddressUtils->update($mailAddress);
    } else if (!$mailAddress = $mailAddressUtils->selectByEmail($email)) {
      $mailAddress = new MailAddress();
      $mailAddress->setEmail($email);
      $mailAddress->setFirstname($firstname);
      $mailAddress->setLastname($lastname);
      $mailAddress->setComment($comment);
      $mailAddress->setCountry($country);
      $mailAddress->setSubscribe($subscribe);
      $mailAddress->setCreationDateTime($systemDateTime);
      $mailAddressUtils->insert($mailAddress);
    }

    $str = LibHtml::urlRedirect("$gMailUrl/address/admin.php");
    printContent($str);
    return;

  }

} else {

  $mailAddressId = LibEnv::getEnvHttpGET("mailAddressId");

  $email = '';
  $firstname = '';
  $lastname = '';
  $comment = '';
  $country = '';
  $subscribe = '1';
  $validateDomainName = '1';
  if ($mailAddressId) {
    if ($mailAddress = $mailAddressUtils->selectById($mailAddressId)) {
      $email = $mailAddress->getEmail();
      $firstname = $mailAddress->getFirstname();
      $lastname = $mailAddress->getLastname();
      $comment = $mailAddress->getComment();
      $country = $mailAddress->getCountry();
      $subscribe = $mailAddress->getSubscribe();
    }
  }

}

if ($validateDomainName == '1') {
  $checkedValidateDomainName = "CHECKED";
} else {
  $checkedValidateDomainName = '';
}

if ($subscribe == '1') {
  $checkedSubscribe = "CHECKED";
} else {
  $checkedSubscribe = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gMailUrl/address/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='email' value='$email' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[8], $mlText[9], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type=checkbox name='validateDomainName' $checkedValidateDomainName value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[4], $mlText[6], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type=checkbox name='subscribe' $checkedSubscribe value='1'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='firstname' value='$firstname' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), "<input type='text' name='lastname' value='$lastname' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<textarea name='comment' cols='30' rows='5'>$comment</textarea>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[13], "nbr"), "<input type='text' name='country' value='$country' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('mailAddressId', $mailAddressId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
