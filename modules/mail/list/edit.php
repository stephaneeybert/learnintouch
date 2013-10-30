<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $autoSubscribe = LibEnv::getEnvHttpPOST("autoSubscribe");
  $mailListId = LibEnv::getEnvHttpPOST("mailListId");
  $userFromDate = LibEnv::getEnvHttpPOST("userFromDate");
  $userToDate = LibEnv::getEnvHttpPOST("userToDate");
  $addressFromDate = LibEnv::getEnvHttpPOST("addressFromDate");
  $addressToDate = LibEnv::getEnvHttpPOST("addressToDate");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $autoSubscribe = LibString::cleanString($autoSubscribe);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[8]);
  }

  // Validate the from and to dates
  if ($userFromDate && !$clockUtils->isLocalNumericDateValid($userFromDate)) {
    array_push($warnings, $mlText[5] . ' ' . $clockUtils->getDateNumericFormatTip());
  }
  if ($userToDate && !$clockUtils->isLocalNumericDateValid($userToDate)) {
    array_push($warnings, $mlText[5] . ' ' . $clockUtils->getDateNumericFormatTip());
  }
  if ($addressFromDate && !$clockUtils->isLocalNumericDateValid($addressFromDate)) {
    array_push($warnings, $mlText[5] . ' ' . $clockUtils->getDateNumericFormatTip());
  }
  if ($addressToDate && !$clockUtils->isLocalNumericDateValid($addressToDate)) {
    array_push($warnings, $mlText[5] . ' ' . $clockUtils->getDateNumericFormatTip());
  }

  if ($userFromDate) {
    $userFromDate = $clockUtils->localToSystemDate($userFromDate);
  }
  if ($userToDate) {
    $userToDate = $clockUtils->localToSystemDate($userToDate);
  }
  if ($addressFromDate) {
    $addressFromDate = $clockUtils->localToSystemDate($addressFromDate);
  }
  if ($addressToDate) {
    $addressToDate = $clockUtils->localToSystemDate($addressToDate);
  }

  // The to date must be after the from date
  if ($userToDate && $userFromDate && $clockUtils->systemDateIsGreater($userFromDate, $userToDate)) {
    array_push($warnings, $mlText[4]);
  }
  if ($addressToDate && $addressFromDate && $clockUtils->systemDateIsGreater($addressFromDate, $addressToDate)) {
    array_push($warnings, $mlText[4]);
  }

  if (count($warnings) == 0) {

    if ($mailList = $mailListUtils->selectById($mailListId)) {
      $mailList->setName($name);
      $mailList->setDescription($description);
      $mailList->setAutoSubscribe($autoSubscribe);
      $mailListUtils->update($mailList);
    } else {
      $mailList = new MailList();
      $mailList->setName($name);
      $mailList->setDescription($description);
      $mailList->setAutoSubscribe($autoSubscribe);
      $mailListUtils->insert($mailList);
      $mailListId = $mailListUtils->getLastInsertId();
    }

    if ($userFromDate && $userToDate) {
      if ($mailList = $mailListUtils->selectById($mailListId)) {
        if ($users = $userUtils->selectByCreationDateTime($userFromDate, $userToDate)) {
          foreach ($users as $user) {
            $userId = $user->getId();
            if (!$mailListUser = $mailListUserUtils->selectByMailListIdAndUserId($mailListId, $userId)) {
              $mailListUser = new MailListUser();
              $mailListUser->setUserId($userId);
              $mailListUser->setMailListId($mailListId);
              $mailListUserUtils->insert($mailListUser);
            }
          }
        }
      }
    }

    if ($addressFromDate && $addressToDate) {
      if ($mailList = $mailListUtils->selectById($mailListId)) {
        if ($mailAddresses = $mailAddressUtils->selectByCreationDateTime($addressFromDate, $addressToDate)) {
          foreach ($mailAddresses as $mailAddress) {
            $mailAddressId = $mailAddress->getId();
            if (!$mailListAddress = $mailListAddressUtils->selectByMailListIdAndMailAddressId($mailListId, $mailAddressId)) {
              $mailListAddress = new MailListAddress();
              $mailListAddress->setMailAddressId($mailAddressId);
              $mailListAddress->setMailListId($mailListId);
              $mailListAddressUtils->insert($mailListAddress);
            }
          }
        }
      }

    }

    $str = LibHtml::urlRedirect("$gMailUrl/list/admin.php");
    printContent($str);
    return;

  }

} else {

  $mailListId = LibEnv::getEnvHttpGET("mailListId");

  if (!$mailListId) {
    $mailListId = LibEnv::getEnvHttpPOST("mailListId");
  }

  $name = '';
  $description = '';
  $autoSubscribe = '';
  $userFromDate = '';
  $userToDate = '';
  $addressFromDate = '';
  $addressToDate = '';
  if ($mailListId) {
    if ($mailList = $mailListUtils->selectById($mailListId)) {
      $name = $mailList->getName();
      $description = $mailList->getDescription();
      $autoSubscribe = $mailList->getAutoSubscribe();
    }
  }

}

if ($clockUtils->systemDateIsSet($userFromDate)) {
  $userFromDate = $clockUtils->systemToLocalNumericDate($userFromDate);
} else {
  $userFromDate = '';
}
if ($clockUtils->systemDateIsSet($userToDate)) {
  $userToDate = $clockUtils->systemToLocalNumericDate($userToDate);
} else {
  $userToDate = '';
}
if ($clockUtils->systemDateIsSet($addressFromDate)) {
  $addressFromDate = $clockUtils->systemToLocalNumericDate($addressFromDate);
} else {
  $addressFromDate = '';
}
if ($clockUtils->systemDateIsSet($addressToDate)) {
  $addressToDate = $clockUtils->systemToLocalNumericDate($addressToDate);
} else {
  $addressToDate = '';
}

if ($autoSubscribe == '1') {
  $checkedAutoSubscribe = "CHECKED";
} else {
  $checkedAutoSubscribe = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gMailUrl/list/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, 'edit');
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[11], $mlText[12], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[9], $mlText[10], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='autoSubscribe' $checkedAutoSubscribe value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[2], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' name='userFromDate' id='userFromDate' value='$userFromDate' size='12' maxlength='10' class='date_field'> $mlText[3] <input type='text' name='userToDate' id='userToDate' value='$userToDate' size='12' maxlength='10' class='date_field'> " . $clockUtils->getDateNumericFormatTip(), "b"));
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[7], $mlText[2], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' name='addressFromDate' id='addressFromDate' value='$addressFromDate' size='12' maxlength='10' class='date_field'> $mlText[3] <input type='text' name='addressToDate' id='addressToDate' value='$addressToDate' size='12' maxlength='10' class='date_field'> " . $clockUtils->getDateNumericFormatTip(), "b"));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('mailListId', $mailListId);
$panelUtils->closeForm();

if ($clockUtils->isUSDateFormat()) {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $(".date_field").datepicker({ dateFormat:'mm/dd/yy' });
});
</script>
HEREDOC;
} else {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $(".date_field").datepicker({ dateFormat:'dd-mm-yy' });
});
</script>
HEREDOC;
}

$languageCode = $languageUtils->getCurrentAdminLanguageCode();
$code = LibJavaScript::renderJQueryDatepickerLanguageCode($languageCode);
$strJsSuggestCloseDate .= <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $.datepicker.setDefaults($.datepicker.regional['$code']);
});
</script>
HEREDOC;
$panelUtils->addContent($strJsSuggestCloseDate);

$str = $panelUtils->render();

printAdminPage($str);

?>
