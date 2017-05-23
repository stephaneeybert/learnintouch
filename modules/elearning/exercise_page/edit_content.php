<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
  $text = LibEnv::getEnvHttpPOST("text");

  $text = LibString::cleanHtmlString($text);

  if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
    $elearningExercisePage->setText($text);
    $elearningExercisePageUtils->update($elearningExercisePage);
  }

  $str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
  printContent($str);
  return;

} else {

  $elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");

  $text = '';
  if ($elearningExercisePageId) {
    if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      $text = $elearningExercisePage->getText();
    }
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/compose.php");
  $panelUtils->openForm($PHP_SELF);
  include($gHtmlEditorPath . "CKEditorUtils.php");
  $editorName = "text";
  $contentEditor = new CKEditorUtils();
  $contentEditor->languageUtils = $languageUtils;
  $contentEditor->commonUtils = $commonUtils;
  $contentEditor->load();
  $contentEditor->setImagePath($elearningExercisePageUtils->imageFilePath);
  $contentEditor->setImageUrl($elearningExercisePageUtils->imageFileUrl);
  $contentEditor->setImageBrowserUploadUrl($gSystemUrl . '/editor/ckeditor/connector/image_elearning_exercise_page.php');
  $contentEditor->withStandardToolbar();
  $contentEditor->withImageButton();
  $contentEditor->withLexicon();
  $contentEditor->setHeight(500);
  $strEditor = $contentEditor->render();
  $strEditor .= $contentEditor->renderInstance($editorName, $text);
  $panelUtils->addLine($strEditor);
  $panelUtils->addHiddenField('elearningExercisePageId', $elearningExercisePageId);
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();

  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
