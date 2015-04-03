<?PHP

require_once("website.php");
require_once($gMailPath . "MailOutbox.php");
require_once($gMailPath . "MailOutboxDao.php");
require_once($gMailPath . "MailOutboxDB.php");
require_once($gMailPath . "MailOutboxUtils.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$nbFailed = $mailOutboxUtils->countFailed();

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$mailStatus = LibEnv::getEnvHttpPOST("mailStatus");

if (!$mailStatus) {
  $mailStatus = LibEnv::getEnvHttpGET("mailStatus");
}

if (!$mailStatus) {
  $mailStatus = LibSession::getSessionValue(MAIL_SESSION_STATUS);
} else {
  LibSession::putSessionValue(MAIL_SESSION_STATUS, $mailStatus);
}

$searchPattern = LibString::cleanString($searchPattern);

if ($searchPattern) {
  $mailStatus = '';
  LibSession::putSessionValue(MAIL_SESSION_STATUS, '');
}

if (!$mailStatus && !$searchPattern) {
  if ($nbFailed > 0) {
    $mailStatus = MAIL_FAILED;
  } else {
    $mailStatus = MAIL_SENT;
  }
}

$panelUtils->setHeader($mlText[0], "$gMailUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[1], 300, 440);
$panelUtils->setHelp($help);

$mailStatusList = array(
  '-1' => '',
  MAIL_FAILED => $mlText[10],
  MAIL_SENT => $mlText[11],
  MAIL_ALL => $mlText[12],
);
$strSelectPublished = LibHtml::getSelectList("mailStatus", $mailStatusList, $mailStatus, true);

$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . $panelUtils->getTinyOk()
  . "</form>";

// Display a message to confirm that the email has been sent to all the
// email addresses of the list or to warn that the email has failed being
// sent to some of the email addresses
if ($mailStatus == MAIL_FAILED && $nbFailed > 0) {
  $resultMessage = $mlText[4];
} else if ($mailStatus == MAIL_SENT && $nbFailed > 0) {
  $resultMessage = $mlText[6];
} else {
  $resultMessage = '';
}

$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $panelUtils->addCell($strSearch, "n"), '');
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), $panelUtils->addCell($strSelectPublished, "n"), $panelUtils->addCell($resultMessage, "n"));
$panelUtils->addLine();

$panelUtils->addLine($panelUtils->addCell($mlText[2], "nb"), $panelUtils->addCell($mlText[3], "nb"), $panelUtils->addCell($mlText[13], "nb"));

$preferenceUtils->init($mailUtils->preferences);
$listStep = $preferenceUtils->getValue("MAIL_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $mailOutboxes = $mailOutboxUtils->selectLikePattern($searchPattern);
} else if ($mailStatus == MAIL_FAILED) {
  $mailOutboxes = $mailOutboxUtils->selectUnsent($listIndex, $listStep);
} else if ($mailStatus == MAIL_SENT) {
  $mailOutboxes = $mailOutboxUtils->selectSent($listIndex, $listStep);
} else if ($mailStatus == MAIL_ALL) {
  $mailOutboxes = $mailOutboxUtils->selectAll($listIndex, $listStep);
} else if ($nbFailed > 0) {
  $mailOutboxes = $mailOutboxUtils->selectUnsent($listIndex, $listStep);
} else {
  $mailOutboxes = $mailOutboxUtils->selectSent($listIndex, $listStep);
}

$listNbItems = $mailOutboxUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($mailOutboxes as $mailOutbox) {
  $firstname = $mailOutbox->getFirstname();
  $lastname = $mailOutbox->getLastname();
  $email = $mailOutbox->getEmail();
  $errorMessage = $mailOutbox->getErrorMessage();

  $panelUtils->addLine("$firstname $lastname", $email, $errorMessage);
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
