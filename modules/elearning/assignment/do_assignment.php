<?PHP

require_once("website.php");

$elearningAssignmentId = LibEnv::getEnvHttpGET("elearningAssignmentId");

if ($elearningAssignment = $elearningAssignmentUtils->selectById($elearningAssignmentId)) {
  $elearningExerciseId = $elearningAssignment->getElearningExerciseId();
  $elearningSubscriptionId = $elearningAssignment->getElearningSubscriptionId();

  if ($elearningExerciseId && $elearningSubscriptionId) {
    $str = LibHtml::urlRedirect("$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId");
  } else {
    $str = LibHtml::urlRedirect("$gElearningUrl/subscription/display_participant_subscriptions.php");
  }
}

printContent($str);
return;

?>
