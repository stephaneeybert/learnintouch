<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");

  // The content must belong to the user
  if ($elearningExercisePageId && !$elearningExercisePageUtils->createdByUser($elearningExercisePageId, $userId)) {
    array_push($warnings, $websiteText[11]);
  }

  if (count($warnings) == 0) {

    $elearningExercisePageUtils->deleteExercisePage($elearningExercisePageId);

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/exercise/compose.php");
    printContent($str);
    return;

  }

}

$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
if (!$elearningExercisePageId) {
  $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
}

if ($elearningQuestion = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
  $name = $elearningQuestion->getName();
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<div class='system_label'>$websiteText[1]</div>";
$str .= "\n<div class='system_field'>$name</div>";

if ($elearningExercisePageUtils->exercisePageHasResults($elearningExercisePageId)) {
  $str .= "\n<div class='system_warning'>$websiteText[3]</div>";
}

$str .= "\n<form name='delete' id='delete' action='$gElearningUrl/teacher/corner/exercise/page/delete.php' method='post'>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['delete'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[2]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningExercisePageId' value='$elearningExercisePageId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/exercise/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[10]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
