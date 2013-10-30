<?PHP

// Store the participant answers of the previous exercise page in the session
// to be later used when displaying the results at the end of the exercise

$elearningPreviousExercisePageId = LibEnv::getEnvHttpPOST("elearningPreviousExercisePageId");

if ($elearningPreviousExercisePage = $elearningExercisePageUtils->selectById($elearningPreviousExercisePageId)) {
  $elearningExercisePageUtils->sessionStoreParticipantAnswers($elearningPreviousExercisePage);
}

?>
