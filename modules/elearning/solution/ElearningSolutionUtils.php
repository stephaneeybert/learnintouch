<?

class ElearningSolutionUtils extends ElearningSolutionDB {

  var $elearningAnswerUtils;

  function ElearningSolutionUtils() {
    $this->ElearningSolutionDB();
  }

  // Get all the possible solutions to a question
  function getQuestionSolutions($elearningQuestionId) {
    $allPossibleSolutions = '';

    if ($elearningSolutions = $this->selectByQuestion($elearningQuestionId)) {
      foreach ($elearningSolutions as $elearningSolution) {
        $solutionElearningAnswerId = $elearningSolution->getElearningAnswer();
        if ($solutionElearningAnswer = $this->elearningAnswerUtils->selectById($solutionElearningAnswerId)) {
          $allPossibleSolutions .= '(' . $solutionElearningAnswer->getAnswer() . ')';
        }
      }
    }

    return($allPossibleSolutions);
  }

  // Get the number of solutions for a question
  function getNumberOfSolutions($elearningQuestionId) {
    $elearningSolutions = $this->selectByQuestion($elearningQuestionId);

    $nbSolutions = count($elearningSolutions);

    return($nbSolutions);
  }

}

?>
