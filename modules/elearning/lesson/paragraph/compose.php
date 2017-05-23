<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonParagraphId = LibEnv::getEnvHttpPOST("elearningLessonParagraphId");
  $body = LibEnv::getEnvHttpPOST("body");

  $body = LibString::cleanHtmlString($body);

  if (count($warnings) == 0) {

    if ($elearningLessonParagraph = $elearningLessonParagraphUtils->selectById($elearningLessonParagraphId)) {
      $elearningLessonParagraph->setBody($body);
      $elearningLessonParagraphUtils->update($elearningLessonParagraph);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/lesson/compose.php");
    printMessage($str);
    return;

  }

} else {

  $elearningLessonParagraphId = LibEnv::getEnvHttpGET("elearningLessonParagraphId");

  $headline = '';
  $body = '';
  if ($elearningLessonParagraphId) {
    if ($elearningLessonParagraph = $elearningLessonParagraphUtils->selectById($elearningLessonParagraphId)) {
      $headline = $elearningLessonParagraph->getHeadline();
      $body = $elearningLessonParagraph->getBody();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$help = $popupUtils->getHelpPopup($mlText[11], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/compose.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell("<b>" . $mlText[4] . "</b> " . $headline, "n"));
$panelUtils->addLine();
include($gHtmlEditorPath . "CKEditorUtils.php");
$contentEditorBody = new CKEditorUtils();
$contentEditorBody->languageUtils = $languageUtils;
$contentEditorBody->commonUtils = $commonUtils;
$contentEditorBody->load();
$contentEditorBody->setImagePath($elearningLessonParagraphUtils->imageFilePath);
$contentEditorBody->setImageUrl($elearningLessonParagraphUtils->imageFileUrl);
$contentEditorBody->setImageBrowserUploadUrl($gSystemUrl . '/editor/ckeditor/connector/image_elearning_lesson_paragraph.php');
$contentEditorBody->withStandardToolbar();
$contentEditorBody->withImageButton();
$contentEditorBody->withLexicon();
$editorName = "body";
$strBodyEditor = $contentEditorBody->render();
$strBodyEditor .= $contentEditorBody->renderInstance($editorName, $body);
$label = $popupUtils->getTipPopup($mlText[29], $mlText[30], 300, 300);
$panelUtils->addLine($strBodyEditor);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningLessonParagraphId', $elearningLessonParagraphId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
