<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
  $introduction = LibEnv::getEnvHttpPOST("introduction");

  $introduction = LibString::cleanHtmlString($introduction);

  if (count($warnings) == 0) {

    if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
      $elearningLesson->setIntroduction($introduction);
      $elearningLessonUtils->update($elearningLesson);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/lesson/compose.php?elearningLessonId=$elearningLessonId");
    printContent($str);
    return;

  }

} else {

  $elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");

  $name = '';
  $introduction = '';
  if ($elearningLessonId) {
    if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
      $name = $elearningLesson->getName();
      $introduction = $elearningLesson->getIntroduction();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/compose.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$help = $popupUtils->getHelpPopup($mlText[1], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF, "edit");
if ($elearningExerciseUtils->useHtmlEditorInnova()) {
  $oInnovaContentName = "introduction";
  include($gInnovaHtmlEditorPath . "setupElearningLesson.php");
  $panelUtils->addContent($gInnovaHead);
  $strEditor = "<textarea id='$oInnovaContentName' name='$oInnovaContentName' cols='30' rows='5'>\n$introduction\n</textarea> $gInnovaBodyOpen $gInnovaBodyClose";
} else {
  include($gHtmlEditorPath . "CKEditorUtils.php");
  $editorName = "introduction";
  $contentEditor = new CKEditorUtils();
  $contentEditor->languageUtils = $languageUtils;
  $contentEditor->commonUtils = $commonUtils;
  $contentEditor->load();
  $contentEditor->setImagePath($elearningLessonUtils->imageFilePath);
  $contentEditor->setImageUrl($elearningLessonUtils->imageFileUrl);
  $contentEditor->setImageBrowserUploadUrl($gSystemUrl . '/editor/ckeditor/connector/image_elearning_lesson_introduction.php');
  $contentEditor->withStandardToolbar();
  $contentEditor->withImageButton();
  $contentEditor->withLexicon();
  $contentEditor->setHeight(500);
  $strEditor = $contentEditor->render();
  $strEditor .= $contentEditor->renderInstance($editorName, $introduction);
}
$panelUtils->addLine($panelUtils->addCell($strEditor, "c"));
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningLessonId', $elearningLessonId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
