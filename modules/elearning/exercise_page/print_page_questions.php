<?PHP

require_once("website.php");

$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");

if (!$elearningExercisePageId) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
  }

$str = $elearningExercisePageUtils->printPageOfQuestions($elearningExercisePageId);

print($templateUtils->renderPopup($str));

?>
