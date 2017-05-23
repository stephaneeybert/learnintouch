<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $instructions = LibEnv::getEnvHttpPOST("instructions");

  $currentLanguageCode = LibString::cleanString($currentLanguageCode);

  $instructions = LibString::cleanHtmlString($instructions);

  // The content must belong to the user
  if ($elearningExerciseId && !$elearningExerciseUtils->createdByUser($elearningExerciseId, $userId)) {
    array_push($warnings, $websiteText[12]);
  }

  if (count($warnings) == 0) {

    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
      $elearningExercise->setInstructions($languageUtils->setTextForLanguage($elearningExercise->getInstructions(), $currentLanguageCode, $instructions));
      $elearningExerciseUtils->update($elearningExercise);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/exercise/compose.php");
    printContent($str);
    return;

  }

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

  // The content must belong to the user
  if ($elearningExerciseId && !$elearningExerciseUtils->createdByUser($elearningExerciseId, $userId)) {
    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
    printContent($str);
    return;
  }

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  $name = '';
  $description = '';
  $instructions = '';
  if ($elearningExerciseId) {
    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
      $name = $elearningExercise->getName();
      $description = $elearningExercise->getDescription();
      $instructions = $languageUtils->getTextForLanguage($elearningExercise->getInstructions(), $currentLanguageCode);
    }
  }

}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$help = $popupUtils->getTipPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_QUESTION_MARK_MEDIUM . "' class='no_style_image_icon' title='' alt='' />", $websiteText[11], 300, 400);

$str .= "\n<div style='text-align:right;'>$help</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/exercise/instructions.php' method='post'>";

$str .= "\n<span class='system_label'>$websiteText[4]</span> <span class='system_field'>$name</span>";

$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$str .= "\n<div class='system_field'>$strLanguageFlag</div>";

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
$str .= "\n<div class='system_field'>$strEditor</div>";

$strJsEditor .= <<<HEREDOC
<script type='text/javascript'>
function changeWebsiteLanguage(languageCode) {
  var url = '$gElearningUrl/exercise/getInstructions.php?elearningExerciseId=$elearningExerciseId&languageCode='+languageCode;
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
  var params = []; params["elearningExerciseId"] = "$elearningExerciseId"; params["languageCode"] = languageCode; params[editorName] = content;
  ajaxAsynchronousPOSTRequest("$gElearningUrl/exercise/update.php", params);
}
</script>
HEREDOC;
$str .= $strJsEditor;

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[7]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningExerciseId' value='$elearningExerciseId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/exercise/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[2]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
