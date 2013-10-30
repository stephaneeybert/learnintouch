<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");

// The results have already been created at the start of the exercise
$elearningResultId = LibSession::getSessionValue(ELEARNING_SESSION_RESULT_ID);

if ($elearningQuestion = $elearningQuestionUtils->selectById($elearningQuestionId)) {
  $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
  if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
    $questionType = $elearningExercisePage->getQuestionType();
    $elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
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

      $responseText = <<<HEREDOC
{
  "elearningResultId" : "$elearningResultId",
  "elearningQuestionId" : "$elearningQuestionId",
  "questionType" : "$questionType",
  "uniqueQuestionId" : "$uniqueQuestionId",
  "uId" : "$uniqueQuestionId",
  "uiId" : "$uniqueQuestionInputId",
  "utId" : "$uniqueTextareaId",
  "isCorrect" : "$isCorrect",
  "isAnswered" : "$isAnswered",
  "answers" : [ $answers ]
}
HEREDOC;
    }
  }

  print($responseText);
}

?>
