<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_USER);

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
$validity = LibEnv::getEnvHttpPOST("validity");
$lastLogin = LibEnv::getEnvHttpPOST("lastLogin");
$mailingSubscription = LibEnv::getEnvHttpPOST("mailingSubscription");
$smsSubscription = LibEnv::getEnvHttpPOST("smsSubscription");

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(USER_SESSION_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(USER_SESSION_SEARCH_PATTERN, $searchPattern);
}

if (!$validity) {
  $validity = LibSession::getSessionValue(USER_SESSION_VALIDITY);
} else {
  LibSession::putSessionValue(USER_SESSION_VALIDITY, $validity);
}

if (!$lastLogin) {
  $lastLogin = LibSession::getSessionValue(USER_SESSION_LAST_LOGIN);
} else {
  LibSession::putSessionValue(USER_SESSION_LAST_LOGIN, $lastLogin);
}

if (!$mailingSubscription) {
  $mailingSubscription = LibSession::getSessionValue(USER_SESSION_MAIL_LIST_SUBSCRIPTION);
} else {
  LibSession::putSessionValue(USER_SESSION_MAIL_LIST_SUBSCRIPTION, $mailingSubscription);
}

if (!$smsSubscription) {
  $smsSubscription = LibSession::getSessionValue(USER_SESSION_SMS_LIST_SUBSCRIPTION);
} else {
  LibSession::putSessionValue(USER_SESSION_SMS_LIST_SUBSCRIPTION, $smsSubscription);
}

$systemDateTime = $clockUtils->getSystemDateTime();

$searchPattern = LibString::cleanString($searchPattern);

if ($searchPattern) {
  $validity = '';
  $lastLogin = '';
  $mailingSubscription = '';
  $smsSubscription = '';
  LibSession::putSessionValue(USER_SESSION_VALIDITY, '');
  LibSession::putSessionValue(USER_SESSION_LAST_LOGIN, '');
  LibSession::putSessionValue(USER_SESSION_MAIL_LIST_SUBSCRIPTION, '');
  LibSession::putSessionValue(USER_SESSION_SMS_LIST_SUBSCRIPTION, '');
} else if ($validity > 0) {
  $lastLogin = '';
  $mailingSubscription = '';
  $smsSubscription = '';
  LibSession::putSessionValue(USER_SESSION_LAST_LOGIN, '');
  LibSession::putSessionValue(USER_SESSION_MAIL_LIST_SUBSCRIPTION, '');
  LibSession::putSessionValue(USER_SESSION_SMS_LIST_SUBSCRIPTION, '');
} else if ($lastLogin > 0) {
  $mailingSubscription = '';
  $smsSubscription = '';
  LibSession::putSessionValue(USER_SESSION_MAIL_LIST_SUBSCRIPTION, '');
  LibSession::putSessionValue(USER_SESSION_SMS_LIST_SUBSCRIPTION, '');
} else if ($mailingSubscription > 0) {
  $smsSubscription = '';
  LibSession::putSessionValue(USER_SESSION_SMS_LIST_SUBSCRIPTION, '');
}

$nbUsers = $userUtils->countAll();

$validUntilList = array(
  '-1' => '',
  USER_ACCOUNT_NOT_VALID => $mlText[15],
  USER_ACCOUNT_VALID_TEMPORARILY => $mlText[18],
  USER_ACCOUNT_VALID_PERMANENTLY => $mlText[19],
  USER_ACCOUNT_EMAIL_UNCONFIRMED => $mlText[34],
);
$strSelectValidUntil = LibHtml::getSelectList("validity", $validUntilList, $validity, true);

$lastLoginList = array(
  '-1' => '',
  USER_LAST_LOGIN_WEEK => $mlText[39],
  USER_LAST_LOGIN_MONTH => $mlText[40],
  USER_LAST_LOGIN_QUARTER => $mlText[44],
  USER_LAST_LOGIN_SEMESTER => $mlText[45],
  USER_LAST_LOGIN_YEAR => $mlText[41],
  USER_LAST_LOGIN_MORE => $mlText[42],
);
$strSelectLastLogin = LibHtml::getSelectList("lastLogin", $lastLoginList, $lastLogin, true);

$mailingSubscriptionList = array(
  '-1' => '',
  USER_MAIL_SUBSCRIBE => $mlText[30],
  USER_MAIL_UNSUBSCRIBE => $mlText[31],
  );
$strSelectMailing = LibHtml::getSelectList("mailingSubscription", $mailingSubscriptionList, $mailingSubscription, true);

$smsSubscriptionList = array(
  '-1' => '',
  USER_SMS_SUBSCRIBE => $mlText[30],
  USER_SMS_UNSUBSCRIBE => $mlText[31],
  );
$strSelectSms = LibHtml::getSelectList("smsSubscription", $smsSubscriptionList, $smsSubscription, true);

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[6], 300, 300);
$panelUtils->setHelp($help);

$strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageImport' title='$mlText[2]'>", "$gUserUrl/import.php", 600, 600)
. " <a href='$gUserUrl/deleteImport.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[7]'></a>"
. " <a href='$gUserUrl/export.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageExport' title='$mlText[8]'></a>"
. " <a href='$gUserUrl/login_pages_admin.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageHome' title='$mlText[16]'></a>"
. " <a href='$gUserUrl/preference.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[9]'></a>";

$panelUtils->openForm($PHP_SELF);
$label = $popupUtils->getTipPopup($mlText[13], $mlText[14], 300, 300);
$strSearch = "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
. "<input type='hidden' name='searchSubmitted' value='1'> "
. $panelUtils->getTinyOk();
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell($strSearch, "n"), $panelUtils->addCell($mlText[12], "nbr"), $panelUtils->addCell($nbUsers, "n"), '', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

$validUntilLabel = $popupUtils->getTipPopup($mlText[20], $mlText[21], 300, 200);
$lastLoginLabel = $popupUtils->getTipPopup($mlText[37], $mlText[38], 300, 200);
$mailingSubscriptionLabel = $popupUtils->getTipPopup($mlText[28], $mlText[32], 300, 200);
$smsSubscriptionLabel = $popupUtils->getTipPopup($mlText[29], $mlText[33], 300, 200);
$panelUtils->addLine($panelUtils->addCell('<b>' . $validUntilLabel . '</b> ' . $strSelectValidUntil, "n"), $panelUtils->addCell($mailingSubscriptionLabel, "nbr"), $panelUtils->addCell($strSelectMailing, "n"), '', '');
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell('<b>' . $lastLoginLabel . '</b> ' . $strSelectLastLogin, "n"), $panelUtils->addCell($smsSubscriptionLabel, "nbr"), $panelUtils->addCell($strSelectSms, "n"), '', '');
$panelUtils->closeForm();
$panelUtils->addLine();

if ($validity == USER_ACCOUNT_NOT_VALID) {
  $headerValidUntil = $mlText[22];
} else if ($validity == USER_ACCOUNT_VALID_TEMPORARILY || $validity == USER_ACCOUNT_EMAIL_UNCONFIRMED) {
  $headerValidUntil = $mlText[23];
} else if ($validity == USER_ACCOUNT_VALID_PERMANENTLY) {
  $headerValidUntil = $mlText[43];
} else {
  $headerValidUntil = $mlText[43];
}

$headerLastLogin = $popupUtils->getTipPopup($mlText[35], $mlText[36], 300, 200);

if ($lastLogin == USER_LAST_LOGIN_WEEK) {
  $lastLoginSinceDate = $clockUtils->incrementWeeks($systemDateTime, -1);
  $lastLoginUntilDate = $systemDateTime;
} else if ($lastLogin == USER_LAST_LOGIN_MONTH) {
  $lastLoginSinceDate = $clockUtils->incrementWeeks($systemDateTime, -4);
  $lastLoginUntilDate = $clockUtils->incrementWeeks($systemDateTime, -1);
} else if ($lastLogin == USER_LAST_LOGIN_QUARTER) {
  $lastLoginSinceDate = $clockUtils->incrementWeeks($systemDateTime, -13);
  $lastLoginUntilDate = $clockUtils->incrementWeeks($systemDateTime, -1);
} else if ($lastLogin == USER_LAST_LOGIN_SEMESTER) {
  $lastLoginSinceDate = $clockUtils->incrementWeeks($systemDateTime, -26);
  $lastLoginUntilDate = $clockUtils->incrementWeeks($systemDateTime, -1);
} else if ($lastLogin == USER_LAST_LOGIN_YEAR) {
  $lastLoginSinceDate = $clockUtils->incrementWeeks($systemDateTime, -52);
  $lastLoginUntilDate = $clockUtils->incrementWeeks($systemDateTime, -4);
} else if ($lastLogin == USER_LAST_LOGIN_MORE) {
  $lastLoginSinceDate = $clockUtils->incrementWeeks($systemDateTime, -520);
  $lastLoginUntilDate = $clockUtils->incrementWeeks($systemDateTime, -52);
}

$strCommand = "<a href='$gUserUrl/add.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[3]'></a>";

$panelUtils->addLine($panelUtils->addCell($mlText[10], "nb"), $panelUtils->addCell($mlText[27], "nb"), $panelUtils->addCell($mlText[11], "nb"), $panelUtils->addCell($headerValidUntil, "nbc"), $panelUtils->addCell($headerLastLogin, "nbc"), $panelUtils->addCell($strCommand, "nr"));

$preferenceUtils->init($userUtils->preferences);
$listStep = $preferenceUtils->getValue("USER_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $users = $userUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else if ($validity == USER_ACCOUNT_NOT_VALID) {
  $users = $userUtils->selectNotValid($systemDateTime, $listIndex, $listStep);
} else if ($validity == USER_ACCOUNT_VALID_TEMPORARILY) {
  $users = $userUtils->selectValidTemporarily($systemDateTime, $listIndex, $listStep);
} else if ($validity == USER_ACCOUNT_VALID_PERMANENTLY) {
  $users = $userUtils->selectValidPermanently($listIndex, $listStep);
} else if ($validity == USER_ACCOUNT_EMAIL_UNCONFIRMED) {
  $users = $userUtils->selectNotYetConfirmedEmail($listIndex, $listStep);
} else if ($lastLogin > 0) {
  $users = $userUtils->selectLoggedInSince($lastLoginSinceDate, $lastLoginUntilDate, $listIndex, $listStep);
} else if ($mailingSubscription == USER_MAIL_SUBSCRIBE) {
  $users = $userUtils->searchMailSubscribersLikePattern($searchPattern, $listIndex, $listStep);
} else if ($mailingSubscription == USER_MAIL_UNSUBSCRIBE) {
  $users = $userUtils->searchNotMailSubscribersLikePattern($searchPattern, $listIndex, $listStep);
} else if ($smsSubscription == USER_SMS_SUBSCRIBE) {
  $users = $userUtils->searchSmsSubscribersLikePattern($searchPattern, $listIndex, $listStep);
} else if ($smsSubscription == USER_SMS_UNSUBSCRIBE) {
  $users = $userUtils->searchNotSmsSubscribersLikePattern($searchPattern, $listIndex, $listStep);
} else {
  $users = $userUtils->selectAll($listIndex, $listStep);
}

$listNbItems = $userUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($users as $user) {
  $userId = $user->getId();
  $firstname = $user->getFirstname();
  $lastname = $user->getLastname();
  $organisation = $user->getOrganisation();
  $email = $user->getEmail();
  $validUntil = $user->getValidUntil();
  $lastLogin = $user->getLastLogin();

  if ($clockUtils->systemDateIsSet($validUntil)) {
    $validUntil = $clockUtils->systemToLocalNumericDate($validUntil);
  } else {
    $validUntil = '';
  }

  if ($lastLogin && $clockUtils->systemDateIsSet($lastLogin)) {
    $lastLogin = $clockUtils->systemToLocalNumericDate($lastLogin);
  } else {
    $lastLogin = '';
  }

  $strEmail = "<a href='mailto:$email'>$email</a>";

  $strName = '';
  if ($firstname || $lastname) {
    $strName .= $firstname . ' ' . $lastname;
  }

  $strCommand = ''
    . " <a href='$gUserUrl/adminEditProfile.php?userId=$userId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[4]'></a>"
    . " <a href='$gUserUrl/validate.php?userId=$userId&validate=1'' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageTrue' title='$mlText[25]'></a>"
    . " <a href='$gUserUrl/validate.php?userId=$userId&invalidate=1'' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageFalse' title='$mlText[26]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[17]'>", "$gUserUrl/adminImage.php?userId=$userId", 600, 600)
    . " <a href='$gUserUrl/setPassword.php?userId=$userId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImagePassword' title='$mlText[5]'></a>"
    . " <a href='$gUserUrl/delete.php?userId=$userId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[1]'></a>";

  $panelUtils->addLine($firstname . ' ' . $lastname, $organisation, $strEmail, $panelUtils->addCell($validUntil, "nc"), $panelUtils->addCell($lastLogin, "nc"), $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("user_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
