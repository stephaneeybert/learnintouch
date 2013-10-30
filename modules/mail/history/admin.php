<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(MAIL_SESSION_ADMIN_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(MAIL_SESSION_ADMIN_SEARCH_PATTERN, $searchPattern);
}

$searchPattern = LibString::cleanString($searchPattern);

$panelUtils->setHeader($mlText[0], "$gMailUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[4], 300, 400);
$panelUtils->setHelp($help);

$labelSearch = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . $panelUtils->getTinyOk()
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "</form>";

$strCommand = "<a href='$gMailUrl/history/deleteAll.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell($labelSearch, "nbr"), $panelUtils->addCell($strSearch, "n"), '', '', '', '', '');
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nb"), $panelUtils->addCell($mlText[10], "nb"), $panelUtils->addCell($mlText[11], "nb"), $panelUtils->addCell($mlText[9], "nb"), $panelUtils->addCell($mlText[3], "nb"), $panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($strCommand, "nbr"));

$preferenceUtils->init($mailUtils->preferences);
$listStep = $preferenceUtils->getValue("MAIL_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $mailHistories = $mailHistoryUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else {
  $mailHistories = $mailHistoryUtils->selectAll($listIndex, $listStep);
}

$listNbItems = $mailHistoryUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($mailHistories as $mailHistory) {
  $mailHistoryId = $mailHistory->getId();
  $subject = $mailHistory->getSubject();
  $body = $mailHistory->getBody();
  $description = $mailHistory->getDescription();
  $attachments = $mailHistory->getAttachments();
  $mailListId = $mailHistory->getMailListId();
  $email = $mailHistory->getEmail();
  $adminId = $mailHistory->getAdminId();
  $sendDate = $mailHistory->getSendDate();

  $attachments = str_replace(':', ' ', $attachments);

  $strSubject = $popupUtils->getDialogPopup($subject, "$gMailUrl/history/preview.php?mailHistoryId=$mailHistoryId", 600, 600);

  $recipient = '';
  if ($mailListId && $mailList = $mailListUtils->selectById($mailListId)) {
    $recipient = $mailList->getName();
  } else {
    $recipient = $email;
  }

  $administrator = '';
  if ($admin = $adminUtils->selectById($adminId)) {
    $firstname = $admin->getFirstname();
    $lastname = $admin->getLastname();
    $administrator = "$firstname $lastname";
  }

  $panelUtils->addLine($strSubject, $description, $attachments, $panelUtils->addCell($sendDate, "n"), $recipient, $administrator, '');
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("mail_history_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
