<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $introduction = LibEnv::getEnvHttpPOST("introduction");

  $introduction = LibString::cleanHtmlString($introduction);

  // The content must belong to the user
  if ($elearningExerciseId && !$elearningExerciseUtils->createdByUser($elearningExerciseId, $userId)) {
    array_push($warnings, $websiteText[2]);
  }

  if (count($warnings) == 0) {

    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
      $elearningExercise->setIntroduction($introduction);
      $elearningExerciseUtils->update($elearningExercise);
    } else {
      $elearningExercise = new ElearningExercise();
      $elearningExercise->setIntroduction($introduction);
      $elearningExerciseUtils->insert($elearningExercise);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/exercise/compose.php");
    printContent($str);
    return;

  }

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

  // The content must belong to the user
  if ($elearningExerciseId && !$elearningExerciseUtils->createdByUser($elearningExerciseId, $userId)) {
    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
    printContent($str);
    return;
  }

  $introduction = '';
  if ($elearningExerciseId) {
    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
      $introduction = $elearningExercise->getIntroduction();
    }
  }

}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/exercise/introduction.php' method='post'>";

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
$contentEditor->setHeight(500);
$strEditor = $contentEditor->render();
$strEditor .= $contentEditor->renderInstance($editorName, $introduction);
$str .= $strEditor;

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningExerciseId' value='$elearningExerciseId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/exercise/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[1]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
