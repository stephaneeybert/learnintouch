<?PHP

require_once("website.php");

require_once($gElearningPath . "exercise/store_exercise_page_answers.php");

require_once($gElearningPath . "exercise/store_exercise_time.php");

$elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
$elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");

$email = $userUtils->getUserEmail();

// If the user is not logged in then check if the contact page must
// be displayed before displaying the results
if (!$email && $elearningExerciseUtils->displayContactPageBeforeResults($elearningExerciseId)) {

  $str = LibHtml::urlRedirect("$gElearningUrl/exercise/display_contact_page.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId");
  printContent($str);
  exit;

} else {

  if ($email) {
    if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
      $elearningSubscriptionUtils->saveLastExercisePageId($elearningSubscription, '');
      $elearningSubscriptionUtils->saveLastActive($elearningSubscription, '');
    }

    $elearningResultId = $elearningExerciseUtils->saveExerciseResults($elearningExerciseId, $elearningSubscriptionId, $email, '', '', '');
    if ($elearningResultId) {
      $elearningExerciseUtils->sendExerciseResults($elearningResultId, $email, '');
    }
  }

  $str = LibHtml::urlRedirect("$gElearningUrl/exercise/display_results.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId");
  printContent($str);
  exit;

}

?>
