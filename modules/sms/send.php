<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $smsId = LibEnv::getEnvHttpPOST("smsId");
  $smsListId = LibEnv::getEnvHttpPOST("smsListId");
  $userId = LibEnv::getEnvHttpPOST("userId");
  $parentUrl = LibEnv::getEnvHttpPOST("parentUrl");
  $mobilePhone = LibEnv::getEnvHttpPOST("mobilePhone");
  $userRecipients = LibEnv::getEnvHttpPOST("userRecipients");

  $mobilePhone = LibString::cleanString($mobilePhone);
  $userRecipients = LibString::cleanString($userRecipients);

  $body = '';
  if ($sms = $smsUtils->selectById($smsId)) {
    $body = $smsUtils->renderBody($sms);
  }

  // The body is required
  if (!$body) {
    array_push($warnings, $mlText[7]);
  }

  $websiteEmail = $profileUtils->getProfileValue("website.email");
  $websiteName = $profileUtils->getProfileValue("website.name");

  // Init the list of users
  $users = array();

  // This script can also act as a view controller for other scripts
  // In that case a list of recipients will already have been set
  // The recipients list is already set in the client script
  if (!isset($smsRecipients)) {
    $smsRecipients = array();
  }
  if (count($smsRecipients) == 0) {

    // Add the typed in mobile phone number if any
    if ($mobilePhone) {
      // The mobile phone number must have a numeric format
      if ($mobilePhone && !is_numeric($mobilePhone)) {
        array_push($warnings, $mlText[38]);
      }

      $mobilePhone = $smsGatewayUtils->cleanUpNumber($mobilePhone);

      // Add the mobile phone number to the list
      array_push($smsRecipients, array($mobilePhone, '', '', '', ''));
    } else if ($smsListId) {
      // Add the addresses and users if any

      $smsListNumbers = $smsListNumberUtils->selectBySmsListId($smsListId);
      foreach ($smsListNumbers as $smsListNumber) {
        $smsNumberId = $smsListNumber->getSmsNumberId();
        if ($smsNumber = $smsNumberUtils->selectById($smsNumberId)) {
          $mobilePhone = $smsNumber->getMobilePhone();
          $firstname = $smsNumber->getFirstname();
          $lastname = $smsNumber->getLastname();
          $subscribe = $smsNumber->getSubscribe();
          if ($mobilePhone && $subscribe) {
            // Add the mobile phone number to the list
            array_push($smsRecipients, array($mobilePhone, $firstname, $lastname, '', ''));
          }
        }
      }

      $smsListUsers = $smsListUserUtils->selectBySmsListId($smsListId);
      foreach ($smsListUsers as $smsListUser) {
        $userId = $smsListUser->getUserId();
        if ($user = $userUtils->selectById($userId)) {
          $firstname = $user->getFirstname();
          $lastname = $user->getLastname();
          $mobilePhone = $user->getMobilePhone();
          $email = $user->getEmail();
          $password = $user->getPassword();
          $smsSubscribe = $user->getSmsSubscribe();
          if ($mobilePhone && $smsSubscribe) {
            // Add the mobile phone number to the list
            array_push($smsRecipients, array($mobilePhone, $firstname, $lastname, $email, $password));
          }
        }
      }
    } else if ($userId) {
      // Send to a user
      if ($user = $userUtils->selectById($userId)) {
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $mobilePhone = $user->getMobilePhone();
        $email = $user->getEmail();
        $password = $user->getReadablePassword();
        $smsSubscribe = $user->getSmsSubscribe();
        if ($mobilePhone && $smsSubscribe) {
          // Add the mobile phone number to the list
          array_push($smsRecipients, array($mobilePhone, $firstname, $lastname, $email, $password));
        }
      }
    } else if ($userRecipients != '') {
      $systemDate = $clockUtils->getSystemDate();
      if ($userRecipients == 'user_recipient_expired') {
        $users = $userUtils->selectExpiredSmsSubscribers($systemDate);
      } else if ($userRecipients == 'user_recipient_current') {
        $users = $userUtils->selectCurrentSmsSubscribers($systemDate);
      } else if ($userRecipients == 'user_recipient_all') {
        $users = $userUtils->selectAllSmsSubscribers();
      }

      if (count($users) > 0) {
        foreach ($users as $user) {
          $userId = $user->getId();
          $mobilePhone = $user->getMobilePhone();
          $email = $user->getEmail();
          $password = $user->getPassword();
          $firstname = $user->getFirstname();
          $lastname = $user->getLastname();
          $smsSubscribe = $user->getSmsSubscribe();

          if ($smsSubscribe) {
            if ($mobilePhone) {
              // Add the mobile phone number to the list
              array_push($smsRecipients, array($mobilePhone, $firstname, $lastname, $email, $password));
            }
          }
        }
      }
    }

  }

  // Check that there are some mobile phone numbers
  if (count($smsRecipients) == 0) {
    array_push($warnings, $mlText[5]);
  }

  $smsRouteCosts = $smsGatewayUtils->getRouteCosts();

  // Check that the cost is retrieved
  if (count($smsRouteCosts) < 1) {
    array_push($warnings, $mlText[15]);
  }

  // Check that the account balance has enough credits to send the SMS message to all recipients
  $smsRoutingCost = 0;
  $nbRecipients = 0;
  foreach ($smsRecipients as $smsRecipient) {
    list($mobilePhone, $firstname, $lastname, $email, $password) = $smsRecipient;
    $countryCode = substr($mobilePhone, 2, 2);
    if (isset($smsRouteCosts[$countryCode])) {
      $routeCost = $smsRouteCosts[$countryCode];
    } else {
      $routeCost = SMS_DEFAULT_ROUTE_COST;
    }
    $smsRoutingCost += $routeCost;
    $nbRecipients++;
  }
  $balance = $smsGatewayUtils->checkBalance();
  if ($smsRoutingCost > $balance) {
    array_push($warnings, $mlText[14]);
  }

  if (count($warnings) == 0) {

    // Empty the outbox previous list of mobile phone numbers
    $smsOutboxUtils->deleteAll();

    // Store the list of mobile phone numbers in the outbox
    // for later use by the sms batch script
    foreach ($smsRecipients as $listedMobilePhone) {
      list($mobilePhone, $firstname, $lastname, $email, $password) = $listedMobilePhone;
      $smsOutbox = new SmsOutbox();
      $smsOutbox->setFirstname($firstname);
      $smsOutbox->setLastname($lastname);
      $smsOutbox->setMobilePhone($mobilePhone);
      $smsOutbox->setEmail($email);
      $smsOutbox->setPassword($password);
      $smsOutboxUtils->insert($smsOutbox);
    }

    $scriptFile = $gSmsUrl . "/sendBatch.php?smsId=$smsId";
    $commonUtils->execlCLIwget($scriptFile);

    // Get the date and time the SMS message has been sent
    $sendDate = $clockUtils->getSystemDateTime();

    // Register the SMS message sending in the history
    $adminId = $adminUtils->getLoggedAdminId();
    $smsHistory = new SmsHistory();
    $smsHistory->setSmsId($smsId);
    $smsHistory->setSmsListId($smsListId);
    $smsHistory->setMobilePhone($mobilePhone);
    $smsHistory->setAdminId($adminId);
    $smsHistory->setSendDate($sendDate);
    $smsHistory->setNbRecipients($nbRecipients);
    $smsHistoryUtils->insert($smsHistory);

    // Reset the session value for the result display
    LibSession::putSessionValue(SMS_SESSION_STATUS, '');

    $str = LibHtml::urlRedirect($parentUrl);
    printContent($str);
    return;

  }

}

$smsId = LibEnv::getEnvHttpGET("smsId");
if (!$smsId) {
  $smsId = LibEnv::getEnvHttpPOST("smsId");
}

$smsListId = LibEnv::getEnvHttpGET("smsListId");
if (!$smsListId) {
  $smsListId = LibEnv::getEnvHttpPOST("smsListId");
}

$userId = LibEnv::getEnvHttpGET("userId");
if (!$userId) {
  $userId = LibEnv::getEnvHttpPOST("userId");
}

$body = '';
$description = '';
if ($sms = $smsUtils->selectById($smsId)) {
  $description = $sms->getDescription();
  $body = $smsUtils->renderBody($sms);
}

$smsListName = '';
if ($smsList = $smsListUtils->selectById($smsListId)) {
  $smsListName = $smsList->getName();
}

$userName = '';
if ($user = $userUtils->selectById($userId)) {
  $userName = $user->getFirstname() . ' ' . $user->getLastname();
}

// Display only the logged in administrator's sms messages
if (!$adminUtils->isLoggedSuperAdmin()) {
  $adminId = $adminUtils->getLoggedAdminId();
} else {
  $adminId = '';
}

$userRecipients = Array('' => '', 'user_recipient_expired' => $mlText[16], 'user_recipient_current' => $mlText[17], 'user_recipient_all' => $mlText[18]);
$strSelectUserRecipient = LibHtml::getSelectList("userRecipients", $userRecipients);

if (!isset($parentUrl)) {
  $parentUrl = "$gSmsUrl/admin.php";
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], $parentUrl);
$help = $popupUtils->getHelpPopup($mlText[9], 300, 400);
$panelUtils->setHelp($help);
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gSmsUrl/suggestSms.php", "description", "smsId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('smsId', $smsId);
$label = $popupUtils->getTipPopup($mlText[20], $mlText[21], 300, 100);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='description' value='$description' size='40' />");
$panelUtils->addLine();
$strBody = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[3]'> $mlText[3]", "$gSmsUrl/preview.php?smsId=$smsId", 300, 200);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $panelUtils->addCell($strBody, ''));
$panelUtils->addLine();
// This script can also act as a view controller for other client scripts
// In that case a list of recipients will already have been set
if (!isset($strImposedSelectList)) {
  $strJsSuggest = $commonUtils->ajaxAutocomplete("$gSmsUrl/list/suggestLists.php", "smsListName", "smsListId");
  $panelUtils->addContent($strJsSuggest);
  $panelUtils->addHiddenField('smsListId', $smsListId);
  $label = $popupUtils->getTipPopup($mlText[6], $mlText[12], 300, 200);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='smsListName' value='$smsListName' size='40' />");
  $panelUtils->addLine();
  $strJsSuggest = $commonUtils->ajaxAutocomplete("$gUserUrl/suggestUsers.php", "userName", "userId");
  $panelUtils->addContent($strJsSuggest);
  $panelUtils->addHiddenField('userId', $userId);
  $label = $popupUtils->getTipPopup($mlText[19], $mlText[22], 300, 200);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='userName' value='$userName' size='40' />");
} else {
  $label = $popupUtils->getTipPopup($mlText[6], $mlText[12], 300, 200);
  $panelUtils->addContent($strHiddenPost);
  $panelUtils->addLine($panelUtils->addCell($label, "br"), $strImposedSelectList);
}
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[8], $mlText[11], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "br"), "<input type='text' name='mobilePhone' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[10], $mlText[13], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "br"), $strSelectUserRecipient);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('parentUrl', $parentUrl);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
