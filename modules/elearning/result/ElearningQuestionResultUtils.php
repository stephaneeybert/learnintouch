<?

class ElearningQuestionResultUtils extends ElearningQuestionResultDB {

  var $elearningAnswerUtils;
  var $elearningQuestionUtils;

  function ElearningQuestionResultUtils() {
    $this->ElearningQuestionResultDB();
  }

  // Get the participant answers of a question
  // This are the ids of the answers a participant has given for a question
  function getParticipantAnswers($elearningResultId, $elearningQuestionId) {
    $participantQuestionAnswers = array();

    if ($elearningQuestionResults = $this->selectByResultAndQuestion($elearningResultId, $elearningQuestionId)) {
      foreach ($elearningQuestionResults as $elearningQuestionResult) {
        $elearningQuestion = $this->elearningQuestionUtils->selectById($elearningQuestionId);
        if ($this->elearningQuestionUtils->isWrittenAnswer($elearningQuestion)) {
          $answer = $elearningQuestionResult->getElearningAnswerText();
          if ($answer) {
            array_push($participantQuestionAnswers, $answer);
          }
        } else {
          $elearningAnswerId = $elearningQuestionResult->getElearningAnswerId();
          if ($elearningAnswerId) {
            array_push($participantQuestionAnswers, $elearningAnswerId);
          }
        }
      }
    }
    return($participantQuestionAnswers);
  }

  // Render all the answers given by the participant
  // This are the visible values of the answers a participant has given for a question
  function renderParticipantAnswers($elearningResultId, $elearningQuestionId, $isCorrectlyAnswered) {
    $userQuestionAnswers = '';

    if ($elearningQuestionResults = $this->selectByResultAndQuestion($elearningResultId, $elearningQuestionId)) {
      $elearningQuestion = $this->elearningQuestionUtils->selectById($elearningQuestionId);
      foreach ($elearningQuestionResults as $elearningQuestionResult) {
        $answer = '';
        if ($this->elearningQuestionUtils->isWrittenAnswer($elearningQuestion)) {
          $answer = $elearningQuestionResult->getElearningAnswerText();
        } else {
          $elearningAnswerId = $elearningQuestionResult->getElearningAnswerId();
          if ($elearningAnswer = $this->elearningAnswerUtils->selectById($elearningAnswerId)) {
            $answer = $elearningAnswer->getAnswer();
            $image = $elearningAnswer->getImage();
            if ($image) {
              $answer .= ' ' . $this->elearningAnswerUtils->renderImage($elearningAnswerId);
            }
          }
        }
        if ($answer) {
          $elearningQuestion = $this->elearningQuestionUtils->selectById($elearningQuestionId);
          if ($this->elearningQuestionUtils->typeIsWriteText($elearningQuestion)) {
            $answer = "<div style='color:grey; font-weight:bold;'>" . $answer . "</div>";
            $userQuestionAnswers .= $answer;
          } else {
            if ($isCorrectlyAnswered) {
              $answer = "<span style='color:green; font-weight:bold;'>" . $answer . "</span>";
            } else {
              $answer = "<span style='color:red; font-weight:bold;'>" . $answer . "</span>";
            }
            $userQuestionAnswers .= ' (' . $answer . ')';
          }
        }
      }
    }

    return($userQuestionAnswers);
  }

}

?>
