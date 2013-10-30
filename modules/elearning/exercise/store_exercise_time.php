<?PHP

// Save the time when the exercise ended

$elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");

$exerciseEndTime = $clockUtils->getLocalTimeStamp();

LibSession::putSessionValue(ELEARNING_SESSION_EXERCISE_END_TIME . $elearningExerciseId, $exerciseEndTime);

$exerciseTimedOut = LibEnv::getEnvHttpPOST("exerciseTimedOut");

if ($exerciseTimedOut) {
  LibSession::putSessionValue(ELEARNING_SESSION_EXERCISE_TIME_OUT . $elearningExerciseId, $exerciseEndTime);
}

?>
