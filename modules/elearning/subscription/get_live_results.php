<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningResultId = LibEnv::getEnvHttpGET("elearningResultId");

if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
  $elearningExerciseId = $elearningResult->getElearningExerciseId();
  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $resultTotals = $elearningResultUtils->getExerciseTotals($elearningExerciseId, $elearningResultId);
    $nbQuestions = $elearningResultUtils->getResultNbQuestions($resultTotals);
    $nbIncorrectAnswers = $elearningResultUtils->getResultNbIncorrectAnswers($resultTotals);
    $nbCorrectAnswers = $elearningResultUtils->getResultNbCorrectAnswers($resultTotals);
    $nbCorrectReadingAnswers = $elearningResultUtils->getResultNbCorrectReadingAnswers($resultTotals);
    $nbCorrectWritingAnswers = $elearningResultUtils->getResultNbCorrectWritingAnswers($resultTotals);
    $nbCorrectListeningAnswers = $elearningResultUtils->getResultNbCorrectListeningAnswers($resultTotals);
    $nbReadingPoints = $elearningResultUtils->getResultNbReadingPoints($resultTotals);
    $nbWritingPoints = $elearningResultUtils->getResultNbWritingPoints($resultTotals);
    $nbListeningPoints = $elearningResultUtils->getResultNbListeningPoints($resultTotals);
    $nbPoints = $elearningResultUtils->getResultNbPoints($resultTotals);
    $grade = $elearningResultRangeUtils->calculateGrade($nbCorrectAnswers, $nbQuestions);

    $elearningExercisePages = $elearningExercisePageUtils->selectByExerciseId($elearningExerciseId);
    $questions = '';
    foreach ($elearningExercisePages as $elearningExercisePage) {
      $elearningExercisePageId = $elearningExercisePage->getId();
      $typeIsWriteText = $elearningExercisePageUtils->typeIsWriteText($elearningExercisePage);
      $elearningQuestions = $elearningQuestionUtils->selectByExercisePage($elearningExercisePageId);
      foreach ($elearningQuestions as $elearningQuestion) {
        $elearningQuestionId = $elearningQuestion->getId();
        $isCorrect = $elearningResultUtils->isACorrectAnswer($elearningResultId, $elearningQuestionId);
        $isAnswered = $elearningResultUtils->isAnswered($elearningResultId, $elearningQuestionId);
        $participantAnswers = $elearningQuestionResultUtils->renderParticipantAnswers($elearningResultId, $elearningQuestionId, $isCorrect);
        $participantAnswers = LibString::jsonEscapeLinebreak($participantAnswers);
        $participantAnswers = LibString::escapeDoubleQuotes($participantAnswers);

        $questions .= "{elearningQuestionId : \"$elearningQuestionId\", participantAnswers : \"$participantAnswers\", isCorrect : \"$isCorrect\", isAnswered : \"$isAnswered\", typeIsWriteText : \"$typeIsWriteText\"},";
      }
    }
    $questions = substr($questions, 0, strlen($questions) - 1);

    $graphImageUrlNoAnswer = $elearningResultUtils->renderExerciseResultsGraphNoAnswerImageUrl($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true);
    $graphImageUrlIncorrect = $elearningResultUtils->renderExerciseResultsGraphIncorrectImageUrl($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true);
    $graphImageUrlCorrect = $elearningResultUtils->renderExerciseResultsGraphCorrectImageUrl($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true);
    $graphTitle = $elearningResultUtils->getExerciseResultsGraphTitle($nbQuestions, $nbCorrectAnswers);
    $strGraph = $elearningResultUtils->renderExerciseResultsGraph($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true, true, '');

    $responseText = <<<HEREDOC
{
  "elearningResultId" : "$elearningResultId",
  "nbQuestions" : "$nbQuestions",
  "nbCorrectReadingAnswers" : "$nbCorrectReadingAnswers",
  "nbCorrectWritingAnswers" : "$nbCorrectWritingAnswers",
  "nbCorrectListeningAnswers" : "$nbCorrectListeningAnswers",
  "nbReadingPoints" : "$nbReadingPoints",
  "nbWritingPoints" : "$nbWritingPoints",
  "nbListeningPoints" : "$nbListeningPoints",
  "nbIncorrectAnswers" : "$nbIncorrectAnswers",
  "nbCorrectAnswers" : "$nbCorrectAnswers",
  "grade" : "$grade",
  "nbPoints" : "$nbPoints",
  "questions" : [ $questions ],
  "graphImageUrlNoAnswer" : "$graphImageUrlNoAnswer",
  "graphImageUrlIncorrect" : "$graphImageUrlIncorrect",
  "graphImageUrlCorrect" : "$graphImageUrlCorrect",
  "graphTitle" : "$graphTitle",
  "strGraph" : "$strGraph"
}
HEREDOC;

    print($responseText);
  }
}

?>
