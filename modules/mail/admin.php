<?PHP

require_once("website.php");
require_once($gMailPath . "MailOutbox.php");
require_once($gMailPath . "MailOutboxDao.php");
require_once($gMailPath . "MailOutboxDB.php");
require_once($gMailPath . "MailOutboxUtils.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
$categoryId = LibEnv::getEnvHttpPOST("categoryId");

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(MAIL_SESSION_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(MAIL_SESSION_SEARCH_PATTERN, $searchPattern);
}

if (!$categoryId) {
  $categoryId = LibSession::getSessionValue(MAIL_SESSION_CATEGORY);
} else {
  LibSession::putSessionValue(MAIL_SESSION_CATEGORY, $categoryId);
}

if ($searchPattern) {
  $categoryId = '';
  LibSession::putSessionValue(MAIL_SESSION_CATEGORY, '');
}

$searchPattern = LibString::cleanString($searchPattern);

$mailCategories = $mailCategoryUtils->selectAll();
$listCategories = Array('-1' => '');
foreach ($mailCategories as $mailCategory) {
  $wId = $mailCategory->getId();
  $wName = $mailCategory->getName();
  $listCategories[$wId] = $wName;
}
$strSelectCategory = LibHtml::getSelectList("categoryId", $listCategories, $categoryId, true);

// Create the string for the list of meta names
$strNames = '';
$metaNames = $mailUtils->getMetaNames();
foreach ($metaNames as $metaName) {
  list($name, $description) = $metaName;
  $strNames .= "<br>$name";
}

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup("$mlText[13]<br>$strNames<br><br>$mlText[14]", 300, 400);
$panelUtils->setHelp($help);

$labelSearch = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . $panelUtils->getTinyOk()
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "</form>";

// Warn if another mail is being sent
$strOngoing = '';
$strCounterJsAjax = '';
$ongoing = $mailOutboxUtils->isMailingOnGoing();
if ($ongoing) {
  $strCounterJsAjax = <<<HEREDOC
<script type='text/javascript'>
function updateNbUnsentMails(nbUnsent) {
  if (nbUnsent == '') {
    nbUnsent = '0';
  }
  document.getElementById("nbUnsentMails").innerHTML = nbUnsent;
  if (nbUnsent < 1) {
    // Update the message
    document.getElementById("ongoing").innerHTML = "<span style='font-weight:bold; color:green'>$mlText[27]</span>";
    // Stop the loop
    window.clearInterval(repeat);
  }
}
// Refresh the number of unsent mails every few seconds
var repeat = window.setInterval("ajaxAsynchronousRequest('$gMailUrl/countUnsentMails.php', updateNbUnsentMails)", 2000);
</script>
HEREDOC;
  $nbAllMails = $mailOutboxUtils->countAll();
  $nbUnsentMails = $mailOutboxUtils->countUnsent();
  $strOngoing = "<span id='ongoing'>$mlText[24] <span id='nbUnsentMails' style='font-weight:bold; color:red'>$nbUnsentMails</span> $mlText[25] $nbAllMails $mlText[26]</span>";
}

$strCommand = ''
  . " <a href='$gMailUrl/address/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[15]'></a>"
  . " <a href='$gMailUrl/list/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEmailList' title='$mlText[12]'></a>"
  . " <a href='$gMailUrl/history/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageHistory' title='$mlText[17]'></a>"
  . " <a href='$gMailUrl/result.php?mailStatus=" . MAIL_FAILED . "' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEmailResult' title='$mlText[18]'></a>"
  . " <a href='$gMailUrl/category/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCategory' title='$mlText[21]'></a>"
  . " <a href='$gMailUrl/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[16]'></a>";

$panelUtils->addLine($panelUtils->addCell($labelSearch, "nbr"), $panelUtils->addCell($strSearch, "n"), $panelUtils->addCell($strOngoing, "nr"), $panelUtils->addCell($strCommand, "nr"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[22], "nbr"), $panelUtils->addCell($strSelectCategory, "n"), '', '');
$panelUtils->closeForm();
$panelUtils->addLine();

// Display only the logged in administrator's sms messages
$adminId = '';
$preferenceUtils->init($mailUtils->preferences);
if (!$adminUtils->isLoggedSuperAdmin()) {
  $onlyAdmin = $preferenceUtils->getValue("MAIL_ONLY_ADMIN");
  if ($onlyAdmin) {
    $adminId = $adminUtils->getLoggedAdminId();
  }
}

$strCommand = "<a href='$gMailUrl/add.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nb"), $panelUtils->addCell($mlText[7], "nb"), $panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$listStep = $preferenceUtils->getValue("MAIL_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $mails = $mailUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else if ($adminId > 0 && $categoryId > 0) {
  $mails = $mailUtils->selectByAdminIdAndCategoryId($adminId, $categoryId, $listIndex, $listStep);
} else if ($adminId > 0) {
  $mails = $mailUtils->selectByAdminId($adminId, $listIndex, $listStep);
} else if ($categoryId > 0) {
  $mails = $mailUtils->selectByCategoryId($categoryId, $listIndex, $listStep);
} else {
  $mails = $mailUtils->selectAll($listIndex, $listStep);
}

$listNbItems = $mailUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
}

$panelUtils->openList();
foreach ($mails as $mail) {
  $mailId = $mail->getId();
  $subject = $mail->getSubject();
  $body = $mail->getBody();
  $description = $mail->getDescription();
  $locked = $mail->getLocked();
  $adminId = $mail->getAdminId();

  if ($admin = $adminUtils->selectById($adminId)) {
    $name = $admin->getFirstname() . ' ' . $admin->getLastname();
  } else {
    $name = '';
  }

  $strSubject = $mailUtils->renderSubject($mail);

  $strCommand = " <a href='$gMailUrl/send.php?mailId=$mailId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[4]'></a>";

  if (!$mailUtils->isLockedForLoggedInAdmin($mailId)) {
    $strCommand .= " <a href='$gMailUrl/edit.php?mailId=$mailId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[9]'></a>"
      . " <a href='$gMailUrl/edit_content.php?mailId=$mailId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageCompose' title='$mlText[2]'></a>";
  }

  $adminLogin = $adminUtils->checkAdminLogin();
  if ($adminUtils->isSuperAdmin($adminLogin)) {
    if ($locked) {
      $strCommand .= " <a href='$gMailUrl/lock.php?mailId=$mailId&locked=0' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageUnlock' title='$mlText[6]'></a>";
    } else {
      $strCommand .= " <a href='$gMailUrl/lock.php?mailId=$mailId&locked=1' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageLock' title='$mlText[10]'></a>";
    }
  }

  $strCommand .= " <a href='$gMailUrl/duplicate.php?mailId=$mailId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCopy' title='$mlText[19]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageFile' title='$mlText[20]'>", "$gMailUrl/attachment/admin.php?mailId=$mailId", 600, 600)
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[11]'>", "$gMailUrl/preview.php?mailId=$mailId", 800, 800);

  if (!$mailUtils->isLockedForLoggedInAdmin($mailId)) {
    $strCommand .= " <a href='$gMailUrl/delete.php?mailId=$mailId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";
  } else {
    $strCommand .= " <img border='0' src='$gCommonImagesUrl/$gImageTransparent' title=''>";
  }

  $panelUtils->addLine($strSubject, $name, $description, $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("mail_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str, $strCounterJsAjax);

?>
