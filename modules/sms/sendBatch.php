<?PHP

require_once("website.php");

$smsId = LibEnv::getEnvHttpGET("smsId");

$body = '';
if ($sms = $smsUtils->selectById($smsId)) {
  $body = $smsUtils->renderBody($sms);
}

if (!$body) {
  reportError("The body of the SMS message is missing.");
}

$smsOutboxes = $smsOutboxUtils->selectUnsent();

foreach ($smsOutboxes as $smsOutbox) {
  $email = $smsOutbox->getEmail();
  $firstname = $smsOutbox->getFirstname();
  $lastname = $smsOutbox->getLastname();
  $mobilePhone = $smsOutbox->getMobilePhone();
  $password = $smsOutbox->getPassword();

  // Replace the meta values in the body
  $smsBody = $body;
  $metaNames = $smsUtils->getMetaNames();
  foreach ($metaNames as $metaName) {
    list($name, $phpVariable, $description) = $metaName;
    eval("\$smsBody = str_replace(\$name, \$$phpVariable, \$smsBody);");
  }

  // Remove extra blank spaces in the body
  $smsBody = LibString::stripMultipleSpaces($smsBody);

  $smsGatewayUtils->sendSMS($smsBody, $mobilePhone);

  // Mark the mobile phone number as having been sent the SMS message to
  $smsOutbox->setSent(1);
  $smsOutboxUtils->update($smsOutbox);
}

?>
