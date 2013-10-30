<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningScoringRangeId = LibEnv::getEnvHttpPOST("elearningScoringRangeId");
  $upperRange = LibEnv::getEnvHttpPOST("upperRange");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $score = LibEnv::getEnvHttpPOST("score");
  $advice = LibEnv::getEnvHttpPOST("advice");
  $proposal = LibEnv::getEnvHttpPOST("proposal");
  $linkText = LibEnv::getEnvHttpPOST("linkText");
  $webpageId = LibEnv::getEnvHttpPOST("webpageId");
  $webpageName = LibEnv::getEnvHttpPOST("webpageName");
  $scoringId = LibEnv::getEnvHttpPOST("scoringId");

  $upperRange = LibString::cleanString($upperRange);
  $currentLanguageCode = LibString::cleanString($currentLanguageCode);
  $linkText = LibString::cleanString($linkText);
  $webpageId = LibString::cleanString($webpageId);
  $webpageName = LibString::cleanString($webpageName);

  // Clear the page if necessary
  if (!$webpageName) {
    $webpageId = '';
  }

  // If a web page or a system page has been selected then use it
  if ($webpageId) {
    $linkUrl = $webpageId;
  } else {
    $linkUrl = '';
  }

  // The upper value for the range is required
  if (!$upperRange) {
    array_push($warnings, $mlText[6]);
  }

  // The score message is required
  if ($languageUtils->countActiveLanguages() == 1) {
    if (!$score) {
      array_push($warnings, $mlText[3]);
    }
  }

  // The upper value for the range must be between 1 and 100
  if ($upperRange < 1 || $upperRange > 100) {
    array_push($warnings, $mlText[12]);
  }

  $score = LibString::cleanHtmlString("score");
  $advice = LibString::cleanHtmlString("advice");
  $proposal = LibString::cleanHtmlString("proposal");

  if (count($warnings) == 0) {

    if ($scoringRange = $elearningScoringRangeUtils->selectById($elearningScoringRangeId)) {
      $scoringRange->setUpperRange($upperRange);
      $scoringRange->setScore($languageUtils->setTextForLanguage($scoringRange->getScore(), $currentLanguageCode, $score));
      $scoringRange->setAdvice($languageUtils->setTextForLanguage($scoringRange->getAdvice(), $currentLanguageCode, $advice));
      $scoringRange->setProposal($languageUtils->setTextForLanguage($scoringRange->getProposal(), $currentLanguageCode, $proposal));
      $scoringRange->setLinkText($linkText);
      $scoringRange->setLinkUrl($linkUrl);
      $scoringRange->setScoringId($scoringId);
      $elearningScoringRangeUtils->update($scoringRange);
    } else {
      $scoringRange = new ElearningScoringRange();
      $scoringRange->setUpperRange($upperRange);
      $scoringRange->setScore($languageUtils->setTextForLanguage('', $currentLanguageCode, $score));
      $scoringRange->setAdvice($languageUtils->setTextForLanguage('', $currentLanguageCode, $advice));
      $scoringRange->setProposal($languageUtils->setTextForLanguage('', $currentLanguageCode, $proposal));
      $scoringRange->setLinkText($linkText);
      $scoringRange->setLinkUrl($linkUrl);
      $scoringRange->setScoringId($scoringId);
      $elearningScoringRangeUtils->insert($scoringRange);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/scoring/range/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningScoringRangeId = LibEnv::getEnvHttpGET("elearningScoringRangeId");

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  $upperRange = '';
  $score = '';
  $advice = '';
  $proposal = '';
  $linkText = '';
  $linkUrl = '';
  $webpageId = '';
  $webpageName = '';
  $scoringId = '';
  if ($elearningScoringRange = $elearningScoringRangeUtils->selectById($elearningScoringRangeId)) {
    $upperRange = $elearningScoringRange->getUpperRange();
    $score = $languageUtils->getTextForLanguage($elearningScoringRange->getScore(), $currentLanguageCode);
    $advice = $languageUtils->getTextForLanguage($elearningScoringRange->getAdvice(), $currentLanguageCode);
    $proposal = $languageUtils->getTextForLanguage($elearningScoringRange->getProposal(), $currentLanguageCode);
    $linkText = $elearningScoringRange->getLinkText();
    $linkUrl = $elearningScoringRange->getLinkUrl();
    $scoringId = $elearningScoringRange->getScoringId();
  }

  $webpageName = $templateUtils->getPageName($linkUrl);
  if ($webpageName) {
    $webpageId = $linkUrl;
  }

}

$scorings = $elearningScoringUtils->selectAll();
$scoringList = Array();
foreach ($scorings as $scoring) {
  $wId = $scoring->getId();
  $wName = $scoring->getName();
  $scoringList[$wId] = $wName;
}
$strSelect = LibHtml::getSelectList("scoringId", $scoringList, $scoringId);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/scoring/range/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[4], $mlText[14], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='upperRange' value='$upperRange' size='3' maxlength='3'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[5], $mlText[9], 300, 200);
if ($elearningExerciseUtils->useHtmlEditorInnova()) {
  $oInnovaContentName = "score";
  include($gInnovaHtmlEditorPath . "setupElearningScoring.php");
  $panelUtils->addContent($gInnovaHead);
  $strEditor = "<textarea id='$oInnovaContentName' name='$oInnovaContentName' cols='30' rows='5'>\n$score\n</textarea> $gInnovaBodyOpen $gInnovaBodyClose";
  $strJsEditor = <<<HEREDOC
<script type='text/javascript'>
function getContentScore() {
  var content = $oInnovaName.getHTMLBody();
  return(content);
}
function setContentScore(content) {
  $oInnovaName.putHTML(content);
}
function changeScore(languageCode) {
  var url = '$gElearningUrl/scoring/range/getScore.php?elearningScoringRangeId=$elearningScoringRangeId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateScore);
}
function updateScore(responseText) {
  var response = eval('(' + responseText + ')');
  var score = response.score;
  setContentScore(score);
}
$oInnovaName.onSave=new Function("saveInnovaEditorContent$oInnovaName()");
function saveInnovaEditorContent$oInnovaName() {
  var score = getContentScore();
  saveEditorContent("$oInnovaContentName", score);
}
</script>
HEREDOC;
} else {
  include($gHtmlEditorPath . "CKEditorUtils.php");
  $editorName = "score";
  $contentEditorScore = new CKEditorUtils();
  $contentEditorScore->languageUtils = $languageUtils;
  $contentEditorScore->commonUtils = $commonUtils;
  $contentEditorScore->load();
  $contentEditorScore->withReducedToolbar();
  $contentEditorScore->withAjaxSave();
  $strEditor = $contentEditorScore->render();
  $strEditor .= $contentEditorScore->renderInstance($editorName, $score);
  $strJsEditor = <<<HEREDOC
<script type='text/javascript'>
function getContentScore() {
  var editor = CKEDITOR.instances.$editorName;
  var content = editor.getData();
  return(content);
}
function setContentScore(content) {
  var editor = CKEDITOR.instances.$editorName;
  editor.setData(content);
}
</script>
HEREDOC;
}
$panelUtils->addHiddenField('currentLanguageCode', $currentLanguageCode);
$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strEditor . ' ' . $strLanguageFlag);
$strJsEditor .= <<<HEREDOC
<script type='text/javascript'>
function changeScore(languageCode) {
  var url = '$gElearningUrl/scoring/range/getScore.php?elearningScoringRangeId=$elearningScoringRangeId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateScore);
}
function updateScore(responseText) {
  var response = eval('(' + responseText + ')');
  var score = response.score;
  setContentScore(score);
}
function saveEditorContent(editorName, content) {
  editorName = encodeURIComponent(editorName);
  content = encodeURIComponent(content);
  var languageCode = document.getElementById('currentLanguageCode').value;
  var params = []; params["elearningScoringRangeId"] = "$elearningScoringRangeId"; params["languageCode"] = languageCode; params[editorName] = content;
  if (editorName == 'score') {
    ajaxAsynchronousPOSTRequest("$gElearningUrl/scoring/range/updateScore.php", params);
  } else if (editorName == 'advice') { 
    ajaxAsynchronousPOSTRequest("$gElearningUrl/scoring/range/updateAdvice.php", params);
  } else if (editorName == 'proposal') { 
    ajaxAsynchronousPOSTRequest("$gElearningUrl/scoring/range/updateProposal.php", params);
  }
}
</script>
HEREDOC;
$panelUtils->addContent($strJsEditor);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[10], 300, 200);
if ($elearningExerciseUtils->useHtmlEditorInnova()) {
  $oInnovaContentName = "advice";
  include($gInnovaHtmlEditorPath . "setupElearningScoring.php");
  $strEditor = "<textarea id='$oInnovaContentName' name='$oInnovaContentName' cols='30' rows='5'>\n$advice\n</textarea> $gInnovaBodyOpen $gInnovaBodyClose";
  $strJsEditor = <<<HEREDOC
<script type='text/javascript'>
function getContentAdvice() {
  var content = $oInnovaName.getHTMLBody();
  return(content);
}
function setContentAdvice(content) {
  $oInnovaName.putHTML(content);
}
function changeAdvice(languageCode) {
  var url = '$gElearningUrl/scoring/range/getAdvice.php?elearningScoringRangeId=$elearningScoringRangeId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateAdvice);
}
function updateAdvice(responseText) {
  var response = eval('(' + responseText + ')');
  var advice = response.advice;
  setContentAdvice(advice);
}
$oInnovaName.onSave=new Function("saveInnovaEditorContent$oInnovaName()");
function saveInnovaEditorContent$oInnovaName() {
  var advice = getContentAdvice();
  saveEditorContent("$oInnovaContentName", advice);
}
</script>
HEREDOC;
} else {
  $editorName = "advice";
  $contentEditorAdvice = new CKEditorUtils();
  $contentEditorAdvice->languageUtils = $languageUtils;
  $contentEditorAdvice->commonUtils = $commonUtils;
  $contentEditorAdvice->load();
  $contentEditorAdvice->withReducedToolbar();
  $contentEditorAdvice->withAjaxSave();
  $strEditor = $contentEditorAdvice->renderInstance($editorName, $advice);
  $strJsEditor = <<<HEREDOC
<script type='text/javascript'>
function getContentAdvice() {
  var editor = CKEDITOR.instances.$editorName;
  var content = editor.getData();
  return(content);
}
function setContentAdvice(content) {
  var editor = CKEDITOR.instances.$editorName;
  editor.setData(content);
}
function changeAdvice(languageCode) {
  var url = '$gElearningUrl/scoring/range/getAdvice.php?elearningScoringRangeId=$elearningScoringRangeId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateAdvice);
}
function updateAdvice(responseText) {
  var response = eval('(' + responseText + ')');
  var advice = response.advice;
  setContentAdvice(advice);
}
</script>
HEREDOC;
}
$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strEditor . ' ' . $strLanguageFlag);
$panelUtils->addContent($strJsEditor);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[2], $mlText[11], 300, 200);
if ($elearningExerciseUtils->useHtmlEditorInnova()) {
  $oInnovaContentName = "proposal";
  include($gInnovaHtmlEditorPath . "setupElearningScoring.php");
  $strEditor = "<textarea id='$oInnovaContentName' name='$oInnovaContentName' cols='30' rows='5'>\n$proposal\n</textarea> $gInnovaBodyOpen $gInnovaBodyClose";
  $strJsEditor = <<<HEREDOC
<script type='text/javascript'>
function getContentProposal() {
  var content = $oInnovaName.getHTMLBody();
  return(content);
}
function setContentProposal(content) {
  $oInnovaName.putHTML(content);
}
function changeProposal(languageCode) {
  var url = '$gElearningUrl/scoring/range/getProposal.php?elearningScoringRangeId=$elearningScoringRangeId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateProposal);
}
function updateProposal(responseText) {
  var response = eval('(' + responseText + ')');
  var proposal = response.proposal;
  setContentProposal(proposal);
}
$oInnovaName.onSave=new Function("saveInnovaEditorContent$oInnovaName()");
function saveInnovaEditorContent$oInnovaName() {
  var proposal = getContentProposal();
  saveEditorContent("$oInnovaContentName", proposal);
}
</script>
HEREDOC;
} else {
  $editorName = "proposal";
  $contentEditorProposal = new CKEditorUtils();
  $contentEditorProposal->languageUtils = $languageUtils;
  $contentEditorProposal->commonUtils = $commonUtils;
  $contentEditorProposal->load();
  $contentEditorProposal->withReducedToolbar();
  $contentEditorProposal->withAjaxSave();
  $strEditor = $contentEditorProposal->renderInstance($editorName, $proposal);
  $strJsEditor = <<<HEREDOC
<script type='text/javascript'>
function getContentProposal() {
  var editor = CKEDITOR.instances.$editorName;
  var content = editor.getData();
  return(content);
}
function setContentProposal(content) {
  var editor = CKEDITOR.instances.$editorName;
  editor.setData(content);
}
function changeProposal(languageCode) {
  var url = '$gElearningUrl/scoring/range/getProposal.php?elearningScoringRangeId=$elearningScoringRangeId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateProposal);
}
function updateProposal(responseText) {
  var response = eval('(' + responseText + ')');
  var proposal = response.proposal;
  setContentProposal(proposal);
}
</script>
HEREDOC;
}
$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strEditor . ' ' . $strLanguageFlag);
$strJsEditor .= <<<HEREDOC
<script type='text/javascript'>
function changeWebsiteLanguage(languageCode) {
  changeScore(languageCode);
  changeAdvice(languageCode);
  changeProposal(languageCode);
}
</script>
HEREDOC;
$panelUtils->addContent($strJsEditor);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[15], $mlText[16], 300, 200);
$strSelectPage = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageSelect' title='$mlText[17]'> $mlText[18]", "$gTemplateUrl/select.php", 600, 600);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' name='webpageName' value='$webpageName' size='30' maxlength='255'> $strSelectPage", "n"));
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[7], $mlText[8], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='linkText' value='$linkText' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[13], "nbr"), $strSelect);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningScoringRangeId', $elearningScoringRangeId);
$panelUtils->addHiddenField('webpageId', $webpageId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
