<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonParagraphId = LibEnv::getEnvHttpPOST("elearningLessonParagraphId");
  $body = LibEnv::getEnvHttpPOST("body");

  $body = LibString::cleanHtmlString($body);

  // The content must belong to the user
  if ($elearningLessonParagraphId && !$elearningLessonParagraphUtils->createdByUser($elearningLessonParagraphId, $userId)) {
    array_push($warnings, $websiteText[11]);
  }

  if (count($warnings) == 0) {

    if ($elearningLessonParagraph = $elearningLessonParagraphUtils->selectById($elearningLessonParagraphId)) {
      $elearningLessonParagraph->setBody($body);
      $elearningLessonParagraphUtils->update($elearningLessonParagraph);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/lesson/compose.php");
    printContent($str);
    return;

  }

} else {

  $elearningLessonParagraphId = LibEnv::getEnvHttpGET("elearningLessonParagraphId");

  $body = '';
  if ($elearningLessonParagraphId) {
    if ($elearningLessonParagraph = $elearningLessonParagraphUtils->selectById($elearningLessonParagraphId)) {
      $headline = $elearningLessonParagraph->getHeadline();
      $body = $elearningLessonParagraph->getBody();
    }
  }

}

// The content must belong to the user
if ($elearningLessonParagraphId && !$elearningLessonParagraphUtils->createdByUser($elearningLessonParagraphId, $userId)) {
  $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
  printContent($str);
  return;
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/lesson/paragraph/content.php' method='post'>";

include($gHtmlEditorPath . "CKEditorUtils.php");
$contentEditor = new CKEditorUtils();
$contentEditor->languageUtils = $languageUtils;
$contentEditor->commonUtils = $commonUtils;
$contentEditor->load();
$contentEditor->setImagePath($elearningLessonParagraphUtils->imageFilePath);
$contentEditor->setImageUrl($elearningLessonParagraphUtils->imageFileUrl);
$contentEditor->setImageBrowserUploadUrl($gSystemUrl . '/editor/ckeditor/connector/image_elearning_lesson_paragraph.php');
$contentEditor->withStandardToolbar();
$contentEditor->withImageButton();
$editorName = "body";
$strEditor = $contentEditor->render();
$strEditor .= $contentEditor->renderInstance($editorName, $body);
$str .= $strEditor;

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningLessonParagraphId' value='$elearningLessonParagraphId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/lesson/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[1]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
