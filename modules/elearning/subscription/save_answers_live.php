<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");
$givenParticipantAnswer = LibEnv::getEnvHttpGET("participantAnswer");

// An ajax request parameter value is UTF-8 encoded
$givenParticipantAnswer = utf8_decode($givenParticipantAnswer);

// Remove backslashes before quotes if any
// A type in answer may contain some
// The backslashes must be removed only if the sent value is coming from an ajax request
// When the sent value is coming from a regular http post request the backslashes are not present
$givenParticipantAnswer = LibString::stripBSlashes($givenParticipantAnswer);

$elearningQuestion = $elearningQuestionUtils->selectById($elearningQuestionId);
$elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
$elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId);
$elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
$elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId);

// A question may receive one or several answers
$participantAnswer = '';
if ($elearningExercisePageUtils->typeIsRequireOneOrMoreCorrectAnswers($elearningExercisePage) || $elearningExercisePageUtils->typeIsRequireAllPossibleAnswers($elearningExercisePage) || $elearningExercisePageUtils->typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningExercisePage) || $elearningExercisePageUtils->typeIsDragAndDropOrderSentence($elearningExercisePage)) {
  $participantAnswer = array();
  // The participant answer value can actually contain several answers
  // concatenated in a string if the question is of a checkbox type
  // or of a drag and drop under any question
  if (strstr($givenParticipantAnswer, ELEARNING_ANSWERS_SEPARATOR)) {
    $answers = explode(ELEARNING_ANSWERS_SEPARATOR, $givenParticipantAnswer);
    foreach ($answers as $answer) {
      if ($answer) {
        array_push($participantAnswer, $answer);
      }
    }
  } else {
    // Even if several answers are possible, there may be only one answer
    // given by the participant
    if ($givenParticipantAnswer) {
      array_push($participantAnswer, $givenParticipantAnswer);
    }
  }
} else {
  $participantAnswer = $givenParticipantAnswer;
}

// Check for the correction only if at least one answer was given for the question
$isCorrectlyAnswered = false;
if ($participantAnswer) {
  $isCorrectlyAnswered = $elearningExercisePageUtils->isCorrectlyAnswered($elearningQuestionId, $participantAnswer);
}

// Return the number of answers to the question, to avoid displaying a message if no answers was given by the participant
$nbGivenAnswers = 0;
if ($elearningExercisePageUtils->answerIsArrayOfAnswers($participantAnswer)) {
  foreach ($participantAnswer as $anAnswer) {
    if ($anAnswer) {
      $nbGivenAnswers++;
    }
  }
} else if ($participantAnswer) {
  $nbGivenAnswers = 1;
}

if ($isCorrectlyAnswered) {
  $questionPoints = $elearningQuestion->getPoints();
} else {
  $questionPoints = 0;
}

// The results have already been created at the start of the exercise
$elearningResultId = LibSession::getSessionValue(ELEARNING_SESSION_RESULT_ID);

if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
  $uniqueQuestionId = $elearningQuestionUtils->renderUniqueQuestionId($elearningQuestionId);

  if ($elearningExercisePageUtils->isWrittenAnswer($elearningExercisePage)) {
    $elearningResultUtils->deleteQuestionResults($elearningQuestionId);

    $elearningQuestionResult = new ElearningQuestionResult();
    $elearningQuestionResult->setElearningResult($elearningResultId);
    $elearningQuestionResult->setElearningQuestion($elearningQuestionId);
    $elearningQuestionResult->setElearningAnswerText($participantAnswer);
    $elearningQuestionResultUtils->insert($elearningQuestionResult);
    $elearningQuestionResultId = $elearningQuestionResultUtils->getLastInsertId();

    // Store the answer in the session as all answers are stored in the session
    $elearningExercisePageUtils->sessionStoreParticipantQuestionAnswer($uniqueQuestionId, $givenParticipantAnswer);
  } else if ($elearningExercisePageUtils->answerIsArrayOfAnswers($participantAnswer)) {
    $elearningResultUtils->deleteQuestionResults($elearningQuestionId);

    if (count($participantAnswer) > 0) {
      $dragAndDropOrder = 0;
      foreach ($participantAnswer as $participantAnswerId) {
        $elearningQuestionResult = new ElearningQuestionResult();
        $elearningQuestionResult->setElearningResult($elearningResultId);
        $elearningQuestionResult->setElearningQuestion($elearningQuestionId);
        $elearningQuestionResult->setElearningAnswerId($participantAnswerId);
        if ($elearningExercisePageUtils->typeIsDragAndDropOrderSentence($elearningExercisePage)) {
          $dragAndDropOrder++;
          $elearningQuestionResult->setElearningAnswerOrder($dragAndDropOrder);
        }
        $elearningQuestionResultUtils->insert($elearningQuestionResult);
        $elearningQuestionResultId = $elearningQuestionResultUtils->getLastInsertId();
      }

      // Store the answer in the session as all participant answers are stored in the session
      $elearningExercisePageUtils->sessionStoreParticipantQuestionAnswer($uniqueQuestionId, $givenParticipantAnswer);
    } else {
      // Store the answer in the session as all participant answers are stored in the session
      $elearningExercisePageUtils->sessionStoreParticipantQuestionAnswer($uniqueQuestionId, '');
    }
  } else {
    $elearningResultUtils->deleteQuestionResults($elearningQuestionId);

    if ($participantAnswer) {
      $elearningQuestionResult = new ElearningQuestionResult();
      $elearningQuestionResult->setElearningResult($elearningResultId);
      $elearningQuestionResult->setElearningQuestion($elearningQuestionId);
      $elearningQuestionResult->setElearningAnswerId($participantAnswer);
      $elearningQuestionResultUtils->insert($elearningQuestionResult);
      $elearningQuestionResultId = $elearningQuestionResultUtils->getLastInsertId();
    }
  }

  $elearningSubscriptionId = $elearningResult->getSubscriptionId();
  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    $elearningSubscriptionUtils->saveLastActive($elearningSubscription);
  }

  $responseText = <<<HEREDOC
{
  "elearningQuestionId" : "$elearningQuestionId"
}
HEREDOC;

  print($responseText);
}

?>
