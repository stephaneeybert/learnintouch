<?PHP

require_once("website.php");

require_once($gElearningPath . "exercise/store_exercise_page_answers.php");

$elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
$elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
$elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");

$str = LibHtml::urlRedirect("$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId&elearningExercisePageId=$elearningExercisePageId&elearningSubscriptionId=$elearningSubscriptionId");
printContent($str);
exit;

?>
