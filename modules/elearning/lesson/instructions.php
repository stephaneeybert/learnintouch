<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $instructions = LibEnv::getEnvHttpPOST("instructions");

  $currentLanguageCode = LibString::cleanString($currentLanguageCode);

  $instructions = LibString::cleanHtmlString($instructions);

  if (count($warnings) == 0) {

    if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
      $elearningLesson->setInstructions($languageUtils->setTextForLanguage($elearningLesson->getInstructions(), $currentLanguageCode, $instructions));
      $elearningLessonUtils->update($elearningLesson);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/lesson/compose.php?elearningLessonId=$elearningLessonId");
    printContent($str);
    return;

  }

} else {

  $elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");

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

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/compose.php?elearningLessonId=$elearningLessonId");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $name);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[11], 300, 300);
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
  saveEditorContent("$oInnovaContentName", body)
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
$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strEditor . ' ' . $strLanguageFlag);
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
$panelUtils->addContent($strJsEditor);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningLessonId', $elearningLessonId);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
