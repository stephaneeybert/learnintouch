<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");
$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

if ($elearningResult = $elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $elearningExerciseId)) {
  $elearningResultId = $elearningResult->getId();
  $elearningSubscriptionId = $elearningResult->getSubscriptionId();
  $lastExercisePageId = '';
  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    $lastExercisePageId = $elearningSubscription->getLastExercisePageId();
  }
  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $questions = '';
    if ($elearningExercisePage = $elearningExercisePageUtils->selectById($lastExercisePageId)) {
      $elearningExercisePageId = $elearningExercisePage->getId();
      $questionType = $elearningExercisePage->getQuestionType();
      $elearningQuestions = $elearningQuestionUtils->selectByExercisePage($lastExercisePageId);
      if ($elearningQuestions = $elearningQuestionUtils->selectByExercisePage($elearningExercisePageId)) {
        foreach ($elearningQuestions as $elearningQuestion) {
          $elearningQuestionId = $elearningQuestion->getId();
          $answers = '';
          $isCorrect = '';
          $isAnswered = '';
          if ($elearningQuestionResults = $elearningQuestionResultUtils->selectByResultAndQuestion($elearningResultId, $elearningQuestionId)) {
            foreach ($elearningQuestionResults as $elearningQuestionResult) {
              $elearningQuestionId = $elearningQuestionResult->getElearningQuestion();
              $isCorrect = $elearningResultUtils->isACorrectAnswer($elearningResultId, $elearningQuestionId);
              $isAnswered = $elearningResultUtils->isAnswered($elearningResultId, $elearningQuestionId);
              $elearningAnswerId = $elearningQuestionResult->getElearningAnswerId();
              $displayedAnswer = '';
              if ($elearningAnswer = $elearningAnswerUtils->selectById($elearningAnswerId)) {
                $displayedAnswer = $elearningAnswer->getAnswer();
                $image = $elearningAnswer->getImage();
                if ($image) {
                  $displayedAnswer .= $elearningAnswerUtils->renderImage($elearningAnswerId);
                }
                $displayedAnswer = LibString::jsonEscapeLinebreak($displayedAnswer);
                $displayedAnswer = LibString::escapeDoubleQuotes($displayedAnswer);
              }
              $uniqueAnswerId = $elearningQuestionUtils->renderUniqueQuestionId($elearningQuestionId, $elearningAnswerId);
              $participantAnswer = '';
              if ($elearningQuestionResult = $elearningQuestionResultUtils->selectByResultAndQuestionAndAnswerId($elearningResultId, $elearningQuestionId, $elearningAnswerId)) {
                $participantAnswer = $elearningAnswerId;
              } else if ($elearningQuestionResults = $elearningQuestionResultUtils->selectByResultAndQuestion($elearningResultId, $elearningQuestionId)) {
                if (count($elearningQuestionResults) > 0) {
                  $elearningQuestionResult = $elearningQuestionResults[0];
                  if ($elearningQuestionUtils->isWrittenAnswer($elearningQuestion)) {
                    $participantAnswer = $elearningQuestionResult->getElearningAnswerText();
                    $participantAnswer = LibString::jsonEscapeLinebreak($participantAnswer);
                    $participantAnswer = LibString::escapeDoubleQuotes($participantAnswer);
                  }
                }
              }
              $answers .= "{id : \"$elearningAnswerId\", uId : \"$uniqueAnswerId\", dAn : \"$displayedAnswer\",  pA : \"$participantAnswer\" },";
            }
          }

          $uniqueQuestionId = $elearningQuestionUtils->renderUniqueQuestionId($elearningQuestionId);
          $uniqueQuestionInputId = $elearningQuestionUtils->renderUniqueQuestionInputId($elearningQuestionId);
          $uniqueTextareaId = $elearningQuestionUtils->renderUniqueQuestionTextareaId($elearningQuestionId);
          $questions .= "{id : \"$elearningQuestionId\", uId : \"$uniqueQuestionId\", uiId : \"$uniqueQuestionInputId\", utId : \"$uniqueTextareaId\", answers : [ $answers ], isCorrect : \"$isCorrect\", isAnswered : \"$isAnswered\"},";
        }
      }

      $questions = substr($questions, 0, strlen($questions) - 1);

      $responseText = <<<HEREDOC
{
  "lastExercisePageId" : "$lastExercisePageId",
  "questionType" : "$questionType",
  "questions" : [ $questions ]
}
HEREDOC;

      print($responseText);
    }
  }
}

?>
