<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(MAIL_SESSION_LIST_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(MAIL_SESSION_LIST_SEARCH_PATTERN, $searchPattern);
}

$searchPattern = LibString::cleanString($searchPattern);

$panelUtils->setHeader($mlText[0], "$gMailUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[4], 300, 400);
$panelUtils->setHelp($help);

$label = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<b>$label</b> <input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
. $panelUtils->getTinyOk()
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "</form>";

$strCommand = "<a href='$gMailUrl/address/admin.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[15]'></a>";

$panelUtils->addLine($panelUtils->addCell($strSearch, "n"), $panelUtils->addCell($strCommand, "nbr"));

$strCommand = "<a href='$gMailUrl/list/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";

$panelUtils->addLine($panelUtils->addCell($mlText[8], "nb"), $panelUtils->addCell($strCommand, "nbr"));

$preferenceUtils->init($mailUtils->preferences);
$listStep = $preferenceUtils->getValue("MAIL_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $mailLists = $mailListUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else {
  $mailLists = $mailListUtils->selectAll($listIndex, $listStep);
}

$listNbItems = $mailListUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($mailLists as $mailList) {
  $mailListId = $mailList->getId();
  $name = $mailList->getName();

  $strCommand = ''
    . " <a href='$gMailUrl/send.php?mailListId=$mailListId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[6]'></a>"
    . " <a href='$gMailUrl/list/compose.php?mailListId=$mailListId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDesign' title='$mlText[5]'></a>"
    . " <a href='$gMailUrl/list/edit.php?mailListId=$mailListId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gMailUrl/list/delete.php?mailListId=$mailListId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("mail_list_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
