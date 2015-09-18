<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
$newsPublicationId = LibEnv::getEnvHttpPOST("newsPublicationId");
$newsPaperId = LibEnv::getEnvHttpPOST("newsPaperId");
$status = LibEnv::getEnvHttpPOST("status");
$newsHeadingId = LibEnv::getEnvHttpPOST("newsHeadingId");
$newsEditorId = LibEnv::getEnvHttpPOST("newsEditorId");

if (!$newsPublicationId) {
  $newsPublicationId = LibEnv::getEnvHttpGET("newsPublicationId");
}

if (!$newsPaperId) {
  $newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");
}

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(NEWS_SESSION_NEWSSTORY_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(NEWS_SESSION_NEWSSTORY_SEARCH_PATTERN, $searchPattern);
}

if (!$newsPublicationId) {
  $newsPublicationId = LibSession::getSessionValue(NEWS_SESSION_NEWSPUBLICATION);
} else {
  LibSession::putSessionValue(NEWS_SESSION_NEWSPUBLICATION, $newsPublicationId);
}

if (!$newsPaperId) {
  $newsPaperId = LibSession::getSessionValue(NEWS_SESSION_NEWSPAPER);
} else {
  LibSession::putSessionValue(NEWS_SESSION_NEWSPAPER, $newsPaperId);
}

if (!$status) {
  $status = LibSession::getSessionValue(NEWS_SESSION_NEWSPAPER_STATUS);
} else {
  LibSession::putSessionValue(NEWS_SESSION_NEWSPAPER_STATUS, $status);
}

if (!$newsHeadingId) {
  $newsHeadingId = LibSession::getSessionValue(NEWS_SESSION_NEWSHEADING);
} else {
  LibSession::putSessionValue(NEWS_SESSION_NEWSHEADING, $newsHeadingId);
}

if (!$newsEditorId) {
  $newsEditorId = LibSession::getSessionValue(NEWS_SESSION_NEWSEDITOR);
} else {
  LibSession::putSessionValue(NEWS_SESSION_NEWSEDITOR, $newsEditorId);
}

if ($searchPattern) {
  $newsPublicationId = '';
  $newsPaperId = '';
  $status = '';
  $newsHeadingId = '';
  $newsEditorId = '';
  LibSession::putSessionValue(NEWS_SESSION_NEWSPUBLICATION, '');
  LibSession::putSessionValue(NEWS_SESSION_NEWSPAPER, '');
  LibSession::putSessionValue(NEWS_SESSION_NEWSPAPER_STATUS, '');
  LibSession::putSessionValue(NEWS_SESSION_NEWSHEADING, '');
  LibSession::putSessionValue(NEWS_SESSION_NEWSEDITOR, '');
} else if ($status > 0) {
  $newsHeadingId = '';
  $newsEditorId = '';
  LibSession::putSessionValue(NEWS_SESSION_NEWSHEADING, '');
  LibSession::putSessionValue(NEWS_SESSION_NEWSEDITOR, '');
}

if ($newsPublicationId < 1 || $newsPaperId < 1) {
  $status = '';
  $newsHeadingId = '';
  $newsEditorId = '';
  LibSession::putSessionValue(NEWS_SESSION_NEWSPAPER_STATUS, '');
  LibSession::putSessionValue(NEWS_SESSION_NEWSHEADING, '');
  LibSession::putSessionValue(NEWS_SESSION_NEWSEDITOR, '');
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

$newsPapers = $newsPaperUtils->selectByNewsPublicationId($newsPublicationId);
$newsPaperList = Array('-1' => '');
foreach ($newsPapers as $newsPaper) {
  $wId = $newsPaper->getId();
  $title = $newsPaper->getTitle();
  $wReleaseDate = $newsPaper->getReleaseDate();
  $wTitle = LibString::wordSubtract($title, 6);
  if (strlen($wTitle) < $title) {
    $wTitle .= ' ...';
  }
  $newsPaperList[$wId] = $wReleaseDate . ' ' . $wTitle;
}
$strSelectNewsPaper = LibHtml::getSelectList("newsPaperId", $newsPaperList, $newsPaperId, true);

$newsHeadingList = Array('-1' => '', '-2' => $mlText[29]);
$newsHeadings = $newsHeadingUtils->selectByNewsPublicationId($newsPublicationId);
foreach ($newsHeadings as $newsHeading) {
  $wId = $newsHeading->getId();
  $name = $newsHeading->getName();
  $newsHeadingList[$wId] = $name;
}
$strSelectNewsHeading = LibHtml::getSelectList("newsHeadingId", $newsHeadingList, $newsHeadingId, true);

$newsEditorList = Array('-1' => '');
$newsEditors = $newsEditorUtils->selectAll();

// If no editor is yet selected then get it from the logged in admin
if (count($newsEditors) > 0) {
  if (!$newsEditorId) {
    $adminId = $adminUtils->getLoggedAdminId();
    if ($adminId) {
      if ($newsEditor = $newsEditorUtils->selectByAdminId($adminId)) {
        $newsEditorId = $newsEditor->getId();
      }
    }
  }
}
foreach ($newsEditors as $newsEditor) {
  $wId = $newsEditor->getId();
  $firstname = $newsEditorUtils->getFirstname($wId);
  $lastname = $newsEditorUtils->getLastname($wId);
  $newsEditorList[$wId] = "$firstname $lastname";
}
$strSelectNewsEditor = LibHtml::getSelectList("newsEditorId", $newsEditorList, $newsEditorId, true);

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[10], 300, 300);
$panelUtils->setHelp($help);

$labelSearch = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . $panelUtils->getTinyOk()
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "</form>";

$strCommand = ''
  . " <a href='$gNewsUrl/newsPaper/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageNewspaper' title='$mlText[15]'></a>"
  . " <a href='$gNewsUrl/newsHeading/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageHeading' title='$mlText[14]'></a>"
  . " <a href='$gNewsUrl/newsEditor/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImagePerson' title='$mlText[16]'></a>"
  . " <a href='$gNewsUrl/newsStory/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[9]'></a>";

$panelUtils->addLine($panelUtils->addCell($labelSearch, "nbr"), $panelUtils->addCell($strSearch, "n"), '', '', $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[21], "nbr"), $panelUtils->addCell($strSelectNewsPublication, "n"), $panelUtils->addCell($mlText[5], "nbr"), $panelUtils->addCell($strSelectNewsPaper, "n"), '');
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[24], $mlText[25], 300, 300);
$strSelectStatus = LibHtml::getSelectList("status", $newsPaperUtils->getNewsStatuses(), $status, true);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectStatus, $panelUtils->addCell($mlText[6], "nbr"), $strSelectNewsHeading, '');
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $panelUtils->addCell($strSelectNewsEditor, "n"), '', '', '');
$panelUtils->closeForm();
$panelUtils->addLine();

$strCommandNewsHeadingUrl = "$gNewsUrl/newsStory/edit.php?newsPublicationId=$newsPublicationId&newsPaperId=$newsPaperId"; 
if ($newsHeadingId > 0) {
  $strCommandNewsHeadingUrl .= "&newsHeadingId=$newsHeadingId";
}

$strCommand = ''
  . " <a href='$strCommandNewsHeadingUrl' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>"
  . " <a href='$gNewsUrl/newsPaper/edit.php?newsPaperId=$newsPaperId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[23]'></a>";

if ($newsPaperId > 0) {
  $strCommand .= ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[18]'>", "$gNewsUrl/newsPaper/preview.php?newsPaperId=$newsPaperId", 800, 600);
}

$systemDate = $clockUtils->getSystemDate();

$panelUtils->addLine($panelUtils->addCell($mlText[19], "nb"), $panelUtils->addCell($mlText[8], "nb"), '', '',  $panelUtils->addCell($strCommand, "nr"));

$sortableLinesClass = 'sortableLines_' . $newsHeadingId;
$strSortableLines = <<<HEREDOC
<script type="text/javascript">
$(document).ready(function() {
  $("tbody .$sortableLinesClass").sortable({
    cursor: 'move',
    update: function(ev, ui) {
      var sortableItemIds = [];
      $("tbody .$sortableLinesClass .sortableItem").each(function(index){
        var sortableItemId = $(this).attr("sortableItemId");
        sortableItemIds.push(sortableItemId);
      });
      $.post('$gNewsUrl/newsStory/list_order.php', {'newsStoryIds[]' : sortableItemIds}, function(data){
      });
    }
  }).disableSelection();
});
</script>
HEREDOC;
$panelUtils->addContent($strSortableLines);

$listStep = $preferenceUtils->getValue("NEWS_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

$newsStories = array();
if ($searchPattern) {
  $newsStories = $newsStoryUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else if ($newsPaperId > 0 && $status == NEWS_STATUS_ARCHIVED) {
  $newsStories = $newsStoryUtils->selectByNewsPaperAndArchived($newsPaperId, $listStep, $listIndex);
} else if ($newsPaperId > 0 && $status == NEWS_STATUS_PUBLISHED) {
  $newsStories = $newsStoryUtils->selectByNewsPaperAndPublished($newsPaperId, $systemDate, $listStep, $listIndex);
} else if ($newsPaperId > 0 && $status == NEWS_STATUS_DEFERRED) {
  $newsStories = $newsStoryUtils->selectByNewsPaperAndDeferred($newsPaperId, $systemDate, $listStep, $listIndex);
} else if ($newsPaperId > 0 && $newsHeadingId > 0 && $newsEditorId > 0) {
  $newsStories = $newsStoryUtils->selectByNewsPaperAndNewsHeadingAndNewsEditor($newsPaperId, $newsHeadingId, $newsEditorId, $listStep, $listIndex);
} else if ($newsPaperId > 0 && $newsHeadingId > 0) {
  $newsStories = $newsStoryUtils->selectByNewsPaperAndNewsHeading($newsPaperId, $newsHeadingId, $listStep, $listIndex);
} else if ($newsPaperId > 0 && $newsEditorId > 0) {
  $newsStories = $newsStoryUtils->selectByNewsPaperAndNewsEditor($newsPaperId, $newsEditorId, $listStep, $listIndex);
} else if ($newsPaperId > 0) {
  $newsStories = $newsStoryUtils->selectByNewsPaper($newsPaperId, $listStep, $listIndex);
}

$listNbItems = $newsStoryUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList($sortableLinesClass);
foreach ($newsStories as $newsStory) {
  $newsStoryId = $newsStory->getId();
  $newsHeadingId = $newsStory->getNewsHeading();
  $headline = $newsStory->getHeadline();
  $link = $newsStory->getLink();
  $releaseDate = $newsStory->getReleaseDate();

  $releaseDate = $clockUtils->systemToLocalNumericDate($releaseDate);

  $strHeading = '';
  if ($newsHeadingId) {
    $newsHeading = $newsHeadingUtils->selectById($newsHeadingId);
    $name = $newsHeading->getName();
    $description = $newsHeading->getDescription();

    $strHeading = "<a href='$gNewsUrl/newsHeading/edit.php?newsHeadingId=$newsHeadingId' $gJSNoStatus title='$description'>$name</a>";
  }

  $strSortable = "<span class='sortableItem' sortableItemId='$newsStoryId'></span>";

  $strSwap = ''
    . " <a href='$gNewsUrl/newsStory/swapup.php?newsStoryId=$newsStoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[13]'></a>"
    . " <a href='$gNewsUrl/newsStory/swapdown.php?newsStoryId=$newsStoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[12]'></a>";

  $strCommand = ''
    . " <a href='$gNewsUrl/newsStory/edit.php?newsStoryId=$newsStoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gNewsUrl/newsStory/edit_content.php?newsStoryId=$newsStoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDesign' title='$mlText[22]'></a>"
    . " <a href='$gNewsUrl/newsStory/duplicate.php?newsStoryId=$newsStoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCopy' title='$mlText[20]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[17]'>", "$gNewsUrl/newsStory/preview.php?newsStoryId=$newsStoryId", 800, 600)
    . " <a href='$gNewsUrl/newsStory/image/admin.php?newsStoryId=$newsStoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[4]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$mlText[11]'>", "$gNewsUrl/newsStory/audio.php?newsStoryId=$newsStoryId", 600, 600)
    . " <a href='$gNewsUrl/newsStory/delete.php?newsStoryId=$newsStoryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($strHeading, "$strSortable $strSwap $headline <br>$releaseDate", '', '', $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("news_newsstory_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$strListOrderDragAndDrop = <<<HEREDOC
<script type="text/javascript">
$(document).ready(function() {
});
</script>
HEREDOC;
$panelUtils->addContent($strListOrderDragAndDrop);

$str = $panelUtils->render();

printAdminPage($str);

?>
