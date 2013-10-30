<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
  $text = LibEnv::getEnvHttpPOST("text");

  $text = LibString::cleanHtmlString($text);

  // The content must belong to the user
  if ($elearningExercisePageId && !$elearningExercisePageUtils->createdByUser($elearningExercisePageId, $userId)) {
    array_push($warnings, $websiteText[11]);
  }

  if (count($warnings) == 0) {

    if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      $elearningExercisePage->setText($text);
      $elearningExercisePageUtils->update($elearningExercisePage);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/exercise/compose.php");
    printContent($str);
    return;

  }

} else {

  $elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");

  $text = '';
  if ($elearningExercisePageId) {
    if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      $text = $elearningExercisePage->getText();
    }
  }

}

// The content must belong to the user
if ($elearningExercisePageId && !$elearningExercisePageUtils->createdByUser($elearningExercisePageId, $userId)) {
  $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
  printContent($str);
  return;
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/exercise/page/content.php' method='post'>";

if ($elearningExerciseUtils->useHtmlEditorInnova()) {
  $oInnovaContentName = "text";
  include($gInnovaHtmlEditorPath . "setupElearningExercisePage.php");
  $str .= $gInnovaHead;
  $str .= "\n<textarea id='$oInnovaContentName' name='$oInnovaContentName'>$text</textarea> $gInnovaBodyOpen $gInnovaBodyClose";
} else {
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
  $contentEditor->setHeight(500);
  $strEditor = $contentEditor->render();
  $strEditor .= $contentEditor->renderInstance($editorName, $text);
  $str .= $strEditor;
}

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningExercisePageId' value='$elearningExercisePageId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/exercise/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[1]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
