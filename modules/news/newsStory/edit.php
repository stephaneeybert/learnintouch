<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $newsStoryId = LibEnv::getEnvHttpPOST("newsStoryId");
  $headline = LibEnv::getEnvHttpPOST("headline");
  $webpageId = LibEnv::getEnvHttpPOST("webpageId");
  $webpageName = LibEnv::getEnvHttpPOST("webpageName");
  $externalUrl = LibEnv::getEnvHttpPOST("externalUrl");
  $audioUrl = LibEnv::getEnvHttpPOST("audioUrl");
  $releaseDate = LibEnv::getEnvHttpPOST("releaseDate");
  $archive = LibEnv::getEnvHttpPOST("archive");
  $eventStartDate = LibEnv::getEnvHttpPOST("eventStartDate");
  $eventEndDate = LibEnv::getEnvHttpPOST("eventEndDate");
  $newsEditorId = LibEnv::getEnvHttpPOST("newsEditorId");
  $newsPaperId = LibEnv::getEnvHttpPOST("newsPaperId");
  $newsHeadingId = LibEnv::getEnvHttpPOST("newsHeadingId");
  $targetNewsStoryId = LibEnv::getEnvHttpPOST("targetNewsStoryId");

  $headline = LibString::cleanString($headline);
  $webpageName = LibString::cleanString($webpageName);
  $externalUrl = LibString::cleanString($externalUrl);
  $audioUrl = LibString::cleanString($audioUrl);
  $releaseDate = LibString::cleanString($releaseDate);
  $archive = LibString::cleanString($archive);
  $eventStartDate = LibString::cleanString($eventStartDate);
  $eventEndDate = LibString::cleanString($eventEndDate);

  // The headline is required
  if (!$headline) {
    array_push($warnings, $mlText[4]);
  }

  // The newspaper is required
  if (!$newsPaperId) {
    array_push($warnings, $mlText[11]);
  }

  // Validate the release date
  if ($releaseDate && !$clockUtils->isLocalNumericDateValid($releaseDate)) {
    array_push($warnings, $mlText[21] . ' ' . $clockUtils->getDateNumericFormatTip());
  }

  // Validate the archive date
  if ($archive && !$clockUtils->isLocalNumericDateValid($archive)) {
    array_push($warnings, $mlText[21] . ' ' . $clockUtils->getDateNumericFormatTip());
  }

  // Validate the event dates
  if ($eventStartDate && !$clockUtils->isLocalNumericDateValid($eventStartDate)) {
    array_push($warnings, $mlText[21] . ' ' . $clockUtils->getDateNumericFormatTip());
  }
  if ($eventEndDate && !$clockUtils->isLocalNumericDateValid($eventEndDate)) {
    array_push($warnings, $mlText[21] . ' ' . $clockUtils->getDateNumericFormatTip());
  }

  if ($eventStartDate && !$eventEndDate) {
    $eventEndDate = $eventStartDate;
  }

  if ($releaseDate) {
    $releaseDate = $clockUtils->localToSystemDate($releaseDate);
  } else {
    $releaseDate = $clockUtils->getSystemDate();
  }

  if ($archive) {
    $archive = $clockUtils->localToSystemDate($archive);
  }

  if ($eventStartDate) {
    $eventStartDate = $clockUtils->localToSystemDate($eventStartDate);
  }
  if ($eventEndDate) {
    $eventEndDate = $clockUtils->localToSystemDate($eventEndDate);
  }

  // The archive date must be after the release date
  if ($archive && $releaseDate && $clockUtils->systemDateIsGreater($releaseDate, $archive)) {
    array_push($warnings, $mlText[14]);
  }

  // The event end date must be after the event start date
  if ($eventEndDate && $eventStartDate && $clockUtils->systemDateIsGreater($eventStartDate, $eventEndDate)) {
    array_push($warnings, $mlText[34]);
  }

  // Push back the release before the start of event if any
  if ($eventStartDate && $releaseDate && $clockUtils->systemDateIsGreater($releaseDate, $eventStartDate)) {
    $releaseDate = $eventStartDate;
  }

  // Delay the archiving after the end of event if any
  if ($eventEndDate && $archive && $clockUtils->systemDateIsGreater($eventEndDate, $archive)) {
    $archive = $eventEndDate;
  }

  // Validate the url
  // The url can be a web address or an email address
  if ($externalUrl && LibUtils::isInvalidUrl($externalUrl) && !LibEmail::validate($externalUrl)) {
    array_push($warnings, $mlText[22]);
  }

  // Format the url
  if ($externalUrl && !LibEmail::validate($externalUrl)) {
    $externalUrl = LibUtils::formatUrl($externalUrl);
  }

  // Clear the page if necessary
  if (!$webpageName) {
    $webpageId = '';
  }

  // If a web page or a system page has been selected then use it
  if ($webpageId) {
    $url = $webpageId;
  } else if ($externalUrl) {
    $url = $externalUrl;
  } else {
    $url = '';
  }

  // If the news story is assigned to another newspaper and/or news heading
  // then the news story list order must be set according to the newspaper and
  // news heading number of news stories
  // Otherwise check if the news story list order has been changed

  if ($newsStory = $newsStoryUtils->selectById($newsStoryId)) {
    $listOrder = $newsStory->getListOrder();
    $currentNewsPaperId = $newsStory->getNewsPaper();
    $currentNewsHeadingId = $newsStory->getNewsHeading();
  } else {
    $currentNewsPaperId = '';
    $currentNewsHeadingId = '';
  }

  // Get the next news story id to check if the news story list order has been changed
  if ($nextNewsStory = $newsStoryUtils->selectNext($newsStoryId)) {
    $nextNewsStoryId = $nextNewsStory->getId();
  } else {
    $nextNewsStoryId = '';
  }

  if (count($warnings) == 0) {

    if ($newsStory = $newsStoryUtils->selectById($newsStoryId)) {
      $newsStory->setHeadline($headline);
      $newsStory->setLink($url);
      $newsStory->setAudioUrl($audioUrl);
      $newsStory->setReleaseDate($releaseDate);
      $newsStory->setArchive($archive);
      $newsStory->setEventStartDate($eventStartDate);
      $newsStory->setEventEndDate($eventEndDate);
      $newsStory->setNewsPaper($newsPaperId);
      $newsStory->setNewsHeading($newsHeadingId);
      $newsStory->setNewsEditor($newsEditorId);
      $newsStory->setListOrder($listOrder);
      $newsStoryUtils->update($newsStory);
    } else {
      $newsStory = new NewsStory();
      $newsStory->setHeadline($headline);
      $newsStory->setLink($url);
      $newsStory->setAudioUrl($audioUrl);
      $newsStory->setReleaseDate($releaseDate);
      $newsStory->setArchive($archive);
      $newsStory->setEventStartDate($eventStartDate);
      $newsStory->setEventEndDate($eventEndDate);
      $newsStory->setNewsPaper($newsPaperId);
      $newsStory->setNewsHeading($newsHeadingId);
      $newsStory->setNewsEditor($newsEditorId);
      $newsStoryUtils->insert($newsStory);
      $newsStoryId = $newsStoryUtils->getLastInsertId();

      // Add a paragraph to the news story
      if ($newsStoryId) {
        $newsStoryParagraph = new NewsStoryParagraph();
        $newsStoryParagraph->setNewsStoryId($newsStoryId);
        $newsStoryParagraphUtils->insert($newsStoryParagraph);
      }
    }

    if ($targetNewsStoryId) {
      $listOrder = $newsStoryUtils->placeBefore($newsStoryId, $targetNewsStoryId);
    } else {
      $listOrder = $newsStoryUtils->placeFirst($newsStoryId);
    }

    $str = LibHtml::urlRedirect("$gNewsUrl/newsStory/edit_content.php?newsStoryId=$newsStoryId");
    printContent($str);
    return;

  }

} else {

  $newsStoryId = LibEnv::getEnvHttpGET("newsStoryId");
  if (!$newsStoryId) {
    $newsStoryId = LibEnv::getEnvHttpPOST("newsStoryId");
  }

  $newsPublicationId = LibEnv::getEnvHttpGET("newsPublicationId");
  $newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");
  $newsHeadingId = LibEnv::getEnvHttpGET("newsHeadingId");

  $headline = '';
  $url = '';
  $audioUrl = '';
  $webpageId = '';
  $webpageName = '';
  $releaseDate = '';
  $archive = '';
  $eventStartDate = '';
  $eventEndDate = '';
  $newsEditorId = '';
  if ($newsStoryId) {
    if ($newsStory = $newsStoryUtils->selectById($newsStoryId)) {
      $headline = $newsStory->getHeadline();
      $url = $newsStory->getLink();
      $audioUrl = $newsStory->getAudioUrl();
      $releaseDate = $newsStory->getReleaseDate();
      $archive = $newsStory->getArchive();
      $eventStartDate = $newsStory->getEventStartDate();
      $eventEndDate = $newsStory->getEventEndDate();
      $newsEditorId = $newsStory->getNewsEditor();
      $newsPaperId = $newsStory->getNewsPaper();
      $newsHeadingId = $newsStory->getNewsHeading();
    }
  }

}

$webpageName = $templateUtils->getPageName($url);
if ($webpageName) {
  $externalUrl = '';
  $webpageId = $url;
} else {
  $externalUrl = $url;
}

if (!$clockUtils->systemDateIsSet($releaseDate)) {
  $releaseDate = $clockUtils->getSystemDate();
}

$releaseDate = $clockUtils->systemToLocalNumericDate($releaseDate);

if ($clockUtils->systemDateIsSet($archive)) {
  $archive = $clockUtils->systemToLocalNumericDate($archive);
} else {
  $archive = '';
}

$eventStartDate = $clockUtils->systemToLocalNumericDate($eventStartDate);

if ($clockUtils->systemDateIsSet($eventEndDate)) {
  $eventEndDate = $clockUtils->systemToLocalNumericDate($eventEndDate);
} else {
  $eventEndDate = '';
}

if ($currentNewsPaper = $newsPaperUtils->selectById($newsPaperId)) {
  $newsPublicationId = $currentNewsPaper->getNewsPublicationId();
}

// Do not overwrite the current properties
if ($formSubmitted == 2) {
  $headline = LibEnv::getEnvHttpPOST("headline");
  $headline = LibString::cleanString($headline);
  $newsPublicationId = LibEnv::getEnvHttpPOST("newsPublicationId");
}

if ($formSubmitted == 3) {
  $headline = LibEnv::getEnvHttpPOST("headline");
  $headline = LibString::cleanString($headline);
  $newsPublicationId = LibEnv::getEnvHttpPOST("newsPublicationId");
  $newsPaperId = LibEnv::getEnvHttpPOST("newsPaperId");
  $newsHeadingId = LibEnv::getEnvHttpPOST("newsHeadingId");
}

$newsPublications = $newsPublicationUtils->selectAll();
$newsPublicationList = Array('' => '');
foreach ($newsPublications as $newsPublication) {
  $wId = $newsPublication->getId();
  $wName = $newsPublication->getName();
  $newsPublicationList[$wId] = $wName;
}
$strSelectNewsPublication = LibHtml::getSelectList("newsPublicationId", $newsPublicationList, $newsPublicationId, true);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");

$newsPaperList = Array('' => '');
if ($searchPattern && $newsPublicationId) {
  $newsPapers = $newsPaperUtils->selectByPatternAndNewsPublicationId($searchPattern, $newsPublicationId);
} else if ($searchPattern) {
  $newsPapers = $newsPaperUtils->selectLikePattern($searchPattern);
} else {
  $newsPapers = $newsPaperUtils->selectByNewsPublicationId($newsPublicationId);
}
foreach ($newsPapers as $newsPaper) {
  $wId = $newsPaper->getId();
  $wTitle = $newsPaper->getTitle();
  $wReleaseDate = $newsPaper->getReleaseDate();
  $wReleaseDate = $clockUtils->systemToLocalNumericDate($wReleaseDate);
  $newsPaperList[$wId] = "$wTitle $wReleaseDate";
}
$strSelectNewsPaper = LibHtml::getSelectList("newsPaperId", $newsPaperList, $newsPaperId, true);

$newsHeadingList = Array('' => '');
$preferenceUtils->init($newsStoryUtils->preferences);
$shareHeadings = $preferenceUtils->getValue("NEWS_SHARE_HEADINGS");
if ($shareHeadings) {
  $newsHeadings = $newsHeadingUtils->selectAll();
} else {
  $newsHeadings = $newsHeadingUtils->selectByNewsPublicationId($newsPublicationId);
}
foreach ($newsHeadings as $newsHeading) {
  $wId = $newsHeading->getId();
  $name = $newsHeading->getName();
  $newsHeadingList[$wId] = "$name";
}
$strSelectNewsHeading = LibHtml::getSelectList("newsHeadingId", $newsHeadingList, $newsHeadingId, true);

// Create the editors html select list
$newsEditorList = Array('' => '');
$newsEditors = $newsEditorUtils->selectAll();
foreach ($newsEditors as $newsEditor) {
  $wId = $newsEditor->getId();
  $firstname = $newsEditorUtils->getFirstname($wId);
  $lastname = $newsEditorUtils->getLastname($wId);
  $newsEditorList[$wId] = "$firstname $lastname";
}
$strSelectNewsEditor = LibHtml::getSelectList("newsEditorId", $newsEditorList, $newsEditorId);

// Create the news stories html select list
if ($targetNewsStory = $newsStoryUtils->selectNext($newsStoryId)) {
  $targetNewsStoryId = $targetNewsStory->getId();
} else {
  $targetNewsStoryId = '';
}

if ($newsStoryId && $newsHeadingId) {
  $nextNewsStoryList = Array('' => '');
  $newsStories = $newsStoryUtils->selectByNewsPaperAndNewsHeading($newsPaperId, $newsHeadingId);
  foreach ($newsStories as $newsStory) {
    $wNewsStoryId = $newsStory->getId();
    $wHeadline = $newsStory->getHeadline();
    if ($wNewsStoryId != $newsStoryId) {
      $nextNewsStoryList[$wNewsStoryId] = substr($wHeadline, 0, 50);
    }
  }
  $strSelectNextNewsStory = LibHtml::getSelectList("targetNewsStoryId", $nextNewsStoryList, $targetNewsStoryId);
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gNewsUrl/newsStory/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[3], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectNewsPublication);
$panelUtils->addHiddenField('newsStoryId', $newsStoryId);
$panelUtils->addHiddenField('headline', $headline);
$panelUtils->addHiddenField('newsHeadingId', $newsHeadingId);
$panelUtils->addHiddenField('newsPaperId', $newsPaperId);
$panelUtils->addHiddenField('formSubmitted', 2);
$panelUtils->closeForm();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[5], $mlText[17], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectNewsPaper);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[10], $mlText[18], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectNewsHeading);
$panelUtils->addHiddenField('newsStoryId', $newsStoryId);
$panelUtils->addHiddenField('headline', $headline);
$panelUtils->addHiddenField('newsPublicationId', $newsPublicationId);
$panelUtils->addHiddenField('formSubmitted', 3);
$panelUtils->closeForm();
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[13], "nbr"), "<input type='text' name='headline' value='$headline' size='30' maxlength='255'>");
$panelUtils->addLine();
if ($newsStoryId && $newsHeadingId) {
  $label = $popupUtils->getTipPopup($mlText[31], $mlText[32], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectNextNewsStory);
  $panelUtils->addLine();
}
$label = $popupUtils->getTipPopup($mlText[9], $mlText[19], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectNewsEditor);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[8], $mlText[15], 300, 500);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='releaseDate' id='releaseDate' value='$releaseDate' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[12], $mlText[16], 300, 500);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='archive' id='archive' value='$archive' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[39], $mlText[41], 300, 500);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='eventStartDate' id='eventStartDate' value='$eventStartDate' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[40], $mlText[41], 300, 500);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='eventEndDate' id='eventEndDate' value='$eventEndDate' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[23], $mlText[20], 300, 300);
$strSelectPage = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageSelect' title='$mlText[35]'> $mlText[36]", "$gTemplateUrl/select.php", 600, 600);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("$mlText[33] <input type='text' name='webpageName' value='$webpageName' size='30' maxlength='255'> $strSelectPage", "n"));
$panelUtils->addHiddenField('webpageId', $webpageId);
$panelUtils->addLine('', "$mlText[7] <input type='text' name='externalUrl' value='$externalUrl' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[37], $mlText[38], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='audioUrl' value='$audioUrl' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('newsStoryId', $newsStoryId);
$panelUtils->addHiddenField('newsPaperId', $newsPaperId);
$panelUtils->addHiddenField('newsHeadingId', $newsHeadingId);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

if ($clockUtils->isUSDateFormat()) {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#releaseDate").datepicker({ dateFormat:'mm/dd/yy' });
  $("#archive").datepicker({ dateFormat:'mm/dd/yy' });
  $("#eventStartDate").datepicker({ dateFormat:'mm/dd/yy' });
  $("#eventEndDate").datepicker({ dateFormat:'mm/dd/yy' });
});
</script>
HEREDOC;
} else {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#releaseDate").datepicker({ dateFormat:'dd-mm-yy' });
  $("#archive").datepicker({ dateFormat:'dd-mm-yy' });
  $("#eventStartDate").datepicker({ dateFormat:'dd-mm-yy' });
  $("#eventEndDate").datepicker({ dateFormat:'dd-mm-yy' });
});
</script>
HEREDOC;
}

$languageCode = $languageUtils->getCurrentAdminLanguageCode();
$code = LibJavaScript::renderJQueryDatepickerLanguageCode($languageCode);
$strJsSuggestCloseDate .= <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $.datepicker.setDefaults($.datepicker.regional['$code']);
});
</script>
HEREDOC;
$panelUtils->addContent($strJsSuggestCloseDate);

$str = $panelUtils->render();

printAdminPage($str);

?>
