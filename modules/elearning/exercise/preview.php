<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");

$strCss = $templateUtils->renderPreviewCssProperties();

$str = $templateUtils->renderCommonJavascripts();

$elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId);

if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {

  $elearningExerciseUtils->checkUserLoginForExercise($elearningExercise, $elearningSubscription);

  // Check if the exercise question input fields are to be reset
  $elearningExerciseUtils->checkResetExercise($elearningExerciseId);

  // Display the exercise introduction or a page of questions
  if ($elearningExerciseUtils->skipExerciseIntroduction($elearningExerciseId) && !$elearningExercisePageId) {
    $elearningExercisePageId = $elearningExercisePageUtils->getFirstExercisePage($elearningExercise);
  }

  if ($elearningExercisePageId) {
    $str .= $elearningExerciseUtils->renderExercise($elearningExercise, $elearningExercisePageId, $elearningSubscriptionId);
  } else {
    $str .= $elearningExerciseUtils->renderExerciseIntroduction($elearningExerciseId, $elearningSubscriptionId);
  }

}

printContent($str, $strCss);

?>
