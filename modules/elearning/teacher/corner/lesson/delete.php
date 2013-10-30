<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
  $exercisesInGarbage = LibEnv::getEnvHttpPOST("exercisesInGarbage");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $exercisesInGarbage = LibString::cleanString($exercisesInGarbage);

  // The content must belong to the user
  if ($elearningLessonId && !$elearningLessonUtils->createdByUser($elearningLessonId, $userId)) {
    array_push($warnings, $websiteText[11]);
  }

  if (count($warnings) == 0) {

    $elearningLessonUtils->putInGarbage($elearningLessonId, $exercisesInGarbage);
    $elearningLessonUtils->deleteLesson($elearningLessonId);

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/lesson/compose.php");
    printContent($str);
    return;

  }

} else {

  $elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");

  $name = '';
  $description = '';
  if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
    $name = $elearningLesson->getName();
    $description = $elearningLesson->getDescription();
  }

}

if ($elearningLessonUtils->hasResults($elearningLessonId)) {
  array_push($warnings, $websiteText[3]);
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<div class='system_label'>$websiteText[1]</div>";
$str .= "\n<div class='system_field'>$name</div>";

$str .= "\n<div class='system_label'>$websiteText[5]</div>";
$str .= "\n<div class='system_field'>$description</div>";

if ($elearningLessonUtils->hasExercises($elearningLessonId)) {
  $lessonExerciseLinks = $elearningLessonUtils->renderLessonExerciseComposeLinks($elearningLessonId, $websiteText[7]);
  $label = $popupUtils->getTipPopup($websiteText[8], $websiteText[6], 300, 300);
  $str .= "\n<div class='system_label'>$label</div>";
  $str .= "\n<div class='system_field'>$lessonExerciseLinks</div>";
  $label = $popupUtils->getTipPopup($websiteText[4], $websiteText[6], 300, 300);
  $str .= "\n<div class='system_label'>$label</div>";
  $str .= "\n<div class='system_field'><input type='checkbox' name='exercisesInGarbage' value='1'></div>";
}

$str .= "\n<form name='delete' id='delete' action='$gElearningUrl/teacher/corner/lesson/delete.php' method='post'>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['delete'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[2]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningLessonId' value='$elearningLessonId' />";
$str .= "\n<input type='hidden' name='name' value='$name' />";
$str .= "\n<input type='hidden' name='description' value='$description' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/lesson/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[10]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
