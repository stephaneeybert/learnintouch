<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $introduction = LibEnv::getEnvHttpPOST("introduction");

  $introduction = LibString::cleanHtmlString($introduction);

  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $elearningExercise->setIntroduction($introduction);
    $elearningExerciseUtils->update($elearningExercise);
  } else {
    $elearningExercise = new ElearningExercise();
    $elearningExercise->setIntroduction($introduction);
    $elearningExerciseUtils->insert($elearningExercise);
  }

  $str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
  printContent($str);
  return;

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

  $introduction = '';
  if ($elearningExerciseId) {
    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
      $introduction = $elearningExercise->getIntroduction();
    }
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/compose.php");
  $panelUtils->openForm($PHP_SELF);
  include($gHtmlEditorPath . "CKEditorUtils.php");
  $editorName = "introduction";
  $contentEditor = new CKEditorUtils();
  $contentEditor->languageUtils = $languageUtils;
  $contentEditor->commonUtils = $commonUtils;
  $contentEditor->load();
  $contentEditor->setImagePath($elearningExerciseUtils->imageFilePath);
  $contentEditor->setImageUrl($elearningExerciseUtils->imageFileUrl);
  $contentEditor->setImageBrowserUploadUrl($gSystemUrl . '/editor/ckeditor/connector/image_elearning_exercise_introduction.php');
  $contentEditor->withStandardToolbar();
  $contentEditor->withImageButton();
  $contentEditor->withLexicon();
  $contentEditor->setHeight(500);
  $strEditor = $contentEditor->render();
  $strEditor .= $contentEditor->renderInstance($editorName, $introduction);
  $panelUtils->addLine($strEditor);
  $panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();

  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
