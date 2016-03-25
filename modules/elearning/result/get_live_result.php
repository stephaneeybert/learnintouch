<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningResultId = LibEnv::getEnvHttpGET("elearningResultId");

if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
  $elearningExerciseId = $elearningResult->getElearningExerciseId();
  $resultTotals = $elearningResultUtils->getExerciseTotals($elearningExerciseId, $elearningResultId);
  $nbQuestions = $elearningResultUtils->getResultNbQuestions($resultTotals);
  $nbCorrectAnswers = $elearningResultUtils->getResultNbCorrectAnswers($resultTotals);
  $nbCorrectReadingAnswers = $elearningResultUtils->getResultNbCorrectReadingAnswers($resultTotals);
  $nbCorrectWritingAnswers = $elearningResultUtils->getResultNbCorrectWritingAnswers($resultTotals);
  $nbCorrectListeningAnswers = $elearningResultUtils->getResultNbCorrectListeningAnswers($resultTotals);
  $nbIncorrectAnswers = $elearningResultUtils->getResultNbIncorrectAnswers($resultTotals);
  $nbIncorrectReadingAnswers = $elearningResultUtils->getResultNbIncorrectReadingAnswers($resultTotals);
  $nbIncorrectWritingAnswers = $elearningResultUtils->getResultNbIncorrectWritingAnswers($resultTotals);
  $nbIncorrectListeningAnswers = $elearningResultUtils->getResultNbIncorrectListeningAnswers($resultTotals);
  $nbNoAnswers = $nbQuestions - ($nbCorrectAnswers + $nbIncorrectAnswers);
  $nbPoints = $elearningResultUtils->getResultNbPoints($resultTotals);
  $nbReadingPoints = $elearningResultUtils->getResultNbReadingPoints($resultTotals);
  $nbWritingPoints = $elearningResultUtils->getResultNbWritingPoints($resultTotals);
  $nbListeningPoints = $elearningResultUtils->getResultNbListeningPoints($resultTotals);
  $grade = $elearningResultRangeUtils->calculateGrade($nbCorrectAnswers, $nbQuestions);
  $graphImageUrlNoAnswerH = $elearningResultUtils->renderExerciseResultsGraphNoAnswerImageUrl($nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true);
  $graphImageUrlIncorrectH = $elearningResultUtils->renderExerciseResultsGraphIncorrectImageUrl($nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true);
  $graphImageUrlCorrectH = $elearningResultUtils->renderExerciseResultsGraphCorrectImageUrl($nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true);
  $graphImageUrlNoAnswerV = $elearningResultUtils->renderExerciseResultsGraphNoAnswerImageUrl($nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, false);
  $graphImageUrlIncorrectV = $elearningResultUtils->renderExerciseResultsGraphIncorrectImageUrl($nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, false);
  $graphImageUrlCorrectV = $elearningResultUtils->renderExerciseResultsGraphCorrectImageUrl($nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, false);
  $graphTitle = $elearningResultUtils->getExerciseResultsGraphTitle($nbQuestions, $nbCorrectAnswers);
  $strGraph = $elearningResultUtils->renderExerciseResultsGraph($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true, true, '');

  $isAbsent = '';
  $isInactive = '';
  $elearningSubscriptionId = $elearningResult->getSubscriptionId();
  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    $lastExerciseId = $elearningSubscription->getLastExerciseId();
    $lastExercisePageId = $elearningSubscription->getLastExercisePageId();
  }

  $responseText = <<<HEREDOC
{
  "elearningResultId" : "$elearningResultId",
  "nbQuestions" : "$nbQuestions",
  "nbNoAnswers" : "$nbNoAnswers",
  "nbCorrectAnswers" : "$nbCorrectAnswers",
  "nbCorrectReadingAnswers" : "$nbCorrectReadingAnswers",
  "nbCorrectWritingAnswers" : "$nbCorrectWritingAnswers",
  "nbCorrectListeningAnswers" : "$nbCorrectListeningAnswers",
  "nbIncorrectAnswers" : "$nbIncorrectAnswers",
  "nbIncorrectReadingAnswers" : "$nbIncorrectReadingAnswers",
  "nbIncorrectWritingAnswers" : "$nbIncorrectWritingAnswers",
  "nbIncorrectListeningAnswers" : "$nbIncorrectListeningAnswers",
  "nbPoints" : "$nbPoints",
  "nbReadingPoints" : "$nbReadingPoints",
  "nbWritingPoints" : "$nbWritingPoints",
  "nbListeningPoints" : "$nbListeningPoints",
  "grade" : "$grade",
  "graphImageUrlNoAnswerH" : "$graphImageUrlNoAnswerH",
  "graphImageUrlIncorrectH" : "$graphImageUrlIncorrectH",
  "graphImageUrlCorrectH" : "$graphImageUrlCorrectH",
  "graphImageUrlNoAnswerV" : "$graphImageUrlNoAnswerV",
  "graphImageUrlIncorrectV" : "$graphImageUrlIncorrectV",
  "graphImageUrlCorrectV" : "$graphImageUrlCorrectV",
  "graphTitle" : "$graphTitle",
  "strGraph" : "$strGraph",
  "subscription" : {
    "elearningSubscriptionId" : "$elearningSubscriptionId",
    "elearningExerciseId" : "$elearningExerciseId"
  }
}
HEREDOC;

  print($responseText);
}

?>
