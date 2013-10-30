<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $mailId = LibEnv::getEnvHttpPOST("mailId");
  $mailListId = LibEnv::getEnvHttpPOST("mailListId");
  $userId = LibEnv::getEnvHttpPOST("userId");
  $emailAddress = LibEnv::getEnvHttpPOST("emailAddress");
  $userRecipients = LibEnv::getEnvHttpPOST("userRecipients");
  $sendToAllFailed = LibEnv::getEnvHttpPOST("sendToAllFailed");
  $textFormat = LibEnv::getEnvHttpPOST("textFormat");
  $senderEmail = LibEnv::getEnvHttpPOST("senderEmail");
  $senderName = LibEnv::getEnvHttpPOST("senderName");

  $emailAddress = LibString::cleanString($emailAddress);
  $userRecipients = LibString::cleanString($userRecipients);
  $sendToAllFailed = LibString::cleanString($sendToAllFailed);
  $senderEmail = LibString::cleanString($senderEmail);
  $senderName = LibString::cleanString($senderName);

  if ($mailListId == '-1') {
    $mailListId = '';
  }

  $senderEmail = strtolower($senderEmail);
  if ($senderEmail && !LibEmail::validate($senderEmail)) {
    array_push($warnings, $mlText[39]);
  }

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
    // Escape the quote slashes
    $body = LibString::escapeQuotes($body);
  }

  // The subject and the body are required
  if (!$subject || !$body) {
    array_push($warnings, $mlText[7]);
  }

  $websiteEmail = $profileUtils->getProfileValue("website.email");
  $websiteName = $profileUtils->getProfileValue("website.name");

  $mailRecipients = array();

  // Send to the failed email addresses of the last sending
  if ($sendToAllFailed) {
    $nbFailed = $mailOutboxUtils->countFailed();
    if ($nbFailed > 0) {
      $mailOutboxes = $mailOutboxUtils->selectUnsent();
      if (count($mailOutboxes) > 0) {
        foreach ($mailOutboxes as $mailOutbox) {
          $firstname = $mailOutbox->getFirstname();
          $lastname = $mailOutbox->getLastname();
          $email = $mailOutbox->getEmail();
          // Add the email to the list
          array_push($mailRecipients, array($email, $firstname, $lastname, '', ''));
        }
      }
    }
  } else if ($emailAddress) {
    // Send to one or several email addresses
    $severalAddresses = $emailAddress;
    $severalAddresses = str_replace(',', ' ', $severalAddresses);
    $severalAddresses = str_replace(';', ' ', $severalAddresses);
    $severalAddresses = LibString::trim($severalAddresses);
    $severalAddresses = explode(' ', $severalAddresses);

    foreach ($severalAddresses as $oneAddress) {
      if (!LibEmail::validate($oneAddress)) {
        array_push($warnings, $mlText[38]);
      }

      // The email is case insensitive
      $oneAddress = strtolower($oneAddress);

      // Add the email to the list
      array_push($mailRecipients, array($oneAddress, '', '', '', ''));
    }
  } else if ($mailListId) {
    // Send to a list of recipients

    $mailListAddresses = $mailListAddressUtils->selectByMailListId($mailListId);
    foreach ($mailListAddresses as $mailListAddress) {
      $mailAddressId = $mailListAddress->getMailAddressId();
      if ($mailAddress = $mailAddressUtils->selectById($mailAddressId)) {
        $email = $mailAddress->getEmail();
        $firstname = $mailAddress->getFirstname();
        $lastname = $mailAddress->getLastname();
        $subscribe = $mailAddress->getSubscribe();
        if ($email && $subscribe) {
          // Add the email to the list
          array_push($mailRecipients, array($email, $firstname, $lastname, '', ''));
        }
      }
    }

    $mailListUsers = $mailListUserUtils->selectByMailListId($mailListId);
    foreach ($mailListUsers as $mailListUser) {
      $userId = $mailListUser->getUserId();
      if ($user = $userUtils->selectById($userId)) {
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $email = $user->getEmail();
        $password = $user->getReadablePassword();
        $subscribe = $user->getMailSubscribe();
        if ($email && $subscribe) {
          // Add the email to the list
          array_push($mailRecipients, array($email, $firstname, $lastname, $password, ''));
        }
      }
    }
  } else if ($userId) {
    // Send to a user
    if ($user = $userUtils->selectById($userId)) {
      $firstname = $user->getFirstname();
      $lastname = $user->getLastname();
      $email = $user->getEmail();
      $password = $user->getReadablePassword();
      $subscribe = $user->getMailSubscribe();
      if ($email && $subscribe) {
        // Add the email to the list
        array_push($mailRecipients, array($email, $firstname, $lastname, $password, ''));
      }
    }
  } else if ($userRecipients != '') {
    $systemDate = $clockUtils->getSystemDate();
    if ($userRecipients == 'user_recipient_expired') {
      $users = $userUtils->selectExpiredMailSubscribers($systemDate);
    } else if ($userRecipients == 'user_recipient_current') {
      $users = $userUtils->selectCurrentMailSubscribers($systemDate);
    } else if ($userRecipients == 'user_recipient_all') {
      $users = $userUtils->selectAllMailSubscribers();
    }

    if (count($users) > 0) {
      foreach ($users as $user) {
        $userId = $user->getId();
        $email = $user->getEmail();
        $password = $user->getReadablePassword();
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $subscribe = $user->getMailSubscribe();

        if ($subscribe) {
          if (LibEmail::validate($email)) {
            // Add the email to the list
            array_push($mailRecipients, array($email, $firstname, $lastname, $password, ''));
          }
        }
      }
    }
  } else if ($sendToAllFailed) {
    $nbFailed = $mailOutboxUtils->countFailed();
    if ($nbFailed > 0) {
      $mailOutboxes = $mailOutboxUtils->selectUnsent();
      if (count($mailOutboxes) > 0) {
        foreach ($mailOutboxes as $mailOutbox) {
          $firstname = $mailOutbox->getFirstname();
          $lastname = $mailOutbox->getLastname();
          $email = $mailOutbox->getEmail();
          // Add the email to the list
          array_push($mailRecipients, array($email, $firstname, $lastname, '', ''));
        }
      }
    }
  }

  // Check that there are some email addresses
  if (count($mailRecipients) == 0) {
    array_push($warnings, $mlText[5]);
  }

  if (count($warnings) == 0) {

    // Get the date and time the email has been sent
    $sendDateTime = $clockUtils->getSystemDateTime();

    if ($mail = $mailUtils->selectById($mailId)) {
      // Update the mail subject and format if changed
      $mail->setSubject($subject);
      $mail->setTextFormat($textFormat);

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

    $scriptFile = $gMailUrl . "/sendBatch.php?mailId=$mailId&senderEmail=$senderEmail&senderName=$senderName";
    $commonUtils->execlCLIwget($scriptFile);

    // Register the email sending in the history
    $adminId = $adminUtils->getLoggedAdminId();
    $mailHistory = new MailHistory();
    $mailHistory->setSubject($subject);
    $mailHistory->setBody($body);
    $mailHistory->setAttachments($attachments);
    $mailHistory->setMailListId($mailListId);
    $mailHistory->setEmail($emailAddress);
    $mailHistory->setAdminId($adminId);
    $mailHistory->setSendDate($sendDateTime);
    $mailHistoryUtils->insert($mailHistory);

    // Reset the session value for the result display
    LibSession::putSessionValue(MAIL_SESSION_STATUS, '');

    $str = LibHtml::urlRedirect("$gMailUrl/admin.php");
    printContent($str);
    return;

  }

}

$mailId = LibEnv::getEnvHttpGET("mailId");
if (!$mailId) {
  $mailId = LibEnv::getEnvHttpPOST("mailId");
}

$mailListId = LibEnv::getEnvHttpGET("mailListId");
if (!$mailListId) {
  $mailListId = LibEnv::getEnvHttpPOST("mailListId");
}

$userId = LibEnv::getEnvHttpGET("userId");
if (!$userId) {
  $userId = LibEnv::getEnvHttpPOST("userId");
}

$emailAddress = '';
$subject = '';
$textFormat = '';
if ($mail = $mailUtils->selectById($mailId)) {
  $subject = $mailUtils->renderSubject($mail);
  $subject = LibString::cleanString($subject);
  $textFormat = $mail->getTextFormat();
}

$mailListName = '';
if ($mailList = $mailListUtils->selectById($mailListId)) {
  $mailListName = $mailList->getName();
}

$userName = '';
if ($user = $userUtils->selectById($userId)) {
  $userName = $user->getFirstname() . ' ' . $user->getLastname();
}

if ($textFormat == '1') {
  $checkedTextFormat = "CHECKED";
} else {
  $checkedTextFormat = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gMailUrl/admin.php");
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

$userRecipients = Array('' => '', 'user_recipient_expired' => $mlText[14], 'user_recipient_current' => $mlText[17], 'user_recipient_all' => $mlText[19]);
$strSelectUserRecipient = LibHtml::getSelectList("userRecipients", $userRecipients);

$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gMailUrl/suggestMails.php", "subject", "mailId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('mailId', $mailId);
$label = $popupUtils->getTipPopup($mlText[20], $mlText[21], 300, 100);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='subject' value='$subject' size='40' />");
if ($mailId) {
  $strBody = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[3]'> $mlText[3]", "$gMailUrl/preview.php?mailId=$mailId", 600, 600);

  $strAttachment = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[18]'> $mlText[18]", "$gMailUrl/attachment/admin.php?mailId=$mailId", 600, 600);
  $panelUtils->addLine('', "$strBody $strAttachment");
}
$panelUtils->addLine();
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gMailUrl/list/suggestLists.php", "mailListName", "mailListId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('mailListId', $mailListId);
$label = $popupUtils->getTipPopup($mlText[6], $mlText[12], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='mailListName' value='$mailListName' size='40' />");
$panelUtils->addLine();
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gUserUrl/suggestUsers.php?subscribe=1", "userName", "userId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('userId', $userId);
$label = $popupUtils->getTipPopup($mlText[23], $mlText[24], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='userName' value='$userName' size='40' />");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[8], $mlText[11], 300, 400);
$panelUtils->addLine($panelUtils->addCell($label, "br"), "<input type='text' name='emailAddress' value='$emailAddress' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[10], $mlText[13], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "br"), $strSelectUserRecipient);
$panelUtils->addLine();
// Add the list of failed sendings if any
if ($nbFailed > 0) {
  $label = $popupUtils->getTipPopup($mlText[26], $mlText[27], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "wbr"), $panelUtils->addCell("<input type='checkbox' name='sendToAllFailed' value='1'> $mlText[28]", "w"));
  $panelUtils->addLine();
}
if (!isset($senderEmail)) {
  $senderEmail = $profileUtils->getProfileValue("website.email");
  $senderName = $profileUtils->getProfileValue("website.name");
}
$label = $popupUtils->getTipPopup($mlText[40], $mlText[41], 300, 400);
$panelUtils->addLine($panelUtils->addCell($label, "br"), "<input type='text' name='senderEmail' value='$senderEmail' size='30' maxlength='255'> <input type='text' name='senderName' value='$senderName' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[15], $mlText[16], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "rb"), "<input type='checkbox' name='textFormat' $checkedTextFormat value='1'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
