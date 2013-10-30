<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$userUtils->checkValidUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");

  // The course must belong to the user
  if ($elearningCourseId && !$elearningCourseUtils->createdByUser($elearningCourseId, $userId)) {
    array_push($warnings, $websiteText[10]);
  }

  // The content must belong to the user
  if ($elearningExerciseId && !$elearningExerciseUtils->createdByUser($elearningExerciseId, $userId)) {
    array_push($warnings, $websiteText[11]);
  }

  if (count($warnings) == 0) {

    if ($elearningCourseItem = $elearningCourseItemUtils->selectByCourseIdAndExerciseId($elearningCourseId, $elearningExerciseId)) {
      $elearningCourseItemId = $elearningCourseItem->getId();
      $elearningCourseItemUtils->delete($elearningCourseItemId);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
    printContent($str);
    exit;

  }

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
  $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");

}

$name = '';
$description = '';
if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $name = $elearningExercise->getName();
  $description = $elearningExercise->getDescription();
}

$courseName = '';
$courseDescription = '';
if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
  $courseName = $elearningCourse->getName();
  $courseDescription = $elearningCourse->getDescription();
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/course/remove_exercise.php' method='post'>";

$str .= "\n<div class='system_label'>$websiteText[4]</div>";
$str .= "\n<div class='system_field'>$name</div>";

$str .= "\n<div class='system_label'>$websiteText[5]</div>";
$str .= "\n<div class='system_field'>$description</div>";

$label = $userUtils->getTipPopup($websiteText[2], $websiteText[1], 300, 400);
$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'>$courseName $courseDescription</div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[6]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningExerciseId' value='$elearningExerciseId' />";
$str .= "\n<input type='hidden' name='elearningCourseId' value='$elearningCourseId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/course/list.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[7]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
