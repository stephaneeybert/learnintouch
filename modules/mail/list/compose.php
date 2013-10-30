<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$addUser = LibEnv::getEnvHttpPOST("addUser");
$removeUser = LibEnv::getEnvHttpPOST("removeUser");
$addMailAddress = LibEnv::getEnvHttpPOST("addMailAddress");
$removeMailAddress = LibEnv::getEnvHttpPOST("removeMailAddress");
$mailListId = LibEnv::getEnvHttpPOST("mailListId");

if ($removeUser) {
  $listUserIds = LibEnv::getEnvHttpPOST("listUserIds");

  if ($listUserIds && $mailListId) {
    foreach ($listUserIds as $listUserId) {
      if ($mailListUser = $mailListUserUtils->selectByMailListIdAndUserId($mailListId, $listUserId)) {
        $mailListUserId = $mailListUser->getId();
        $mailListUserUtils->delete($mailListUserId);
      }
    }
  }
} else if ($addUser) {
  $userIds = LibEnv::getEnvHttpPOST("userIds");

  if ($userIds && $mailListId) {
    foreach ($userIds as $userId) {
      if (!$mailListUser = $mailListUserUtils->selectByMailListIdAndUserId($mailListId, $userId)) {
        $mailListUser = new MailListUser();
        $mailListUser->setUserId($userId);
        $mailListUser->setMailListId($mailListId);
        $mailListUserUtils->insert($mailListUser);
      }
    }
  }
} else if ($removeMailAddress) {
  $listMailAddressIds = LibEnv::getEnvHttpPOST("listMailAddressIds");

  if ($listMailAddressIds && $mailListId) {
    foreach ($listMailAddressIds as $listMailAddressId) {
      if ($mailListAddress = $mailListAddressUtils->selectByMailListIdAndMailAddressId($mailListId, $listMailAddressId)) {
        $mailListAddressId = $mailListAddress->getId();
        $mailListAddressUtils->delete($mailListAddressId);
      }
    }
  }
} else if ($addMailAddress) {
  $mailAddressIds = LibEnv::getEnvHttpPOST("mailAddressIds");

  if ($mailAddressIds && $mailListId) {
    foreach ($mailAddressIds as $mailAddressId) {
      if (!$mailListAddress = $mailListAddressUtils->selectByMailListIdAndMailAddressId($mailListId, $mailAddressId)) {
        $mailListAddress = new MailListAddress();
        $mailListAddress->setMailAddressId($mailAddressId);
        $mailListAddress->setMailListId($mailListId);
        $mailListAddressUtils->insert($mailListAddress);
      }
    }
  }
}

$email = LibEnv::getEnvHttpPOST("email");

$email = LibString::cleanString($email);

if ($email) {

  // Validate the email
  if (!LibEmail::validate($email)) {
    array_push($warnings, $mlText[21]);
  }

  // Validate the email domain name
  if (!LibEmail::validateDomain($email)) {
    array_push($warnings, $mlText[22]);
  }

  if (count($warnings) == 0) {

    if (!$mailAddress = $mailAddressUtils->selectByEmail($email)) {
      $mailAddress = new MailAddress();
      $mailAddress->setEmail($email);
      $mailAddress->setSubscribe(true);
      $mailAddressUtils->insert($mailAddress);
      $mailAddressId = $mailAddressUtils->getLastInsertId();

      if ($mailListId && $mailAddressId) {
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

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchPattern = LibString::cleanString($searchPattern);

$searchCountry = LibEnv::getEnvHttpPOST("searchCountry");
$searchCountry = LibString::cleanString($searchCountry);

$mailListId = LibEnv::getEnvHttpGET("mailListId");
if (!$mailListId) {
  $mailListId = LibEnv::getEnvHttpPOST("mailListId");
}

$name = '';
if ($mailListId) {
  if ($mailList = $mailListUtils->selectById($mailListId)) {
    $name = $mailList->getName();
  }
}

if ($searchCountry) {
  $mailListUsers = $mailListUserUtils->selectByMailListIdAndMailSubscribersLikeCountry($mailListId, $searchCountry);
} else if ($searchPattern) {
  $mailListUsers = $mailListUserUtils->selectByMailListIdAndMailSubscribersLikePattern($mailListId, $searchPattern);
} else {
  $mailListUsers = $mailListUserUtils->selectByMailListId($mailListId);
}
$mailList = Array();
foreach ($mailListUsers as $mailListUser) {
  $wId = $mailListUser->getId();
  $wMailListId = $mailListUser->getMailListId();
  $wUserId = $mailListUser->getUserId();
  if ($user = $userUtils->selectById($wUserId)) {
    $wFirstname = $user->getFirstname();
    $wLastname = $user->getLastname();
    $wEmail = $user->getEmail();
    if ($wEmail) {
      if ($wFirstname && $wLastname) {
        $wName = "$wFirstname $wLastname";
      } else {
        $wName = $wEmail;
      }
      $mailList[$wUserId] = "$wName";
    }
  }
}
$strSelectListUser = LibHtml::getMultiSelectList("listUserIds", $mailList, 10);

if ($searchCountry) {
  $users = $userUtils->searchMailSubscribersLikeCountry($searchCountry);
} else if ($searchPattern) {
  $users = $userUtils->searchMailSubscribersLikePattern($searchPattern);
} else {
  $users = $userUtils->selectAllMailSubscribers();
}
$userList = Array();
foreach ($users as $user) {
  $wId = $user->getId();
  $wFirstname = $user->getFirstname();
  $wLastname = $user->getLastname();
  $wEmail = $user->getEmail();
  if (!$mailListUser = $mailListUserUtils->selectByMailListIdAndUserId($mailListId, $wId)) {
    if ($wEmail) {
      if ($wFirstname && $wLastname) {
        $wName = "$wFirstname $wLastname";
      } else {
        $wName = $wEmail;
      }
      $userList[$wId] = "$wName";
    }
  }
}
$strSelectUser = LibHtml::getMultiSelectList("userIds", $userList, 10);

if ($searchCountry) {
  $mailListAddresses = $mailListAddressUtils->selectByMailListIdAndSubscribersLikeCountry($mailListId, $searchCountry);
} else if ($searchPattern) {
  $mailListAddresses = $mailListAddressUtils->selectByMailListIdAndSubscribersLikePattern($mailListId, $searchPattern);
} else {
  $mailListAddresses = $mailListAddressUtils->selectByMailListId($mailListId);
}
$mailList = Array();
foreach ($mailListAddresses as $mailListAddress) {
  $wId = $mailListAddress->getId();
  $wMailListId = $mailListAddress->getMailListId();
  $wMailAddressId = $mailListAddress->getMailAddressId();
  if ($mailAddress = $mailAddressUtils->selectById($wMailAddressId)) {
    $wFirstname = $mailAddress->getFirstname();
    $wLastname = $mailAddress->getLastname();
    $wEmail = $mailAddress->getEmail();
    if ($wEmail) {
      if ($wFirstname || $wLastname) {
        $strName = $wFirstname . ' ' . $wLastname;
      } else {
        $strName = $wEmail;
      }
      $mailList[$wMailAddressId] = $strName;
    }
  }
}
$strSelectListMailAddress = LibHtml::getMultiSelectList("listMailAddressIds", $mailList, 10);

if ($searchCountry) {
  $mailAddresses = $mailAddressUtils->selectSubscribersLikeCountry($searchCountry);
} else if ($searchPattern) {
  $mailAddresses = $mailAddressUtils->selectSubscribersLikePattern($searchPattern);
} else {
  $mailAddresses = $mailAddressUtils->selectSubscribers();
}
$mailAddressList = Array();
foreach ($mailAddresses as $mailAddress) {
  $wId = $mailAddress->getId();
  $wFirstname = $mailAddress->getFirstname();
  $wLastname = $mailAddress->getLastname();
  $wEmail = $mailAddress->getEmail();
  if (!$mailListAddress = $mailListAddressUtils->selectByMailListIdAndMailAddressId($mailListId, $wId)) {
    if ($wEmail) {
      if ($wFirstname || $wLastname) {
        $strName = $wFirstname . ' ' . $wLastname;
      } else {
        $strName = $wEmail;
      }
      $mailAddressList[$wId] = $strName;
    }
  }
}
$strSelectMailAddress = LibHtml::getMultiSelectList("mailAddressIds", $mailAddressList, 10);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gMailUrl/list/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine('', $panelUtils->addCell($mlText[6], "nbr"), $name);
$panelUtils->addHiddenField('mailListId', $mailListId);
$panelUtils->closeForm();
$panelUtils->addLine();
$strCell1 = ''
  . "\n<form action='$PHP_SELF' method='post'>"
  . "\n" . $strSelectListUser;

$strCell2 = ''
  . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageRight'>"
  . "\n<input type='hidden' name='removeUser' value='1'>"
  . "\n<input type='hidden' name='mailListId' value='$mailListId'>"
  . "\n</form>"
  . "\n<form action='$PHP_SELF' method='post'>"
  . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageLeft'>";

$strCell3 = ''
  . "\n" . $strSelectUser
  . "\n<input type='hidden' name='addUser' value='1'>"
  . "\n<input type='hidden' name='mailListId' value='$mailListId'>"
  . "\n</form>";

$panelUtils->addLine($panelUtils->addCell(count($mailListUsers) . ' ' . $mlText[1], "nbr"), '', $panelUtils->addCell($mlText[2], "nb"));
$panelUtils->addLine($panelUtils->addCell($strCell1, "nbr"), $panelUtils->addCell($strCell2, "nbc"), $strCell3);

$strCell1 = ''
  . "\n<form action='$PHP_SELF' method='post'>"
  . "\n" . $strSelectListMailAddress;

$strCell2 = ''
  . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageRight'>"
  . "\n<input type='hidden' name='removeMailAddress' value='1'>"
  . "\n<input type='hidden' name='mailListId' value='$mailListId'>"
  . "\n</form>"
  . "\n<form action='$PHP_SELF' method='post'>"
  . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageLeft'>";

$strCell3 = ''
  . "\n" . $strSelectMailAddress
  . "\n<input type='hidden' name='addMailAddress' value='1'>"
  . "\n<input type='hidden' name='mailListId' value='$mailListId'>"
  . "\n</form>";

$labelEmail = $popupUtils->getTipPopup($mlText[19], $mlText[20], 300, 300);
$strEmailInput = "\n<form action='$PHP_SELF' method='post'>"
  . "\n<input type='text' name='email' value='$email' size='30' maxlength='255' value=''> "
  . "\n<input type='hidden' name='mailListId' value='$mailListId'>"
  . ' ' . $panelUtils->getTinyOk()
  . "\n</form>";

$labelSearch = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . $panelUtils->getTinyOk()
  . "<input type='hidden' name='mailListId' value='$mailListId'>"
  . "</form>";

$labelCountry = $popupUtils->getTipPopup($mlText[5], $mlText[7], 300, 300);
$strCountry = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchCountry' size='20' maxlength='50' value='$searchCountry'> "
  . $panelUtils->getTinyOk()
  . "<input type='hidden' name='mailListId' value='$mailListId'>"
  . "</form>";

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell(count($mailListAddresses) . ' ' . $mlText[3], "nbr"), '', $panelUtils->addCell($mlText[4], "nb"));
$panelUtils->addLine($panelUtils->addCell($strCell1, "nr"), $panelUtils->addCell($strCell2, "nc"), $strCell3);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($labelEmail, "nbr"), '', $panelUtils->addCell($labelSearch, "nb"));
$panelUtils->addLine($panelUtils->addCell($strEmailInput, "nr"), '', $panelUtils->addCell($strSearch, "n"));
$panelUtils->addLine('', '', $panelUtils->addCell($labelCountry, "nb"));
$panelUtils->addLine('', '', $panelUtils->addCell($strCountry, "n"));
$str = $panelUtils->render();

printAdminPage($str);

?>
