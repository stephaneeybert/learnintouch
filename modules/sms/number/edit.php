<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $smsNumberId = LibEnv::getEnvHttpPOST("smsNumberId");
  $mobilePhone = LibEnv::getEnvHttpPOST("mobilePhone");
  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");
  $subscribe = LibEnv::getEnvHttpPOST("subscribe");

  $mobilePhone = LibString::cleanString($mobilePhone);
  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);
  $subscribe = LibString::cleanString($subscribe);

  // Remove white spaces
  $mobilePhone = LibString::stripSpaces($mobilePhone);

  // The mobile phone number is required
  if (!$mobilePhone) {
    array_push($warnings, $mlText[10]);
  }

  $mobilePhone = LibString::cleanupPhoneNumber($mobilePhone);

  // The mobile phone number must be numerical
  if (!is_numeric($mobilePhone)) {
    array_push($warnings, $mlText[11]);
  }

  // Check that the number is not already used
  if ($smsNumber = $smsNumberUtils->selectByMobilePhone($mobilePhone)) {
    if (!$smsNumberId) {
      array_push($warnings, $mlText[5]);
    } else if ($smsNumberId != $smsNumber->getId()) {
      array_push($warnings, $mlText[5]);
    }
  }

  if (count($warnings) == 0) {

    if ($smsNumber = $smsNumberUtils->selectById($smsNumberId)) {
      $smsNumber->setMobilePhone($mobilePhone);
      $smsNumber->setFirstname($firstname);
      $smsNumber->setLastname($lastname);
      $smsNumber->setSubscribe($subscribe);
      $smsNumberUtils->update($smsNumber);
    } else {
      $smsNumber = new SmsNumber();
      $smsNumber->setMobilePhone($mobilePhone);
      $smsNumber->setFirstname($firstname);
      $smsNumber->setLastname($lastname);
      $smsNumber->setSubscribe($subscribe);
      $smsNumberUtils->insert($smsNumber);
    }

    $str = LibHtml::urlRedirect("$gSmsUrl/number/admin.php");
    printContent($str);
    return;

  }

} else {

  $smsNumberId = LibEnv::getEnvHttpGET("smsNumberId");

  $mobilePhone = '';
  $firstname = '';
  $lastname = '';
  $subscribe = '';
  if ($smsNumberId) {
    if ($smsNumber = $smsNumberUtils->selectById($smsNumberId)) {
      $mobilePhone = $smsNumber->getMobilePhone();
      $firstname = $smsNumber->getFirstname();
      $lastname = $smsNumber->getLastname();
      $subscribe = $smsNumber->getSubscribe();
    }
  }

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

$panelUtils->setHeader($mlText[0], "$gSmsUrl/number/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='firstname' value='$firstname' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), "<input type='text' name='lastname' value='$lastname' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='mobilePhone' value='$mobilePhone' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[4], $mlText[6], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type=checkbox name='subscribe' $checkedSubscribe value='1'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('smsNumberId', $smsNumberId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
