<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningLessonUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $instructions = LibEnv::getEnvHttpPOST("instructions");

  $currentLanguageCode = LibString::cleanString($currentLanguageCode);

  $instructions = LibString::cleanHtmlString($instructions);

  // The content must belong to the user
  if ($elearningLessonId && !$elearningLessonUtils->createdByUser($elearningLessonId, $userId)) {
    array_push($warnings, $websiteText[12]);
  }

  if (count($warnings) == 0) {

    if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
      $elearningLesson->setInstructions($languageUtils->setTextForLanguage($elearningLesson->getInstructions(), $currentLanguageCode, $instructions));
      $elearningLessonUtils->update($elearningLesson);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/lesson/compose.php");
    printContent($str);
    return;

  }

} else {

  $elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");

  // The content must belong to the user
  if ($elearningLessonId && !$elearningLessonUtils->createdByUser($elearningLessonId, $userId)) {
    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
    printContent($str);
    return;
  }

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  $name = '';
  $description = '';
  $instructions = '';
  if ($elearningLessonId) {
    if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
      $name = $elearningLesson->getName();
      $description = $elearningLesson->getDescription();
      $instructions = $languageUtils->getTextForLanguage($elearningLesson->getInstructions(), $currentLanguageCode);
    }
  }

}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$help = $popupUtils->getTipPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_QUESTION_MARK_MEDIUM . "' class='no_style_image_icon' title='' alt='' />", $websiteText[11], 300, 400);

$str .= "\n<div style='text-align:right;'>$help</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/lesson/instructions.php' method='post'>";

$str .= "\n<span class='system_label'>$websiteText[4]</span> <span class='system_field'>$name</span>";

$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$str .= "\n<div class='system_field'>$strLanguageFlag</div>";

if ($elearningExerciseUtils->useHtmlEditorInnova()) {
  $oInnovaContentName = "instructions";
  include($gInnovaHtmlEditorPath . "setupLessonInstructions.php");
  $str .= $gInnovaHead;
  $strEditor = "<textarea id='$oInnovaContentName' name='$oInnovaContentName' cols='30' rows='5'>\n$instructions\n</textarea> $gInnovaBodyOpen $gInnovaBodyClose";
  $strJsEditor = <<<HEREDOC
<script type='text/javascript'>
function getContent() {
  var content = $oInnovaName.getHTMLBody();
  return(content);
}
function setContent(content) {
  $oInnovaName.putHTML(content);
}
$oInnovaName.onSave=new Function("saveInnovaEditorContent()");
function saveInnovaEditorContent() {
  var body = getContent();
  saveEditorContent("$oInnovaContentName", body);
}
</script>
HEREDOC;
} else {
  include($gHtmlEditorPath . "CKEditorUtils.php");
  $editorName = "instructions";
  $contentEditor = new CKEditorUtils();
  $contentEditor->languageUtils = $languageUtils;
  $contentEditor->commonUtils = $commonUtils;
  $contentEditor->load();
  $contentEditor->withReducedToolbar();
  $contentEditor->withAjaxSave();
  $contentEditor->setHeight(300);
  $strEditor = $contentEditor->render();
  $strEditor .= $contentEditor->renderInstance($editorName, $instructions);
  $strJsEditor = <<<HEREDOC
<script type='text/javascript'>
function getContent() {
  var editor = CKEDITOR.instances.$editorName;
  var content = editor.getData();
  return(content);
}
function setContent(content) {
  var editor = CKEDITOR.instances.$editorName;
  editor.setData(content);
}
</script>
HEREDOC;
}
$str .= "\n<div class='system_field'>$strEditor</div>";
$str .= "\n<input type='hidden' id='currentLanguageCode' name='currentLanguageCode' value='$currentLanguageCode' />";

$strJsEditor .= <<<HEREDOC
<script type='text/javascript'>
function changeWebsiteLanguage(languageCode) {
  var url = '$gElearningUrl/lesson/getInstructions.php?elearningLessonId=$elearningLessonId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateInstructions);
}
function updateInstructions(responseText) {
  var response = eval('(' + responseText + ')');
  var instructions = response.instructions;
  setContent(instructions);
}
function saveEditorContent(editorName, content) {
  content = encodeURIComponent(content);
  var languageCode = document.getElementById('currentLanguageCode').value;
  var params = []; params["elearningLessonId"] = "$elearningLessonId"; params["languageCode"] = languageCode; params[editorName] = content;
  ajaxAsynchronousPOSTRequest("$gElearningUrl/lesson/update.php", params);
}
</script>
HEREDOC;
$str .= $strJsEditor;

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[7]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningLessonId' value='$elearningLessonId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/lesson/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[2]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
