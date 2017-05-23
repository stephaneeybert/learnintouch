<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $instructions = LibEnv::getEnvHttpPOST("instructions");

  $currentLanguageCode = LibString::cleanString($currentLanguageCode);

  $instructions = LibString::cleanHtmlString($instructions);

  if (count($warnings) == 0) {

    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
      $elearningExercise->setInstructions($languageUtils->setTextForLanguage($elearningExercise->getInstructions(), $currentLanguageCode, $instructions));
      $elearningExerciseUtils->update($elearningExercise);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php?elearningExerciseId=$elearningExerciseId");
    printContent($str);
    return;

  }

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

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

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/compose.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $name);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[11], 300, 300);
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
$panelUtils->addHiddenField('currentLanguageCode', $currentLanguageCode);
$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell($strEditor . ' ' . $strLanguageFlag, ""));
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
$panelUtils->addContent($strJsEditor);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
