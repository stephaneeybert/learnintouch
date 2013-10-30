<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  // The content must belong to the user
  if ($elearningExerciseId && !$elearningExerciseUtils->createdByUser($elearningExerciseId, $userId)) {
    array_push($warnings, $websiteText[11]);
  }

  if ($elearningLessonParagraphs = $elearningLessonParagraphUtils->selectByExerciseId($elearningExerciseId)) {
    $lessonNames = ' ';
    foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
      $elearningLessonId = $elearningLessonParagraph->getElearningLessonId();
      if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
        if (trim($lessonNames)) {
          $lessonNames .= ', ';
        }
        $lessonNames .= '"' . $elearningLesson->getName() . '"';
      }
    }
    array_push($warnings, $websiteText[8] . $lessonNames);
    array_push($warnings, $websiteText[9]);
  }

  if (count($warnings) == 0) {

    $elearningExerciseUtils->putInGarbage($elearningExerciseId);
    $elearningExerciseUtils->deleteExercise($elearningExerciseId);

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/exercise/compose.php");
    printContent($str);
    return;

  }

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

  $name = '';
  $description = '';
  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $name = $elearningExercise->getName();
    $description = $elearningExercise->getDescription();
  }

}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<div class='system_label'>$websiteText[1]</div>";
$str .= "\n<div class='system_field'>$name</div>";

$str .= "\n<div class='system_label'>$websiteText[5]</div>";
$str .= "\n<div class='system_field'>$description</div>";

if ($elearningResultUtils->exerciseHasResults($elearningExerciseId)) {
  $str .= "\n<div class='system_warning'>$websiteText[3]</div>";
}

$str .= "\n<form name='delete' id='delete' action='$gElearningUrl/teacher/corner/exercise/delete.php' method='post'>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['delete'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[2]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningExerciseId' value='$elearningExerciseId' />";
$str .= "\n<input type='hidden' name='name' value='$name' />";
$str .= "\n<input type='hidden' name='description' value='$description' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/exercise/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[10]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
