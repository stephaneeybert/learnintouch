<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $mailId = LibEnv::getEnvHttpPOST("mailId");
  $elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
  $elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");
  $elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");

  $subject = '';
  $body = '';
  $attachments = '';
  if ($mail = $mailUtils->selectById($mailId)) {
    $subject = $mail->getSubject();
    $body = $mail->getBody();
    $attachments = $mail->getAttachments();
  }

  // Fix the body
  if ($body) {
    $body = LibString::escapeQuotes($body);
  }

  // The subject and the body are required
  if (!$subject || !$body) {
    array_push($warnings, $mlText[7]);
  }

  $mailRecipients = array();

  if ($elearningSubscriptionId) {
    if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
      $elearningExerciseId = $elearningSubscriptionUtils->getNextExercise($elearningSubscription);
      $metaNames = array();
      if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
        $nextElearningExerciseName = $elearningExercise->getName();
        $nextElearningExerciseUrl = $templateUtils->renderPageUrl('SYSTEM_PAGE_ELEARNING_EXERCISE' . $elearningExerciseId . 'ELEARNING_SUBSCRIPTION_ID' . $elearningSubscriptionId);
        $strUrl = "<a href='$nextElearningExerciseUrl' $gJSNoStatus>$nextElearningExerciseName</a>";
        $metaNames['MAIL_META_ELEARNING_NEXT_EXERCISE_NAME'] = $strUrl;
      }
      $userId = $elearningSubscription->getUserId();
      if ($user = $userUtils->selectById($userId)) {
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $email = $user->getEmail();
        $password = $user->getPassword();
        if ($email) {
          array_push($mailRecipients, array($email, $firstname, $lastname, $password, $metaNames));
        }
      }
    }
  } else if ($elearningClassId) {
    // Get the class name
    $strClass = '';
    if ($elearningClass = $elearningClassUtils->selectById($elearningClassId)) {
      $strClass = $elearningClass->getName();
    }
    // Get the session name
    $strSession = '';
    if ($elearningSessionId) {
      if ($elearningSession = $elearningSessionUtils->selectById($elearningSessionId)) {
        $sessionName = $elearningSession->getName();
        $openDate = $elearningSession->getOpenDate();
        $closeDate = $elearningSession->getCloseDate();
        $openDate = $clockUtils->systemToLocalNumericDate($openDate);
        if ($clockUtils->systemDateIsSet($closeDate)) {
          $closeDate = $clockUtils->systemToLocalNumericDate($closeDate);
        } else {
          $closeDate = '';
        }
        $strSession = $sessionName . ' (' . $openDate . ' / ' . $closeDate . ')';
      }
      $elearningSubscriptions = $elearningSubscriptionUtils->selectBySessionIdAndClassId($elearningSessionId, $elearningClassId);
    } else {
      $elearningSubscriptions = $elearningSubscriptionUtils->selectByClassId($elearningClassId);
    }
    foreach ($elearningSubscriptions as $elearningSubscription) {
      $elearningSubscriptionId = $elearningSubscription->getId();
      $metaNames = array();
      $elearningExerciseId = $elearningSubscriptionUtils->getNextExercise($elearningSubscription);
      if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
        $nextElearningExerciseName = $elearningExercise->getName();
        $nextElearningExerciseUrl = $templateUtils->renderPageUrl('SYSTEM_PAGE_ELEARNING_EXERCISE' . $elearningExerciseId . 'ELEARNING_SUBSCRIPTION_ID' . $elearningSubscriptionId);
        $strUrl = "<a href='$nextElearningExerciseUrl' $gJSNoStatus>$nextElearningExerciseName</a>";
        $metaNames['MAIL_META_ELEARNING_NEXT_EXERCISE_NAME'] = $strUrl;
      }
      $userId = $elearningSubscription->getUserId();
      if ($user = $userUtils->selectById($userId)) {
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $email = $user->getEmail();
        $password = $user->getPassword();
        if ($email) {
          array_push($mailRecipients, array($email, $firstname, $lastname, $password, $metaNames));
        }
      }
    }
  }

  if (count($mailRecipients) == 0) {
    array_push($warnings, $mlText[5]);
  }

  if (count($warnings) == 0) {

    // Get the date and time the email has been sent
    $sendDateTime = $clockUtils->getSystemDateTime();

    if ($mail = $mailUtils->selectById($mailId)) {
      // Update the last send date
      $mail->setSendDate($sendDateTime);
      $mailUtils->update($mail);
    }

    // Empty the outbox previous list of email addresses
    $mailOutboxUtils->deleteAll();

    // Activate a semaphore to tell a mailing is ongoing
    $mailOutboxUtils->mailingOngoing();

    // Store the list of email addresses in the outbox
    // for later use by the mail batch script
    // Some meta names and their values can be specified
    // to replace in the mail body, meta names with specific values
    // like an elearning subscription for example
    foreach ($mailRecipients as $mailRecipient) {
      list($email, $firstname, $lastname, $password, $metaNames) = $mailRecipient;
      $firstname = LibString::escapeQuotes($firstname);
      $lastname = LibString::escapeQuotes($lastname);
      $strMetaNames = $mailOutboxUtils->metaNamesToString($metaNames);
      $mailOutbox = new MailOutbox();
      $mailOutbox->setFirstname($firstname);
      $mailOutbox->setLastname($lastname);
      $mailOutbox->setEmail($email);
      $mailOutbox->setPassword($password);
      $mailOutbox->setMetaNames($strMetaNames);
      $mailOutboxUtils->insert($mailOutbox);
    }

    $scriptFile = $gMailUrl . "/sendBatch.php?mailId=$mailId";
    $commonUtils->execlCLIwget($scriptFile);

    // Register the email sending in the history
    $adminId = $adminUtils->getLoggedAdminId();
    $mailHistory = new MailHistory();
    $mailHistory->setSubject($subject);
    $mailHistory->setBody($body);
    $mailHistory->setAttachments($attachments);
    $mailHistory->setAdminId($adminId);
    $mailHistory->setSendDate($sendDateTime);
    $mailHistoryUtils->insert($mailHistory);

    // Reset the session value for the result display
    LibSession::putSessionValue(MAIL_SESSION_STATUS, '');

    $str = LibHtml::urlRedirect("$gElearningUrl/subscription/admin.php");
    printContent($str);
    return;

  }

}

$mailId = LibEnv::getEnvHttpGET("mailId");
$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");
$elearningSessionId = LibEnv::getEnvHttpGET("elearningSessionId");
$elearningClassId = LibEnv::getEnvHttpGET("elearningClassId");

if (!$elearningSubscriptionId) {
  $elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
}

if (!$elearningSessionId) {
  $elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");
}

if (!$elearningClassId) {
  $elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");
}

if (!$mailId) {
  $mailId = LibEnv::getEnvHttpPOST("mailId");
}

// These variables are set for use by another included script
$mailRecipients = array();
$parentUrl = "$gElearningUrl/subscription/admin.php";

$subject = '';
if ($mail = $mailUtils->selectById($mailId)) {
  $subject = $mailUtils->renderSubject($mail);
  $subject = LibString::cleanString($subject);
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");
$help = $popupUtils->getHelpPopup($mlText[9], 300, 400);
$panelUtils->setHelp($help);

// Check if some mails failed in the previous sending
$nbFailed = $mailOutboxUtils->countFailed();

// Display a warning if the last sending had some failed sendings
if ($nbFailed > 0) {
  $panelUtils->addLine('', $panelUtils->addCell($mlText[25], "w"));
  $panelUtils->addLine();
}

// Check that no other mail is being sent
// Avoid concurrent access to the mass mailing
$ongoing = $mailOutboxUtils->isMailingOnGoing();
if ($ongoing) {
  $panelUtils->addLine('', $panelUtils->addCell($mlText[22], "w"));
  $panelUtils->addLine();
}

$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gMailUrl/suggestMails.php", "subject", "mailId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('mailId', $mailId);
$label = $popupUtils->getTipPopup($mlText[20], $mlText[21], 300, 100);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='subject' value='$subject' size='40' />");
if ($mailId) {
  $strBody = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[4]'> $mlText[4]", "$gMailUrl/preview.php?mailId=$mailId", 600, 600);

  $strAttachment = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[18]'> $mlText[18]", "$gMailUrl/attachment/admin.php?mailId=$mailId", 600, 600);
  $panelUtils->addLine('', "$strBody $strAttachment");
}
$panelUtils->addLine();

if ($elearningSubscriptionId) {
  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    $userId = $elearningSubscription->getUserId();
    if ($user = $userUtils->selectById($userId)) {
      $firstname = $user->getFirstname();
      $lastname = $user->getLastname();
      $panelUtils->addLine($panelUtils->addCell($mlText[10], "br"), "$firstname $lastname");
    }
  }
} else if ($elearningClassId) {
  if ($elearningClass = $elearningClassUtils->selectById($elearningClassId)) {
    $strClass = $elearningClass->getName();
    $panelUtils->addLine($panelUtils->addCell($mlText[6], "br"), $strClass);
  }
  if ($elearningSessionId) {
    if ($elearningSession = $elearningSessionUtils->selectById($elearningSessionId)) {
      $sessionName = $elearningSession->getName();
      $openDate = $elearningSession->getOpenDate();
      $closeDate = $elearningSession->getCloseDate();
      $openDate = $clockUtils->systemToLocalNumericDate($openDate);
      if ($clockUtils->systemDateIsSet($closeDate)) {
        $closeDate = $clockUtils->systemToLocalNumericDate($closeDate);
      } else {
        $closeDate = '';
      }
      $strSession = $sessionName . ' (' . $openDate . ' / ' . $closeDate . ')';
      $panelUtils->addLine($panelUtils->addCell($mlText[8], "br"), $strSession);
    }
    $elearningSubscriptions = $elearningSubscriptionUtils->selectBySessionIdAndClassId($elearningSessionId, $elearningClassId);
  } else {
    $elearningSubscriptions = $elearningSubscriptionUtils->selectByClassId($elearningClassId);
  }
  $panelUtils->addLine();
  foreach ($elearningSubscriptions as $elearningSubscription) {
    $elearningSubscriptionId = $elearningSubscription->getId();
    $userId = $elearningSubscription->getUserId();
    if ($user = $userUtils->selectById($userId)) {
      $firstname = $user->getFirstname();
      $lastname = $user->getLastname();
      $panelUtils->addLine($panelUtils->addCell($mlText[10], "br"), "$firstname $lastname");
    }
  }
}

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningSubscriptionId', $elearningSubscriptionId);
$panelUtils->addHiddenField('elearningSessionId', $elearningSessionId);
$panelUtils->addHiddenField('elearningClassId', $elearningClassId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
