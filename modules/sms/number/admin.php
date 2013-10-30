<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
$subscribe = LibEnv::getEnvHttpPOST("subscribe");

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(NEWS_SESSION_NEWSSTORY_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(NEWS_SESSION_NEWSSTORY_SEARCH_PATTERN, $searchPattern);
}

if (!$subscribe) {
  $subscribe = LibSession::getSessionValue(SMS_SESSION_SUBSCRIBE);
} else {
  LibSession::putSessionValue(SMS_SESSION_SUBSCRIBE, $subscribe);
}

if ($searchPattern) {
  $subscribe = '';
  LibSession::putSessionValue(SMS_SESSION_SUBSCRIBE, '');
}

$searchPattern = LibString::cleanString($searchPattern);

$subscribeList = array(
  '-1' => '',
  SMS_SUBSCRIBE => $mlText[13],
  SMS_UNSUBSCRIBE => $mlText[14]
);
$strSelectSubscribe = LibHtml::getSelectList("subscribe", $subscribeList, $subscribe, true);

$nbAddress = $smsNumberUtils->countAll();

$panelUtils->setHeader($mlText[0], "$gSmsUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[4], 300, 400);
$panelUtils->setHelp($help);

$strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageImport' title='$mlText[5]'>", "$gSmsUrl/number/import.php", 600, 600)
  . " <a href='$gSmsUrl/number/deleteImport.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[7]'></a>"
  . " <a href='$gSmsUrl/number/export.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageExport' title='$mlText[11]'></a>";

$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . $panelUtils->getTinyOk()
  . "</form>";

$labelStatus = $popupUtils->getTipPopup($mlText[17], $mlText[18], 300, 300);
$labelSearch = $popupUtils->getTipPopup($mlText[9], $mlText[10], 300, 300);
$panelUtils->addLine($panelUtils->addCell($labelSearch, "nbr"), $strSearch, $panelUtils->addCell($mlText[6], "nbr"), $panelUtils->addCell($nbAddress, "n"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($labelStatus, "nbr"), $panelUtils->addCell($strSelectSubscribe, "n"), '', '', '');
$panelUtils->closeForm();
$panelUtils->addLine();

$strCommand = "<a href='$gSmsUrl/number/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";

$panelUtils->addLine($panelUtils->addCell($mlText[12], "nb"), $panelUtils->addCell($mlText[15], "nb"), $panelUtils->addCell($mlText[8], "nb"), '', $panelUtils->addCell($strCommand, "nbr"));

$preferenceUtils->init($smsUtils->preferences);
$listStep = $preferenceUtils->getValue("SMS_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $smsNumbers = $smsNumberUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else if ($subscribe == SMS_SUBSCRIBE) {
  $smsNumbers = $smsNumberUtils->selectSubscribers($listIndex, $listStep);
} else if ($subscribe == SMS_UNSUBSCRIBE) {
  $smsNumbers = $smsNumberUtils->selectNonSubscribers($listIndex, $listStep);
} else {
  $smsNumbers = $smsNumberUtils->selectAll($listIndex, $listStep);
}

$listNbItems = $smsNumberUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($smsNumbers as $smsNumber) {
  $smsNumberId = $smsNumber->getId();
  $mobilePhone = $smsNumber->getMobilePhone();
  $firstname = $smsNumber->getFirstname();
  $lastname = $smsNumber->getLastname();

  $strCommand = ''
    . " <a href='$gSmsUrl/number/edit.php?smsNumberId=$smsNumberId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gSmsUrl/number/delete.php?smsNumberId=$smsNumberId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($firstname, $lastname, $mobilePhone, '', $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("sms_number_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
