<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningLessonUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
  $introduction = LibEnv::getEnvHttpPOST("introduction");

  $introduction = LibString::cleanHtmlString($introduction);

  // The lesson must belong to the user
  if ($elearningLessonId && !$elearningLessonUtils->createdByUser($elearningLessonId, $userId)) {
    array_push($warnings, $websiteText[2]);
  }

  if (count($warnings) == 0) {

    if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
      $elearningLesson->setIntroduction($introduction);
      $elearningLessonUtils->update($elearningLesson);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/lesson/compose.php");
    printContent($str);
    return;

  }

} else {

  $elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");

  // The content must belong to the user
  if ($elearningLessonId && !$elearningLessonUtils->createdByUser($elearningLessonId, $userId)) {
    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
    printContent($str);
    return;
  }

  $name = '';
  $introduction = '';
  if ($elearningLessonId) {
    if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
      $name = $elearningLesson->getName();
      $introduction = $elearningLesson->getIntroduction();
    }
  }

}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$help = $popupUtils->getTipPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_QUESTION_MARK_MEDIUM . "' class='no_style_image_icon' title='' alt='' />", $websiteText[3], 300, 500);

$str .= "\n<div style='text-align:right;'>$help</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/lesson/introduction.php' method='post'>";

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
$contentEditor->setHeight(500);
$strEditor = $contentEditor->render();
$strEditor .= $contentEditor->renderInstance($editorName, $introduction);
$str .= $strEditor;

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningLessonId' value='$elearningLessonId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/lesson/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[1]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
