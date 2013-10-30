<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningQuestionId = LibEnv::getEnvHttpPOST("elearningQuestionId");
$text = LibEnv::getEnvHttpPOST("text");

$text = utf8_decode($text);

$elearningQuestion = $elearningQuestionUtils->selectById($elearningQuestionId);
$elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
$elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId);
$elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
$elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId);

// The results have already been created at the start of the exercise
$elearningResultId = LibSession::getSessionValue(ELEARNING_SESSION_RESULT_ID);

if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
  if ($elearningQuestionResults = $elearningQuestionResultUtils->selectByResultAndQuestion($elearningResultId, $elearningQuestionId)) {
    if (count($elearningQuestionResults) > 0) {
      $elearningQuestionResult = $elearningQuestionResults[0];
      $elearningQuestionResult->setElearningAnswerText($text);
      $elearningQuestionResultUtils->update($elearningQuestionResult);
    }
  } else {
    $elearningQuestionResult = new ElearningQuestionResult();
    $elearningQuestionResult->setElearningResult($elearningResultId);
    $elearningQuestionResult->setElearningQuestion($elearningQuestionId);
    $elearningQuestionResult->setElearningAnswerText($text);
    $elearningQuestionResultUtils->insert($elearningQuestionResult);
  }

  $elearningSubscriptionId = $elearningResult->getSubscriptionId();
  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    $elearningSubscriptionUtils->saveLastActive($elearningSubscription);
  }

  $responseText = <<<HEREDOC
{
  "elearningQuestionId" : "$elearningQuestionId",
}
HEREDOC;

  print($responseText);
}

?>
