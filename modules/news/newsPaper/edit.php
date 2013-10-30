<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsPaperId = LibEnv::getEnvHttpPOST("newsPaperId");
  $title = LibEnv::getEnvHttpPOST("title");
  $header = LibEnv::getEnvHttpPOST("header");
  $footer = LibEnv::getEnvHttpPOST("footer");
  $releaseDate = LibEnv::getEnvHttpPOST("releaseDate");
  $archive = LibEnv::getEnvHttpPOST("archive");
  $published = LibEnv::getEnvHttpPOST("published");
  $newsPublicationId = LibEnv::getEnvHttpPOST("newsPublicationId");

  $title = LibString::cleanString($title);
  $releaseDate = LibString::cleanString($releaseDate);
  $archive = LibString::cleanString($archive);
  $published = LibString::cleanString($published);

  // The title is required
  if (!$title) {
    array_push($warnings, $mlText[4]);
  }

  // The publication is required
  if (!$newsPublicationId) {
    array_push($warnings, $mlText[24]);
  }

  // Validate the release date
  if ($releaseDate && !$clockUtils->isLocalNumericDateValid($releaseDate)) {
    array_push($warnings, $mlText[21] . ' ' . $clockUtils->getDateNumericFormatTip());
  }

  // Validate the archive date
  if ($archive && !$clockUtils->isLocalNumericDateValid($archive)) {
    array_push($warnings, $mlText[21] . ' ' . $clockUtils->getDateNumericFormatTip());
  }

  if ($releaseDate) {
    $releaseDate = $clockUtils->localToSystemDate($releaseDate);
  } else {
    $releaseDate = $clockUtils->getSystemDate();
  }

  if ($archive) {
    $archive = $clockUtils->localToSystemDate($archive);
  }

  $header = LibString::cleanHtmlString($header);
  $footer = LibString::cleanHtmlString($footer);

  if (count($warnings) == 0) {

    if ($newsPaper = $newsPaperUtils->selectById($newsPaperId)) {
      $newsPaper->setTitle($title);
      $newsPaper->setHeader($header);
      $newsPaper->setFooter($footer);
      $newsPaper->setReleaseDate($releaseDate);
      $newsPaper->setArchive($archive);
      $newsPaper->setNotPublished($published);
      $newsPaper->setNewsPublicationId($newsPublicationId);
      $newsPaperUtils->update($newsPaper);
    } else {
      $newsPaper = new NewsPaper();
      $newsPaper->setTitle($title);
      $newsPaper->setHeader($header);
      $newsPaper->setFooter($footer);
      $newsPaper->setReleaseDate($releaseDate);
      $newsPaper->setArchive($archive);
      $newsPaper->setNotPublished($published);
      $newsPaper->setNewsPublicationId($newsPublicationId);
      $newsPaperUtils->insert($newsPaper);
      $newsPaperId = $newsPaperUtils->getLastInsertId();
    }

    $str = LibHtml::urlRedirect("$gNewsUrl/newsPaper/admin.php");
    printContent($str);
    return;

  }

} else {

  $newsPublicationId = LibEnv::getEnvHttpGET("newsPublicationId");
  $newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");

  $title = '';
  $header = '';
  $footer = '';
  $releaseDate = '';
  $archive = '';
  $published = '';
  if ($newsPaperId) {
    if ($newsPaper = $newsPaperUtils->selectById($newsPaperId)) {
      $title = $newsPaper->getTitle();
      $header = $newsPaper->getHeader();
      $footer = $newsPaper->getFooter();
      $releaseDate = $newsPaper->getReleaseDate();
      $archive = $newsPaper->getArchive();
      $published = $newsPaper->getNotPublished();
      $newsPublicationId = $newsPaper->getNewsPublicationId();
    }
  }

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

if ($published == '1') {
  $checkedNotPublished = "CHECKED";
} else {
  $checkedNotPublished = '';
}

// Create the news publications html select list
$newsPublicationList = Array('' => '');
if ($newsPublications = $newsPublicationUtils->selectAll()) {
  foreach ($newsPublications as $newsPublication) {
    $wId = $newsPublication->getId();
    $wName = $newsPublication->getName();
    $newsPublicationList[$wId] = $wName;
  }
  $strSelectNewsPublication = LibHtml::getSelectList("newsPublicationId", $newsPublicationList, $newsPublicationId);
} else {
  $strSelectNewsPublication = '';

  array_push($warnings, $mlText[13]);
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gNewsUrl/newsPaper/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[11], $mlText[12], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectNewsPublication);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[6], $mlText[14], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='title' value='$title' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[3], $mlText[8], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='published' $checkedNotPublished value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[7], $mlText[15], 300, 500);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='releaseDate' id='releaseDate' value='$releaseDate' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[2], $mlText[16], 300, 500);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='archive' id='archive' value='$archive' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[9], 300, 300);
if ($newsStoryUtils->useHtmlEditorInnova()) {
  $oInnovaContentName = "header";
  include($gInnovaHtmlEditorPath . "setupNewsPaper.php");
  $panelUtils->addContent($gInnovaHead);
  $strEditor = "<textarea id='$oInnovaContentName' name='$oInnovaContentName' cols='50' rows='4'>$header</textarea> $gInnovaBodyOpen $gInnovaBodyClose";
} else {
  include($gHtmlEditorPath . "CKEditorUtils.php");
  $contentEditor = new CKEditorUtils();
  $contentEditor->languageUtils = $languageUtils;
  $contentEditor->commonUtils = $commonUtils;
  $contentEditor->load();
  $contentEditor->setImagePath($newsPaperUtils->imagePath);
  $contentEditor->setImageUrl($newsPaperUtils->imageUrl);
  $contentEditor->setImageBrowserUploadUrl($gSystemUrl . '/editor/ckeditor/connector/image_news_paper.php');
  $contentEditor->withReducedToolbar();
  $contentEditor->withImageButton();
  $strEditor = $contentEditor->render();
  $strEditor .= $contentEditor->renderInstance("header", $header);
}
$panelUtils->addLine($panelUtils->addCell("<b>$label</b>" . $strEditor, "n"));
$panelUtils->addLine();
if ($newsStoryUtils->useHtmlEditorInnova()) {
  $oInnovaContentName = "footer";
  include($gInnovaHtmlEditorPath . "setupNewsPaper.php");
  $strEditor = "<textarea id='$oInnovaContentName' name='$oInnovaContentName' cols='50' rows='4'>$footer</textarea> $gInnovaBodyOpen $gInnovaBodyClose";
} else {
  $strEditor = $contentEditor->renderInstance("footer", $footer);
}
$label = $popupUtils->getTipPopup($mlText[5], $mlText[10], 300, 300);
$panelUtils->addLine($panelUtils->addCell("<b>$label</b>" . $strEditor, "n"));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('newsPaperId', $newsPaperId);
$panelUtils->closeForm();

if ($clockUtils->isUSDateFormat()) {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#releaseDate").datepicker({ dateFormat:'mm/dd/yy' });
  $("#archive").datepicker({ dateFormat:'mm/dd/yy' });
});
</script>
HEREDOC;
} else {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#releaseDate").datepicker({ dateFormat:'dd-mm-yy' });
  $("#archive").datepicker({ dateFormat:'dd-mm-yy' });
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
