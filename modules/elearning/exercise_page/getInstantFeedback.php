<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");
$elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");
$givenParticipantAnswer = LibEnv::getEnvHttpGET("participantAnswer");

// Remove backslashes before quotes if any
// A type in answer may contain some
// The backslashes must be removed only if the sent value is coming from an ajax request
// When the sent value is coming from a regular http post request the backslashes are not present
$givenParticipantAnswer = LibString::stripBSlashes($givenParticipantAnswer);

$instantCorrection = $preferenceUtils->getValue("ELEARNING_INSTANT_CORRECTION");
$instantCorrectionNoAnswer = $preferenceUtils->getValue("ELEARNING_INSTANT_NO_ANSWER");
$instantCongratulation = $preferenceUtils->getValue("ELEARNING_INSTANT_CONGRATULATION_ON");
$instantSolution = $preferenceUtils->getValue("ELEARNING_INSTANT_SOLUTION");

$elearningQuestion = $elearningQuestionUtils->selectById($elearningQuestionId);
$elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
$elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId);
$elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
$elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId);
if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
  $elearningCourseId = $elearningSubscription->getCourseId();
  if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
    if (!$instantCorrection) {
      $instantCorrection = $elearningCourse->getInstantCorrection();
    }
    if (!$instantCongratulation) {
      $instantCongratulation = $elearningCourse->getInstantCongratulation();
    }
    if (!$instantSolution) {
      $instantSolution = $elearningCourse->getInstantSolution();
    }
  }
}

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
$isCorrectlyAnswered = '';
if ($participantAnswer) {
  $isCorrectlyAnswered = $elearningExercisePageUtils->isCorrectlyAnswered($elearningQuestionId, $participantAnswer);
}

// Return the number of answers as it is used to avoid displaying the error message
// if no answers at all were given by the participant
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

$explanation = '';
if (!$isCorrectlyAnswered) {
  if ($instantCorrection) {
    $explanation = $elearningExercisePageUtils->renderInstantCorrectionExplanation($elearningQuestionId, $participantAnswer, $instantSolution);
  }
} else if ($participantAnswer) {
  if ($instantCongratulation) {
    $explanation = $elearningExercisePageUtils->renderInstantCorrectionCongratulation();
  }
}
$explanation = LibString::jsonEscapeLinebreak($explanation);
$explanation = LibString::escapeDoubleQuotes($explanation);

// Do not display the instant correction if several answers are required
// and the number of participant answers has not yet reached the number of solutions
// This, so as not to display an instant correction before the participant has had the chance
// to answer all possible correct answers
$displayInstantFeedback = true;
if ($elearningExercisePageUtils->typeIsRequireAllPossibleAnswers($elearningExercisePage)) {
  $nbSolutions = $elearningSolutionUtils->getNumberOfSolutions($elearningQuestionId);
  if ($nbGivenAnswers < $nbSolutions) {
    $displayInstantFeedback = false;
  }
}

$responseText = <<<HEREDOC
{
  "displayInstantFeedback" : "$displayInstantFeedback",
  "displayInstantCorrection" : "$instantCorrection",
  "displayInstantCorrectionNoAnswer" : "$instantCorrectionNoAnswer",
  "displayInstantCongratulation" : "$instantCongratulation",
  "elearningQuestionId" : "$elearningQuestionId",
  "isCorrectlyAnswered" : "$isCorrectlyAnswered",
  "nbGivenAnswers" : "$nbGivenAnswers",
  "explanation" : "$explanation"
}
HEREDOC;

print($responseText);

?>
