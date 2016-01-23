<?PHP

require_once("website.php");

error_log("Starting a mailing batch.");

$mailId = LibEnv::getEnvHttpGET("mailId");
$senderEmail = LibEnv::getEnvHttpGET("senderEmail");
$senderName = LibEnv::getEnvHttpGET("senderName");

$mlText = $languageUtils->getMlText(__FILE__);

if (!$senderEmail) {
  $senderEmail = $profileUtils->getProfileValue("website.email");
  $senderName = $profileUtils->getProfileValue("website.name");
}

// Get the subject and the body
$subject = '';
$body = '';
$attachedFiles = array();;
if ($mail = $mailUtils->selectById($mailId)) {
  $subject = $mailUtils->renderSubject($mail);
  $body = $mailUtils->renderBody($mail);
  $attachedFiles = $mailUtils->getExistingAttachedFiles($mailId, true);
}

// The subject and the body are required
if (!$subject || !$body) {
  reportError($mlText[0]);
}

// Check if the mail is to be sent in a text format
if ($mailUtils->inTextFormat($mailId)) {
  $inTextFormat = true;
} else {
  $inTextFormat = false;
}

// Create the array and transform the image tags in the body
$attachedImages = array();
if (!$inTextFormat) {
  // Transform the image urls into email image elements
  $body = $mailUtils->urlToEmailImageCID($body);
  $attachedImages = $mailUtils->getImagesFromCID($body);

  // Transform the links urls from relative to absolute
  $body = $mailUtils->relativeToAbsoluteUrls($body);

  // Add the user email address to a unsubscribe page url if any
  $body = $mailUtils->addEmailAddressToUnsubscribeUrl($body);

  // Add the user login name and password to a login page link if any
  $body = $mailUtils->addLoginPasswordToLoginUrl($body);
}

$mailOutboxes = $mailOutboxUtils->selectUnsent();

foreach ($mailOutboxes as $mailOutbox) {
  $mailOutboxId = $mailOutbox->getId();
  $firstname = $mailOutbox->getFirstname();
  $lastname = $mailOutbox->getLastname();
  $email = $mailOutbox->getEmail();
  $strMetaNames = $mailOutbox->getMetaNames();

  $firstname = LibString::escapeQuotes($firstname);
  $lastname = LibString::escapeQuotes($lastname);

  // Parse the meta names
  $mailBody = $mailOutboxUtils->parseMetaNames($body, $mailOutboxId);

  // Parse the custom meta names
  // to replace in the mail body, meta names with specific values
  // like an elearning subscription for example
  $metaNames = $mailOutboxUtils->stringToMetaNames($strMetaNames);
  $mailBody = $mailOutboxUtils->parseCustomMetaName($mailBody, $metaNames);

  if ($remainingMetaNames = $mailOutboxUtils->getRemainingMetaNames($mailBody)) {
    $errorMessage = $mlText[1] . ' ' . $remainingMetaNames;
    $mailOutbox->setErrorMessage($errorMessage);
    $mailOutboxUtils->update($mailOutbox);
  } else {
    // Send to the email addresses
error_log("Calling LibEmail::sendMail for $email $firstname $lastname");
    LibEmail::sendMail($email, "$firstname $lastname", $subject, $mailBody, $senderEmail, $senderName, $attachedImages, $attachedFiles, $inTextFormat);

    // Mark the email address as having been sent the email to
    if ($cloneMailOutbox = $mailOutboxUtils->cloneMailOutbox($mailOutbox)) {
      // A cloned object is used to work around the most puzzling of bugs
      // as updating the object being looped on appears to make the loop having missteps
      $cloneMailOutbox->setSent(1);
      $mailOutboxUtils->update($cloneMailOutbox);
error_log("The mail to $email $firstname $lastname has been marked as sent.");
    }
  }
}

emailStaff("A mailing has been completed. Search the log for duplicates. Search for: 'Calling LibEmail::sendMail'. Website $websiteName");

// Activate a semaphore to tell the mailing has ended
$mailOutboxUtils->mailingEnded();

?>
