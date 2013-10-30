<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $instructions = LibEnv::getEnvHttpPOST("instructions");

  $elearningExercisePageId = LibString::cleanString($elearningExercisePageId);
  $currentLanguageCode = LibString::cleanString($currentLanguageCode);

  $instructions = LibString::cleanHtmlString($instructions);

  if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
    $elearningExercisePage->setInstructions($languageUtils->setTextForLanguage($elearningExercisePage->getInstructions(), $currentLanguageCode, $instructions));
    $elearningExercisePageUtils->update($elearningExercisePage);
  }

  $str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
  printMessage($str);
  return;

} else {

  $elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

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

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/compose.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $name);
  $panelUtils->addLine();
  if ($elearningExerciseUtils->useHtmlEditorInnova()) {
    $oInnovaContentName = "instructions";
    include($gInnovaHtmlEditorPath . "setupElearningInstructions.php");
    $panelUtils->addContent($gInnovaHead);
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
  $panelUtils->addHiddenField('currentLanguageCode', $currentLanguageCode);
  $panelUtils->addHiddenField('questionType', $questionType);
  $strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
  $strReset = "<a href='javascript:resetInstructions();' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageReset' title='$mlText[12]' style='margin-top:2px;'></a>";
  $label = $popupUtils->getTipPopup($mlText[3], $mlText[5], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strEditor . ' ' . $strLanguageFlag . ' ' . $strReset);
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
  $panelUtils->addContent($strJsEditor);
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningExercisePageId', $elearningExercisePageId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
