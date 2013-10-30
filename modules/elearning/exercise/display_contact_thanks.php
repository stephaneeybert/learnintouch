<?PHP

require_once("website.php");

$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
$email = LibEnv::getEnvHttpGET("email");

// Display a contact acknowledgement message
$str = $elearningExerciseUtils->renderContactThanks($email);

// Suggest a social network notification
if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $socialConnect = $elearningExercise->getSocialConnect();
  if ($socialConnect) {
    $str .= $elearningExerciseUtils->publishSocialNotification($elearningExerciseId);
  }
}

$gTemplate->setPageContent($str);

require_once($gTemplatePath . "render.php");

?>
