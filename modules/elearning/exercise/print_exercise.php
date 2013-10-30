<?PHP

require_once("website.php");

$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

if (!$elearningExerciseId) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$str = $elearningExerciseUtils->printExercise($elearningExerciseId);

print($templateUtils->renderPopup($str));

?>
