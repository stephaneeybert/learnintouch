<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningResultId = LibEnv::getEnvHttpGET("elearningResultId");
$elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");

if ($elearningQuestionResults = $elearningQuestionResultUtils->selectByResultAndQuestion($elearningResultId, $elearningQuestionId)) {
  if (count($elearningQuestionResults) > 0) {
    $isCorrect = $elearningResultUtils->isACorrectAnswer($elearningResultId, $elearningQuestionId);

    $isAnswered = $elearningResultUtils->isAnswered($elearningResultId, $elearningQuestionId);

    $givenAnswers = $elearningQuestionResultUtils->renderParticipantAnswers($elearningResultId, $elearningQuestionId, $isCorrect);

    $points = 1;
    if ($elearningQuestion = $elearningQuestionUtils->selectById($elearningQuestionId)) {
      $points = $elearningQuestion->getPoints();
    }

    $allPossibleSolutions = $elearningSolutionUtils->getQuestionSolutions($elearningQuestionId);

    $responseText = <<<HEREDOC
{
  "elearningQuestionId" : "$elearningQuestionId",
  "isCorrect" : "$isCorrect",
  "isAnswered" : "$isAnswered",
  "givenAnswers" : "$givenAnswers",
  "points" : "$points",
  "allPossibleSolutions" : "$allPossibleSolutions"
}
HEREDOC;

    print($responseText);
  }
}

?>
