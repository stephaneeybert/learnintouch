<?PHP

require_once("website.php");

LibHtml::preventCaching();

$strLiveResultIds = LibEnv::getEnvHttpGET("strLiveResultIds");

$liveResultIds = explode(UTILS_URL_VALUE_SEPARATOR, $strLiveResultIds);

$responseText = '{ "liveResultIds" : [';

foreach ($liveResultIds as $elearningResultId) {
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
    $graphImageUrlNoAnswer = $elearningResultUtils->renderExerciseResultsGraphNoAnswerImageUrl($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true);
    $graphImageUrlIncorrect = $elearningResultUtils->renderExerciseResultsGraphIncorrectImageUrl($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true);
    $graphImageUrlCorrect = $elearningResultUtils->renderExerciseResultsGraphCorrectImageUrl($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true);
    $graphTitle = $elearningResultUtils->getExerciseResultsGraphTitle($nbQuestions, $nbCorrectAnswers);
    $strGraph = $elearningResultUtils->renderExerciseResultsGraph($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true, true, '');

    $isAbsent = 1;
    $isInactive = 1;
    $completed = '';
    $elearningSubscriptionId = $elearningResult->getSubscriptionId();
    if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
      $lastActive = $elearningSubscription->getLastActive();
      $lastExerciseId = $elearningSubscription->getLastExerciseId();
      $lastExercisePageId = $elearningSubscription->getLastExercisePageId();

      if ($lastExerciseId == $elearningExerciseId) {
        $last = $clockUtils->systemDateTimeToTimeStamp($lastActive);
        $now = $clockUtils->systemDateTimeToTimeStamp($clockUtils->getSystemDateTime());

        if ($last && (($now - $last) < ELEARNING_ABSENT_TIME)) {
          $isAbsent = '';
        }
        if ($last && (($now - $last) < ELEARNING_INACTIVE_TIME)) {
          $isInactive = '';
        }
        if (!$lastExercisePageId) {
          $completed = '1';
        }
      }
    }

    $responseText .= <<<HEREDOC
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
  "graphImageUrlNoAnswer" : "$graphImageUrlNoAnswer",
  "graphImageUrlNoAnswer" : "$graphImageUrlNoAnswer",
  "graphImageUrlIncorrect" : "$graphImageUrlIncorrect",
  "graphImageUrlCorrect" : "$graphImageUrlCorrect",
  "graphTitle" : "$graphTitle",
  "strGraph" : "$strGraph",
  "subscription" : {
    "elearningSubscriptionId" : "$elearningSubscriptionId",
    "elearningExerciseId" : "$elearningExerciseId",
    "lastActive" : "$lastActive",
    "isAbsent" : "$isAbsent",
    "isInactive" : "$isInactive",
    "completed" : "$completed"
  }
},
HEREDOC;
  }
}

$responseText .= '] }';

print($responseText);

?>
