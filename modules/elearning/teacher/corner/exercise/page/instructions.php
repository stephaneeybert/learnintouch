<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $instructions = LibEnv::getEnvHttpPOST("instructions");

  $elearningExercisePageId = LibString::cleanString($elearningExercisePageId);
  $currentLanguageCode = LibString::cleanString($currentLanguageCode);

  $instructions = LibString::cleanHtmlString($instructions);

  // The content must belong to the user
  if ($elearningExercisePageId && !$elearningExercisePageUtils->createdByUser($elearningExercisePageId, $userId)) {
    array_push($warnings, $websiteText[11]);
  }

  if (count($warnings) == 0) {

    if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      $elearningExercisePage->setInstructions($languageUtils->setTextForLanguage($elearningExercisePage->getInstructions(), $currentLanguageCode, $instructions));
      $elearningExercisePageUtils->update($elearningExercisePage);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/exercise/compose.php");
    printContent($str);
    return;

  }

} else {

  $elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

  // The content must belong to the user
  if ($elearningExercisePageId && !$elearningExercisePageUtils->createdByUser($elearningExercisePageId, $userId)) {
    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
    printContent($str);
    return;
  }

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  $name = '';
  $instructions = '';
  $questionType = '';
  if ($elearningExercisePageId) {
    if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      $name = $elearningExercisePage->getName();
      $instructions = $languageUtils->getTextForLanguage($elearningExercisePage->getInstructions(), $currentLanguageCode);
      $questionType = $elearningExercisePage->getQuestionType();
    }
  }

}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$help = $popupUtils->getTipPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_QUESTION_MARK_MEDIUM . "' class='no_style_image_icon' title='' alt='' />", $websiteText[10], 300, 400);

$str .= "\n<div style='text-align:right;'>$help</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/exercise/page/instructions.php' method='post'>";

$str .= "\n<span class='system_label'>$websiteText[4]</span> <span class='system_field'>$name</span>";

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
$str .= "\n<input type='hidden' id='currentLanguageCode' name='currentLanguageCode' value='$currentLanguageCode' />";
$str .= "\n<input type='hidden' id='questionType' name='questionType' value='$questionType' />";
$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$strReset = "<a href='javascript:resetInstructions();' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageReset' title='$websiteText[12]' style='margin-top:2px;'></a>";
$str .= "\n<div class='system_field'>$strLanguageFlag $strReset</div>";
$strJsEditor .= <<<HEREDOC
<script type='text/javascript'>
function changeWebsiteLanguage(languageCode) {
  var url = '$gElearningUrl/exercise_page/getInstructions.php?elearningExercisePageId=$elearningExercisePageId&languageCode='+languageCode;
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
  var params = []; params["elearningExercisePageId"] = "$elearningExercisePageId"; params["languageCode"] = languageCode; params[editorName] = content;
  ajaxAsynchronousPOSTRequest("$gElearningUrl/exercise_page/update_instructions.php", params);
}
function resetInstructions() {
  var languageCode = document.getElementById('currentLanguageCode').value;
  var questionType = document.getElementById('questionType').value;
  var url = '$gElearningUrl/exercise_page/reset_instructions.php?elearningExercisePageId=$elearningExercisePageId&languageCode='+languageCode+'&questionType='+questionType;
  ajaxAsynchronousRequest(url, updateInstructions);
}
</script>
HEREDOC;
$str .= "\n<div class='system_field'>$strEditor</div>";
$str .= $strJsEditor;

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[7]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningExercisePageId' value='$elearningExercisePageId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/exercise/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[2]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
