<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
$newsPublicationId = LibEnv::getEnvHttpPOST("newsPublicationId");
$status = LibEnv::getEnvHttpPOST("status");

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(NEWS_SESSION_NEWSPAPER_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(NEWS_SESSION_NEWSPAPER_SEARCH_PATTERN, $searchPattern);
}

if (!$newsPublicationId) {
  $newsPublicationId = LibSession::getSessionValue(NEWS_SESSION_NEWSPUBLICATION);
} else {
  LibSession::putSessionValue(NEWS_SESSION_NEWSPUBLICATION, $newsPublicationId);
}

if (!$status) {
  $status = LibSession::getSessionValue(NEWS_SESSION_NEWSPAPER_STATUS);
} else {
  LibSession::putSessionValue(NEWS_SESSION_NEWSPAPER_STATUS, $status);
}

if ($searchPattern) {
  $newsPublicationId = '';
  $status = '';
  LibSession::putSessionValue(NEWS_SESSION_NEWSPUBLICATION, '');
  LibSession::putSessionValue(NEWS_SESSION_NEWSPAPER_STATUS, '');
}

if ($newsPublicationId < 1) {
  $status = '';
  LibSession::putSessionValue(NEWS_SESSION_NEWSPAPER_STATUS, '');
}

$searchPattern = LibString::cleanString($searchPattern);

$newsPublications = $newsPublicationUtils->selectAll();
$newsPublicationList = Array('-1' => '');
foreach ($newsPublications as $newsPublication) {
  $wId = $newsPublication->getId();
  $wName = $newsPublication->getName();
  $newsPublicationList[$wId] = $wName;
}
$strSelectNewsPublication = LibHtml::getSelectList("newsPublicationId", $newsPublicationList, $newsPublicationId, true);

$strSelectStatus = LibHtml::getSelectList("status", $newsPaperUtils->getNewsStatuses(), $status, true);

$panelUtils->setHeader($mlText[0], "$gNewsUrl/newsStory/admin.php");
$help = $popupUtils->getHelpPopup($mlText[9], 300, 500);
$panelUtils->setHelp($help);

$labelSearch = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . $panelUtils->getTinyOk()
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "</form>";

$strCommand = ''
  . " <a href='$gNewsUrl/newsPublication/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageNewspaper' title='$mlText[7]'></a>";

$panelUtils->addLine($panelUtils->addCell($labelSearch, "nbr"), $panelUtils->addCell($strSearch, "n"), $panelUtils->addCell($strCommand, "nr"));

$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[11], "nbr"), $panelUtils->addCell($strSelectNewsPublication, "n"), '');
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[10], "nbr"), $panelUtils->addCell($strSelectStatus, "n"), '');
$panelUtils->closeForm();
$panelUtils->addLine();
$strCommand = ''
  . "<a href='$gNewsUrl/newsPaper/edit.php?newsPublicationId=$newsPublicationId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";

$panelUtils->addLine($panelUtils->addCell($mlText[8], "nb"), $panelUtils->addCell($strCommand, "nbr"));

$preferenceUtils->init($newsStoryUtils->preferences);
$listStep = $preferenceUtils->getValue("NEWS_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

$newsPapers = array();
if ($searchPattern) {
  $newsPapers = $newsPaperUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else if ($newsPublicationId > 0 && $status == NEWS_STATUS_PUBLISHED) {
  $newsPapers = $newsPaperUtils->selectPublished($newsPublicationId, $listIndex, $listStep);
} else if ($newsPublicationId > 0 && $status == NEWS_STATUS_DEFERRED) {
  $newsPapers = $newsPaperUtils->selectDeferred($newsPublicationId, $listIndex, $listStep);
} else if ($newsPublicationId > 0 && $status == NEWS_STATUS_ARCHIVED) {
  $newsPapers = $newsPaperUtils->selectArchived($newsPublicationId, $listIndex, $listStep);
} else if ($newsPublicationId > 0 && $status == NEWS_STATUS_NOT_PUBLISHED) {
  $newsPapers = $newsPaperUtils->selectNotPublished($newsPublicationId, $listIndex, $listStep);
} else if ($newsPublicationId > 0) {
  $newsPapers = $newsPaperUtils->selectByNewsPublicationId($newsPublicationId, $listIndex, $listStep);
}

$listNbItems = $newsPaperUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($newsPapers as $newsPaper) {
  $newsPaperId = $newsPaper->getId();
  $title = $newsPaper->getTitle();
  $releaseDate = $newsPaper->getReleaseDate();
  $archive = $newsPaper->getArchive();

  $releaseDate = $clockUtils->systemToLocalNumericDate($releaseDate);
  $strNewsPaper = "$title<br>$releaseDate";
  if ($clockUtils->systemDateIsSet($archive)) {
    $archive = $clockUtils->systemToLocalNumericDate($archive);
    $strNewsPaper .= ' - ' . $archive;
  }

  $strCommand = ''
    . " <a href='$gNewsUrl/newsPaper/edit.php?newsPaperId=$newsPaperId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gNewsUrl/newsStory/admin.php?newsPublicationId=$newsPublicationId&newsPaperId=$newsPaperId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageNewspaper' title='$mlText[13]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[5]'>", "$gNewsUrl/newsPaper/image.php?newsPaperId=$newsPaperId", 600, 600)
    . " <a href='$gNewsUrl/newsPaper/duplicate.php?newsPaperId=$newsPaperId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCopy' title='$mlText[20]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[6]'>",
      "$gNewsUrl/newsPaper/preview.php?newsPaperId=$newsPaperId", 800, 600);
  if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_MAIL)) {
    $strCommand .= "\n<a href='$gNewsUrl/newsPaper/mail.php?newsPaperId=$newsPaperId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[12]'></a>";
  }
  $strCommand .= ''
    . "\n <a href='$gNewsUrl/newsPaper/delete.php?newsPaperId=$newsPaperId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($panelUtils->addCell($strNewsPaper, "l"), $panelUtils->addCell("$strCommand", "r"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("news_newspaper_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
