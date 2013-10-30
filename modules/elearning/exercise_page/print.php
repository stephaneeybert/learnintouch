<?PHP

require_once("website.php");

$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");


$str = '';

if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExercisePage->getElearningExerciseId())) {
    $str = $elearningExercisePageUtils->printExercisePage($elearningExercise, $elearningExercisePage);
  }
}

print($templateUtils->renderPopup($str));

?>
