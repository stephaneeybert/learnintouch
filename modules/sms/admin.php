<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
$categoryId = LibEnv::getEnvHttpPOST("categoryId");

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(SMS_SESSION_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(SMS_SESSION_SEARCH_PATTERN, $searchPattern);
}

if (!$categoryId) {
  $categoryId = LibSession::getSessionValue(SMS_SESSION_CATEGORY);
} else {
  LibSession::putSessionValue(SMS_SESSION_CATEGORY, $categoryId);
}

$searchPattern = LibString::cleanString($searchPattern);

if ($searchPattern) {
  $categoryId = '';
  LibSession::putSessionValue(SMS_SESSION_CATEGORY, '');
}

$smsCategories = $smsCategoryUtils->selectAll();
$listCategories = Array('-1' => '');
foreach ($smsCategories as $smsCategory) {
  $wId = $smsCategory->getId();
  $wName = $smsCategory->getName();
  $listCategories[$wId] = $wName;
}
$strSelectCategory = LibHtml::getSelectList("categoryId", $listCategories, $categoryId, true);

$strNames = '';
$metaNames = $smsUtils->getMetaNames();
foreach ($metaNames as $metaName) {
  list($name, $phpVariable, $description) = $metaName;
  $strNames .= "<br>$name";
}

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup("$mlText[13]<br>$strNames<br><br>$mlText[14]", 300, 400);
$panelUtils->setHelp($help);

$strCommand = ''
  . " <a href='$gSmsUrl/number/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[15]'></a>"
  . " <a href='$gSmsUrl/list/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEmailList' title='$mlText[12]'></a>"
  ." <a href='$gSmsUrl/history/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageHistory' title='$mlText[17]'></a>"
  ." <a href='$gSmsUrl/balance.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageVolume' title='$mlText[9]'></a>"
  . " <a href='$gSmsUrl/category/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCategory' title='$mlText[21]'></a>"
  . " <a href='$gSmsUrl/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[16]'></a>";

$label = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . $panelUtils->getTinyOk()
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "</form>";
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSearch, '', $panelUtils->addCell($strCommand, "nr"));

$panelUtils->addLine($panelUtils->addCell($mlText[22], "nbr"), $panelUtils->addCell($strSelectCategory, "n"), '', '');

$strCommand = "<a href='$gSmsUrl/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nb"), $panelUtils->addCell($mlText[8], "nb"), '', $panelUtils->addCell($strCommand, "nbr"));

if (!$adminUtils->isLoggedSuperAdmin()) {
  $adminId = $adminUtils->getLoggedAdminId();
} else {
  $adminId = '';
}

$preferenceUtils->init($smsUtils->preferences);
$listStep = $preferenceUtils->getValue("SMS_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $smss = $smsUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else if ($adminId > 0 && $categoryId > 0) {
  $smss = $smsUtils->selectByAdminIdAndCategoryId($adminId, $categoryId, $listIndex, $listStep);
} else if ($adminId > 0) {
  $smss = $smsUtils->selectByAdminId($adminId, $listIndex, $listStep);
} else if ($categoryId > 0) {
  $smss = $smsUtils->selectByCategoryId($categoryId, $listIndex, $listStep);
} else {
  $smss = $smsUtils->selectAll($listIndex, $listStep);
}

$listNbItems = $smsUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($smss as $sms) {
  $smsId = $sms->getId();
  $body = $sms->getBody();
  $description = $sms->getDescription();

  $strBody = LibString::wordSubtract($body, 6) . ' ...';

  $strCommand = ''
    . " <a href='$gSmsUrl/send.php?smsId=$smsId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageSms' title='$mlText[4]'></a>"
    . " <a href='$gSmsUrl/edit.php?smsId=$smsId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gSmsUrl/delete.php?smsId=$smsId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($description, $strBody, '', $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("sms_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
