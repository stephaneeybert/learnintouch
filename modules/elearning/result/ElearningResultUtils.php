<?

class ElearningResultUtils extends ElearningResultDB {

  var $mlText;
  var $websiteText;

  var $correctColor;
  var $incorrectColor;
  var $noAnswerColor;
  var $barThickness;
  var $maxBarSize;

  var $languageUtils;
  var $commonUtils;
  var $preferenceUtils;
  var $clockUtils;
  var $profileUtils;
  var $uniqueTokenUtils;
  var $userUtils;
  var $adminUtils;
  var $templateUtils;
  var $elearningAssignmentUtils;
  var $elearningQuestionResultUtils;
  var $elearningExercisePageUtils;
  var $elearningQuestionUtils;
  var $elearningAnswerUtils;
  var $elearningExerciseUtils;
  var $elearningLessonUtils;
  var $elearningSolutionUtils;
  var $elearningSubscriptionUtils;
  var $elearningCourseUtils;
  var $elearningClassUtils;
  var $elearningCourseItemUtils;
  var $elearningResultRangeUtils;
  var $elearningTeacherUtils;

  function ElearningResultUtils() {
    $this->ElearningResultDB();

    $this->init();
  }

  function init() {
    $this->correctColor = "#66CC66";
    $this->incorrectColor = "#CC0000";
    $this->noAnswerColor = "#DADADA";
    $this->barThickness = 10;
    $this->maxBarSize = 180;
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  // Delete the old results
  function deleteOldResults() {
    $autoDelete = $this->preferenceUtils->getValue("ELEARNING_AUTO_DELETE_RESULTS");

    if ($autoDelete && is_numeric($autoDelete)) {
      $systemDate = $this->clockUtils->getSystemDate();

      // Get the date since which to delete the mails
      $sinceDate = $this->clockUtils->incrementMonths($systemDate, -1 * $autoDelete);

      $elearningResults = $this->selectOldResults($sinceDate);
      foreach ($elearningResults as $elearningResult) {
        $elearningResultId = $elearningResult->getId();
        $this->deleteResult($elearningResultId);
      }
    }
  }

  function deleteAnswerResults($elearningAnswerId) {
    if ($elearningQuestionResults = $this->elearningQuestionResultUtils->selectByAnswerId($elearningAnswerId)) {
      foreach ($elearningQuestionResults as $elearningQuestionResult) {
        $this->elearningQuestionResultUtils->delete($elearningQuestionResult->getId());
      }
    }
  }

  function deleteQuestionResults($elearningQuestionId) {
    if ($elearningQuestionResults = $this->elearningQuestionResultUtils->selectByQuestionId($elearningQuestionId)) {
      foreach ($elearningQuestionResults as $elearningQuestionResult) {
        $this->elearningQuestionResultUtils->delete($elearningQuestionResult->getId());
      }
    }
  }

  function hasTypedInTextResult($elearningResultId) {
    $hasTypedInText = false;

    if ($elearningQuestionResults = $this->elearningQuestionResultUtils->selectByResult($elearningResultId)) {
      foreach ($elearningQuestionResults as $elearningQuestionResult) {
        if ($elearningQuestion = $this->elearningQuestionUtils->selectById($elearningQuestionResult->getElearningQuestion())) {
          $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
          $elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId);
          if ($this->elearningExercisePageUtils->typeIsWriteText($elearningExercisePage)) {
            $hasTypedInText = true;
          }
        }
      }
    }

    return($hasTypedInText);
  }

  function deleteQuestionsResults($elearningResultId) {
    if ($elearningQuestionResults = $this->elearningQuestionResultUtils->selectByResult($elearningResultId)) {
      foreach ($elearningQuestionResults as $elearningQuestionResult) {
        $this->elearningQuestionResultUtils->delete($elearningQuestionResult->getId());
      }
    }
  }

  function deleteResult($elearningResultId) {
    if ($elearningAssignments = $this->elearningAssignmentUtils->selectByResultId($elearningResultId)) {
      foreach ($elearningAssignments as $elearningAssignment) {
        $elearningAssignment->setElearningResultId('');
        $this->elearningAssignmentUtils->update($elearningAssignment);
      } 
    }

    $this->deleteQuestionsResults($elearningResultId);

    $this->delete($elearningResultId);
  }

  // Check that the results are those of the user
  function belongsToUser($userId) {
    $belongs = false;

    if ($elearningResults = $this->selectByUserId($userId)) {
      if (count($elearningResults) > 0) {
        $belongs = true;
      }
    }

    return($belongs);
  }

  // Check that the results are those of the email address
  function belongsToEmail($email) {
    $belongs = false;

    if ($elearningResults = $this->selectByEmail($email)) {
      if (count($elearningResults) > 0) {
        $belongs = true;
      }
    }

    return($belongs);
  }

  // Check if an exercise has some results
  function exerciseHasResults($elearningExerciseId) {
    if ($elearningResults = $this->selectByExerciseId($elearningExerciseId)) {
      return(true);
    }

    return(false);
  }

  // Calculate the average grade
  function calculateAverageCorrectAnswers($nbCorrectAnswers, $nbQuestions) {
    if ($nbQuestions > 0) {
      $resultGradeScale = $this->elearningExerciseUtils->resultGradeScale();

      $average = round($nbCorrectAnswers * $resultGradeScale / $nbQuestions, 2);
    } else {
      $average = 0;
    }

    return($average);
  }

  // Render the result grades
  function renderResultGrades($elearningResultId, $grade) {
    $str = "<span class='" . ELEARNING_DOM_ID_RESULT_GRADE . "$elearningResultId'>$grade</span>";

    return($str);
  }

  // Render the result ratio
  function renderResultRatio($elearningResultId, $nbCorrectAnswers, $nbQuestions) {
    $str = "<span class='" . ELEARNING_DOM_ID_RESULT_RATIO . "$elearningResultId'>$nbCorrectAnswers</span> / $nbQuestions";

    return($str);
  }

  // Render the result answers
  function renderResultAnswers($elearningResultId, $nbCorrectAnswers, $nbIncorrectAnswers) {
    global $gImagesUserUrl;

    if ($elearningResultId > 0) {
      $str = "<span class='" . ELEARNING_DOM_ID_RESULT_ANSWER . "$elearningResultId'>"
        . "<span class='result_correct_answers'>$nbCorrectAnswers</span> <img border='0' src='$gImagesUserUrl/" . IMAGE_ELEARNING_ANSWER_TRUE . "' title=''> <span class='result_incorrect_answers'>$nbIncorrectAnswers</span> <img border='0' src='$gImagesUserUrl/" . IMAGE_ELEARNING_ANSWER_FALSE . "' title=''>"
        . "</span>";
    } else {
      $str = '';
    }

    return($str);
  }

  // Render the result points
  function renderResultPoints($elearningResultId, $nbPoints) {
    $str = "<span class='" . ELEARNING_DOM_ID_RESULT_POINT . "$elearningResultId'>$nbPoints</span>";

    return($str);
  }

  // Render the result ratio for the question of writing in type
  function renderReadingResultRatio($elearningResultId, $nbCorrectAnswers, $nbQuestions) {
    $str = $this->renderResultRatio(ELEARNING_DOM_ID_READING . $elearningResultId, $nbCorrectAnswers, $nbQuestions);

    return($str);
  }

  // Render the result answers for the question of writing in type
  function renderReadingResultAnswers($elearningResultId, $nbCorrectReadingAnswers, $nbIncorrectReadingAnswers, $nbReadingQuestions) {
    $str = $this->renderResultAnswers(ELEARNING_DOM_ID_READING . $elearningResultId, $nbCorrectReadingAnswers, $nbIncorrectReadingAnswers, $nbReadingQuestions);

    return($str);
  }

  // Render the result ratio for the question of writing in type
  function renderReadingResultPoints($elearningResultId, $nbPoints) {
    $str = $this->renderResultPoints(ELEARNING_DOM_ID_READING . $elearningResultId, $nbPoints);

    return($str);
  }

  // Render the result ratio for the question of writing in type
  function renderWritingResultRatio($elearningResultId, $nbCorrectAnswers, $nbQuestions) {
    $str = $this->renderResultRatio(ELEARNING_DOM_ID_WRITING . $elearningResultId, $nbCorrectAnswers, $nbQuestions);

    return($str);
  }

  // Render the result answers for the question of writing in type
  function renderWritingResultAnswers($elearningResultId, $nbCorrectReadingAnswers, $nbIncorrectReadingAnswers, $nbReadingQuestions) {
    $str = $this->renderResultAnswers(ELEARNING_DOM_ID_WRITING . $elearningResultId, $nbCorrectReadingAnswers, $nbIncorrectReadingAnswers, $nbReadingQuestions);

    return($str);
  }

  // Render the result ratio for the question of writing in type
  function renderWritingResultPoints($elearningResultId, $nbPoints) {
    $str = $this->renderResultPoints(ELEARNING_DOM_ID_WRITING . $elearningResultId, $nbPoints);

    return($str);
  }

  // Render the result ratio for the question of writing in type
  function renderListeningResultRatio($elearningResultId, $nbCorrectAnswers, $nbQuestions) {
    $str = $this->renderResultRatio(ELEARNING_DOM_ID_LISTENING . $elearningResultId, $nbCorrectAnswers, $nbQuestions);

    return($str);
  }

  // Render the result answers for the question of writing in type
  function renderListeningResultAnswers($elearningResultId, $nbCorrectReadingAnswers, $nbIncorrectReadingAnswers, $nbReadingQuestions) {
    $str = $this->renderResultAnswers(ELEARNING_DOM_ID_LISTENING . $elearningResultId, $nbCorrectReadingAnswers, $nbIncorrectReadingAnswers, $nbReadingQuestions);

    return($str);
  }

  // Render the result ratio for the question of writing in type
  function renderListeningResultPoints($elearningResultId, $nbPoints) {
    $str = $this->renderResultPoints(ELEARNING_DOM_ID_LISTENING . $elearningResultId, $nbPoints);

    return($str);
  }

  // Get the results of an exercise
  function getExerciseTotals($elearningExerciseId, $elearningResultId) {
    $nbReadingQuestions = 0;
    $nbWritingQuestions = 0;
    $nbListeningQuestions = 0;
    $nbCorrectReadingAnswers = 0;
    $nbCorrectWritingAnswers = 0;
    $nbCorrectListeningAnswers = 0;
    $nbIncorrectReadingAnswers = 0;
    $nbIncorrectWritingAnswers = 0;
    $nbIncorrectListeningAnswers = 0;
    $nbReadingPoints = 0;
    $nbWritingPoints = 0;
    $nbListeningPoints = 0;
    $nbNotAnswered = 0;
    $nbIncorrectAnswers = 0;

    if ($elearningResult = $this->selectById($elearningResultId)) {
      $nbReadingQuestions = $elearningResult->getNbReadingQuestions();
      $nbWritingQuestions = $elearningResult->getNbWritingQuestions();
      $nbListeningQuestions = $elearningResult->getNbListeningQuestions();
      // No correct answer is quite unlikely
      // It may be a glitch and in that case reprocess the totals
      if (($nbCorrectReadingAnswers + $nbCorrectWritingAnswers + $nbCorrectListeningAnswers) == 0) {
        $resultTotals = $this->processExerciseTotals($elearningExerciseId, $elearningResultId);
        $nbListeningQuestions = $resultTotals[0];
        $nbCorrectListeningAnswers = $resultTotals[1];
        $nbIncorrectListeningAnswers = $resultTotals[2];
        $nbListeningPoints = $resultTotals[3];
        $nbWritingQuestions = $resultTotals[4];
        $nbCorrectWritingAnswers = $resultTotals[5];
        $nbIncorrectWritingAnswers = $resultTotals[6];
        $nbWritingPoints = $resultTotals[7];
        $nbReadingQuestions = $resultTotals[8];
        $nbCorrectReadingAnswers = $resultTotals[9];
        $nbIncorrectReadingAnswers = $resultTotals[10];
        $nbReadingPoints = $resultTotals[11];
        $nbNotAnswered = $resultTotals[12];
        $nbIncorrectAnswers = $resultTotals[13];

        $elearningResult->setNbReadingQuestions($nbReadingQuestions);
        $elearningResult->setNbCorrectReadingAnswers($nbCorrectReadingAnswers);
        $elearningResult->setNbIncorrectReadingAnswers($nbIncorrectReadingAnswers);
        $elearningResult->setNbReadingPoints($nbReadingPoints);
        $elearningResult->setNbWritingQuestions($nbWritingQuestions);
        $elearningResult->setNbCorrectWritingAnswers($nbCorrectWritingAnswers);
        $elearningResult->setNbIncorrectWritingAnswers($nbIncorrectWritingAnswers);
        $elearningResult->setNbWritingPoints($nbWritingPoints);
        $elearningResult->setNbListeningQuestions($nbListeningQuestions);
        $elearningResult->setNbCorrectListeningAnswers($nbCorrectListeningAnswers);
        $elearningResult->setNbIncorrectListeningAnswers($nbIncorrectListeningAnswers);
        $elearningResult->setNbListeningPoints($nbListeningPoints);
        $elearningResult->setNbNotAnswered($nbNotAnswered);
        $elearningResult->setNbIncorrectAnswers($nbIncorrectAnswers);
        $this->update($elearningResult);
      }

      $nbReadingQuestions = $elearningResult->getNbReadingQuestions();
      $nbCorrectReadingAnswers = $elearningResult->getNbCorrectReadingAnswers();
      $nbIncorrectReadingAnswers = $elearningResult->getNbIncorrectReadingAnswers();
      $nbReadingPoints = $elearningResult->getNbReadingPoints();
      $nbWritingQuestions = $elearningResult->getNbWritingQuestions();
      $nbCorrectWritingAnswers = $elearningResult->getNbCorrectWritingAnswers();
      $nbIncorrectWritingAnswers = $elearningResult->getNbIncorrectWritingAnswers();
      $nbWritingPoints = $elearningResult->getNbWritingPoints();
      $nbListeningQuestions = $elearningResult->getNbListeningQuestions();
      $nbCorrectListeningAnswers = $elearningResult->getNbCorrectListeningAnswers();
      $nbIncorrectListeningAnswers = $elearningResult->getNbIncorrectListeningAnswers();
      $nbListeningPoints = $elearningResult->getNbListeningPoints();
      $nbNotAnswered = $elearningResult->getNbNotAnswered();
      $nbIncorrectAnswers = $elearningResult->getNbIncorrectAnswers();
    }

    $resultTotals = array($nbListeningQuestions, $nbCorrectListeningAnswers, $nbIncorrectListeningAnswers, $nbListeningPoints, $nbWritingQuestions, $nbCorrectWritingAnswers, $nbIncorrectWritingAnswers, $nbWritingPoints, $nbReadingQuestions, $nbCorrectReadingAnswers, $nbIncorrectReadingAnswers, $nbReadingPoints, $nbNotAnswered, $nbIncorrectAnswers);

    return($resultTotals);
  }

  // Process and store the results of an exercise
  function processExerciseTotals($elearningExerciseId, $elearningResultId) {
    $nbListeningQuestions = 0;
    $nbCorrectListeningAnswers = 0;
    $nbIncorrectListeningAnswers = 0;
    $nbListeningPoints = 0;
    $nbWritingQuestions = 0;
    $nbCorrectWritingAnswers = 0;
    $nbIncorrectWritingAnswers = 0;
    $nbWritingPoints = 0;
    $nbReadingQuestions = 0;
    $nbCorrectReadingAnswers = 0;
    $nbIncorrectReadingAnswers = 0;
    $nbReadingPoints = 0;
    $nbNotAnswered = 0;
    $nbIncorrectAnswers = 0;

    $elearningExercisePages = $this->elearningExercisePageUtils->selectByExerciseId($elearningExerciseId);
    foreach ($elearningExercisePages as $elearningExercisePage) {
      $elearningExercisePageId = $elearningExercisePage->getId();

      $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePageId);
      foreach ($elearningQuestions as $elearningQuestion) {
        $elearningQuestionId = $elearningQuestion->getId();
        $points = $elearningQuestion->getPoints();
        if (!$this->isAnswered($elearningResultId, $elearningQuestionId)) {
          $nbNotAnswered++;
        }
        $isCorrect = $this->isACorrectAnswer($elearningResultId, $elearningQuestionId);
        if ($this->elearningQuestionUtils->isListeningContent($elearningQuestionId)) {
          $nbListeningQuestions++;
          if ($isCorrect) {
            $nbCorrectListeningAnswers++;
            $nbListeningPoints += $points;
          } else if ($this->isAnswered($elearningResultId, $elearningQuestionId)) {
            $nbIncorrectListeningAnswers++;
            $nbIncorrectAnswers++;
          }
        } else if ($this->elearningQuestionUtils->isWrittenAnswer($elearningQuestion)) {
          $nbWritingQuestions++;
          if ($isCorrect) {
            $nbCorrectWritingAnswers++;
            $nbWritingPoints += $points;
          } else if ($this->isAnswered($elearningResultId, $elearningQuestionId)) {
            $nbIncorrectWritingAnswers++;
            $nbIncorrectAnswers++;
          }
        } else {
          $nbReadingQuestions++;
          if ($isCorrect) {
            $nbCorrectReadingAnswers++;
            $nbReadingPoints += $points;
          } else if ($this->isAnswered($elearningResultId, $elearningQuestionId)) {
            $nbIncorrectReadingAnswers++;
            $nbIncorrectAnswers++;
          }
        }
      }
    }

    $resultTotals = array($nbListeningQuestions, $nbCorrectListeningAnswers, $nbIncorrectListeningAnswers, $nbListeningPoints, $nbWritingQuestions, $nbCorrectWritingAnswers, $nbIncorrectWritingAnswers, $nbWritingPoints, $nbReadingQuestions, $nbCorrectReadingAnswers, $nbIncorrectReadingAnswers, $nbReadingPoints, $nbNotAnswered, $nbIncorrectAnswers);

    return($resultTotals);
  }

  // Check if a question was given an answer by the participant
  function isAnswered($elearningResultId, $elearningQuestionId) {
    $answered = false;

    if ($elearningQuestionResults = $this->elearningQuestionResultUtils->selectByResultAndQuestion($elearningResultId, $elearningQuestionId)) {
      foreach ($elearningQuestionResults as $elearningQuestionResult) {
        $elearningAnswerId = $elearningQuestionResult->getElearningAnswerId();
        $elearningAnswerText = $elearningQuestionResult->getElearningAnswerText();
        if ($elearningAnswerId || $elearningAnswerText) {
          $answered = true;
        }
      }
    }

    return($answered);
  }

  function containsWrittenText($elearningResultId) {
    $containsWords = false;
    if ($elearningQuestionResults = $this->elearningQuestionResultUtils->selectByResult($elearningResultId)) {
      foreach ($elearningQuestionResults as $elearningQuestionResult) {
        $elearningQuestionId = $elearningQuestionResult->getElearningQuestion();
        if ($elearningQuestion = $this->elearningQuestionUtils->selectById($elearningQuestionId)) {
          if ($this->elearningQuestionUtils->typeIsWriteText($elearningQuestion)) {
            $participantAnswer = $elearningQuestionResult->getElearningAnswerText();
            $nbParticipantAnswerWords = $this->elearningExercisePageUtils->countAnswerNbWords($participantAnswer);
            if ($nbParticipantAnswerWords > 0) {
              $containsWords = true;
            }
          }
        }
      }
    }

    return($containsWords);
  }

  // Check if a question is correctly answered
  // IMPORTANT! Another isCorrectlyAnswered function with ALMOST the same business logic
  // is defined in the class ElearningExercisePageUtils but it is based on session data
  // whereas this one is based on the persisted data of the exercise already done
  function isACorrectAnswer($elearningResultId, $elearningQuestionId) {
    $correctAnswer = false;

    if ($elearningQuestion = $this->elearningQuestionUtils->selectById($elearningQuestionId)) {
      if ($elearningQuestionResults = $this->elearningQuestionResultUtils->selectByResultAndQuestion($elearningResultId, $elearningQuestionId)) {
        $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
        $elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId);
        if ($this->elearningExercisePageUtils->typeIsDragAndDropOrderSentence($elearningExercisePage)) {
          // Check that the submitted text answer containing the sentence assembled
          // from all the possible answers is equal to the question itself
          $question = $elearningQuestion->getQuestion();
          if (count($elearningQuestionResults) > 0) {
            $assembledQuestion = '';
            foreach ($elearningQuestionResults as $elearningQuestionResult) {
              $participantAnswer = $elearningQuestionResult->getElearningAnswerId();
              if ($elearningAnswer = $this->elearningAnswerUtils->selectById($participantAnswer)) {
                $assembledQuestion .= ' ' . $elearningAnswer->getAnswer();
              }
            }
            if (trim($question) == trim($assembledQuestion)) {
              $correctAnswer = true;
            } else {
              $correctAnswer = false;
            }
          }
        } else if ($this->elearningExercisePageUtils->typeIsRequireAllPossibleAnswers($elearningExercisePage)) {
          $correctAnswer = false;
          // Check that some possible answers have been checked
          foreach ($elearningQuestionResults as $elearningQuestionResult) {
            $participantAnswer = $elearningQuestionResult->getElearningAnswerId();
            if ($this->elearningAnswerUtils->isASolution($elearningQuestion, $participantAnswer)) {
              $correctAnswer = true;
            }
          }
          // Check that only the possible answers have been checked
          foreach ($elearningQuestionResults as $elearningQuestionResult) {
            $participantAnswer = $elearningQuestionResult->getElearningAnswerId();
            if (!$this->elearningAnswerUtils->isASolution($elearningQuestion, $participantAnswer)) {
              $correctAnswer = false;
            }
          }
          // Check that no possible answers have not been checked
          if ($elearningSolutions = $this->elearningSolutionUtils->selectByQuestion($elearningQuestionId)) {
            // Check that all possible solutions have received an answer
            foreach ($elearningSolutions as $elearningSolution) {
              $solutionElearningAnswerId = $elearningSolution->getElearningAnswer();
              if (!$this->elearningQuestionResultUtils->selectByResultAndQuestionAndAnswerId($elearningResultId, $elearningQuestionId, $solutionElearningAnswerId)) {
                $correctAnswer = false;
              }
            }
          }
        } else if ($this->elearningExercisePageUtils->typeIsRequireOneOrMoreCorrectAnswers($elearningExercisePage) || $this->elearningExercisePageUtils->typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningExercisePage)) {
          $correctAnswer = false;
          // Check that some possible answers have been checked
          foreach ($elearningQuestionResults as $elearningQuestionResult) {
            $participantAnswer = $elearningQuestionResult->getElearningAnswerId();
            if ($this->elearningAnswerUtils->isASolution($elearningQuestion, $participantAnswer)) {
              $correctAnswer = true;
            }
          }
          // Check that only the possible answers have been checked
          foreach ($elearningQuestionResults as $elearningQuestionResult) {
            $participantAnswer = $elearningQuestionResult->getElearningAnswerId();
            if (!$this->elearningAnswerUtils->isASolution($elearningQuestion, $participantAnswer)) {
              $correctAnswer = false;
            }
          }
        } else if ($this->elearningExercisePageUtils->typeIsWriteText($elearningExercisePage)) {
          // If the number of typed in words is roughly the one of required words
          // then the answer is considered correct
          // A teacher can review the results and correct that default assumption
          $correctAnswer = true;
          if (count($elearningQuestionResults) > 0) {
            $elearningQuestionResult = $elearningQuestionResults[0];
            $participantAnswer = $elearningQuestionResult->getElearningAnswerText();
            $nbAnswerWords = $elearningQuestion->getAnswerNbWords();
            if ($nbAnswerWords) {
              $nbParticipantAnswerWords = $this->elearningExercisePageUtils->countAnswerNbWords($participantAnswer);
              if ($nbParticipantAnswerWords < $nbAnswerWords / 2) {
                $correctAnswer = false;
              } else if ($nbParticipantAnswerWords > $nbAnswerWords * 1.5) {
                $correctAnswer = false;
              }
            }
          }
        } else {
          foreach ($elearningQuestionResults as $elearningQuestionResult) {
            $participantAnswer = $elearningQuestionResult->getElearningAnswerId();
            if (!$participantAnswer) {
              $participantAnswer = $elearningQuestionResult->getElearningAnswerText();
            }
            if ($this->elearningAnswerUtils->isASolution($elearningQuestion, $participantAnswer)) {
              $correctAnswer = true;
            } else {
              $correctAnswer = false;
            }
          }
        }
      }
    }

    return($correctAnswer);
  }

  // Get the number of questions
  function getResultNbQuestions($resultTotals) {
    $nb = $this->getResultNbListeningQuestions($resultTotals) + $this->getResultNbWritingQuestions($resultTotals) + $this->getResultNbReadingQuestions($resultTotals);

    return($nb);
  }

  // Get the number of correct answers
  function getResultNbCorrectAnswers($resultTotals) {
    $nb = $this->getResultNbCorrectListeningAnswers($resultTotals) + $this->getResultNbCorrectWritingAnswers($resultTotals) + $this->getResultNbCorrectReadingAnswers($resultTotals);

    return($nb);
  }

  // Get the number of points
  function getResultNbPoints($resultTotals) {
    $nb = $this->getResultNbListeningPoints($resultTotals) + $this->getResultNbWritingPoints($resultTotals) + $this->getResultNbReadingPoints($resultTotals);

    return($nb);
  }

  // Get the number of listening questions
  function getResultNbListeningQuestions($resultTotals) {
    return($resultTotals[0]);
  }

  // Get the number of correct listening answers
  function getResultNbCorrectListeningAnswers($resultTotals) {
    return($resultTotals[1]);
  }

  // Get the number of incorrect listening answers
  function getResultNbIncorrectListeningAnswers($resultTotals) {
    return($resultTotals[2]);
  }

  // Get the number of listening points
  function getResultNbListeningPoints($resultTotals) {
    return($resultTotals[3]);
  }

  // Get the number of writing questions
  function getResultNbWritingQuestions($resultTotals) {
    return($resultTotals[4]);
  }

  // Get the number of correct writing answers
  function getResultNbCorrectWritingAnswers($resultTotals) {
    return($resultTotals[5]);
  }

  // Get the number of incorrect writing answers
  function getResultNbIncorrectWritingAnswers($resultTotals) {
    return($resultTotals[6]);
  }

  // Get the number of writing points
  function getResultNbWritingPoints($resultTotals) {
    return($resultTotals[7]);
  }

  // Get the number of reading questions
  function getResultNbReadingQuestions($resultTotals) {
    return($resultTotals[8]);
  }

  // Get the number of correct reading answers
  function getResultNbCorrectReadingAnswers($resultTotals) {
    return($resultTotals[9]);
  }

  // Get the number of incorrect reading answers
  function getResultNbIncorrectReadingAnswers($resultTotals) {
    return($resultTotals[10]);
  }

  // Get the number of reading points
  function getResultNbReadingPoints($resultTotals) {
    return($resultTotals[11]);
  }

  // Get the number of not answered questions
  // These are the questions for which the participant has not provided an answer
  // It is not to be mistaken with the questions with a wrong answer
  function getResultNbNotAnswered($resultTotals) {
    return($resultTotals[12]);
  }

  // Get the number of incorrect answers
  function getResultNbIncorrectAnswers($resultTotals) {
    return($resultTotals[13]);
  }

  // Render the live result for a question in admin pages javascript
  function renderLiveQuestionResultJs() {
    global $gElearningUrl;
    global $gImagesUserUrl;

    $ELEARNING_DOM_ID_QUESTION_RESULT_ANSWERS = ELEARNING_DOM_ID_QUESTION_RESULT_ANSWERS;
    $ELEARNING_DOM_ID_QUESTION_RESULT_THUMB = ELEARNING_DOM_ID_QUESTION_RESULT_THUMB;
    $ELEARNING_DOM_ID_QUESTION_RESULT_POINT = ELEARNING_DOM_ID_QUESTION_RESULT_POINT;
    $ELEARNING_DOM_ID_QUESTION_RESULT_SOLUTIONS = ELEARNING_DOM_ID_QUESTION_RESULT_SOLUTIONS;

    $imageAnswerTrue = $gImagesUserUrl . '/' . IMAGE_ELEARNING_ANSWER_TRUE;
    $imageAnswerFalse = $gImagesUserUrl . '/' . IMAGE_ELEARNING_ANSWER_FALSE;

    $strLiveResultJs = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  if ('undefined' != typeof elearningSocket) {
    elearningSocket.on('updateResult', function(data) {
      var url = '$gElearningUrl/result/get_live_question_result.php?elearningResultId=' + data.elearningResultId + '&elearningQuestionId=' + data.elearningQuestionId;
      ajaxAsynchronousRequest(url, renderLiveQuestionResult);
    });
  }
});

function renderLiveQuestionResult(responseText) {
  var liveResult = eval('(' + responseText + ')');
  var elearningQuestionId = liveResult.elearningQuestionId;
  if (elearningQuestionId > 0) {
    var givenAnswersDom = $(".$ELEARNING_DOM_ID_QUESTION_RESULT_ANSWERS" + elearningQuestionId);
    if (givenAnswersDom) {
      givenAnswersDom.each(function() {
        $(this).html(liveResult.givenAnswers);
      });
    }
    var thumbDom = $(".$ELEARNING_DOM_ID_QUESTION_RESULT_THUMB" + elearningQuestionId);
    if (thumbDom) {
      thumbDom.each(function() {
        if (liveResult.isCorrect) {
          $(this).attr("src", "$imageAnswerTrue");
        } else {
          $(this).attr("src", "$imageAnswerFalse");
        }
      });
    }
    var pointsDom = $(".$ELEARNING_DOM_ID_QUESTION_RESULT_POINT" + elearningQuestionId);
    var solutionsDom = $(".$ELEARNING_DOM_ID_QUESTION_RESULT_SOLUTIONS" + elearningQuestionId);
    if (liveResult.isCorrect) {
      pointsDom.each(function() {
        $(this).show();
      });
      solutionsDom.each(function() {
        $(this).hide();
      });
    } else {
      pointsDom.each(function() {
        $(this).hide();
      });
      solutionsDom.each(function() {
        $(this).show();
      });
    }
  }
}
</script>

HEREDOC;

    return($strLiveResultJs);
  }


  // Render the live results in admin pages javascript
  function renderLiveResultJs() {
    global $gElearningUrl;
    global $gCommonImagesUrl;
    global $gImageLightOrangeSmallBlink;
    global $gImageLightGreenSmall;
    global $gSocketHostname;

    $ELEARNING_DOM_ID_NO_ANSWER_H = ELEARNING_DOM_ID_NO_ANSWER_H;
    $ELEARNING_DOM_ID_INCORRECT_H = ELEARNING_DOM_ID_INCORRECT_H;
    $ELEARNING_DOM_ID_CORRECT_H = ELEARNING_DOM_ID_CORRECT_H;
    $ELEARNING_DOM_ID_NO_ANSWER_V = ELEARNING_DOM_ID_NO_ANSWER_V;
    $ELEARNING_DOM_ID_INCORRECT_V = ELEARNING_DOM_ID_INCORRECT_V;
    $ELEARNING_DOM_ID_CORRECT_V = ELEARNING_DOM_ID_CORRECT_V;
    $ELEARNING_DOM_ID_INACTIVE = ELEARNING_DOM_ID_INACTIVE;
    $ELEARNING_DOM_ID_LIVE_RESULT = ELEARNING_DOM_ID_LIVE_RESULT;
    $ELEARNING_DOM_ID_RESULT_GRADE = ELEARNING_DOM_ID_RESULT_GRADE;
    $ELEARNING_DOM_ID_RESULT_RATIO = ELEARNING_DOM_ID_RESULT_RATIO;
    $ELEARNING_DOM_ID_RESULT_ANSWER = ELEARNING_DOM_ID_RESULT_ANSWER;
    $ELEARNING_DOM_ID_RESULT_POINT = ELEARNING_DOM_ID_RESULT_POINT;
    $ELEARNING_DOM_ID_READING = ELEARNING_DOM_ID_READING;
    $ELEARNING_DOM_ID_WRITING = ELEARNING_DOM_ID_WRITING;
    $ELEARNING_DOM_ID_LISTENING = ELEARNING_DOM_ID_LISTENING;

    $this->loadLanguageTexts();

    $titleInactive = $this->mlText[38];
    $titleInactive = LibString::decodeHtmlspecialchars($titleInactive);
    $titleInactive = LibString::escapeQuotes($titleInactive);
    $titleCompleted = $this->mlText[39];
    $titleCompleted = LibString::decodeHtmlspecialchars($titleCompleted);
    $titleCompleted = LibString::escapeQuotes($titleCompleted);

    $NODEJS_SOCKET_PORT = NODEJS_SOCKET_PORT;

    $strLiveResultJs = <<<HEREDOC
<script type='text/javascript'>
function renderLiveResult(responseText) {
  var liveResult = eval('(' + responseText + ')');
  var elearningResultId = liveResult.elearningResultId;
  if (elearningResultId > 0) {
    var nbQuestions = liveResult.nbQuestions;
    var nbNoAnswers = liveResult.nbNoAnswers;
    var nbCorrectAnswers = liveResult.nbCorrectAnswers;
    var nbCorrectReadingAnswers = liveResult.nbCorrectReadingAnswers;
    var nbCorrectWritingAnswers = liveResult.nbCorrectWritingAnswers;
    var nbCorrectListeningAnswers = liveResult.nbCorrectListeningAnswers;
    var nbIncorrectAnswers = liveResult.nbIncorrectAnswers;
    var nbIncorrectReadingAnswers = liveResult.nbIncorrectReadingAnswers;
    var nbIncorrectWritingAnswers = liveResult.nbIncorrectWritingAnswers;
    var nbIncorrectListeningAnswers = liveResult.nbIncorrectListeningAnswers;
    var nbAnswers = parseInt(nbCorrectAnswers) + parseInt(nbIncorrectAnswers);
    var nbPoints = liveResult.nbPoints;
    var nbReadingPoints = liveResult.nbReadingPoints;
    var nbWritingPoints = liveResult.nbWritingPoints;
    var nbListeningPoints = liveResult.nbListeningPoints;
    var grade = liveResult.grade;
    var graphTitle = liveResult.graphTitle;
    var subscription = liveResult.subscription;
    if (subscription) {
      var lastActive = subscription.lastActive;
      var graphDom = $("#$ELEARNING_DOM_ID_LIVE_RESULT" + elearningResultId);
      var elearningSubscriptionId = subscription.elearningSubscriptionId;
      var elearningExerciseId = subscription.elearningExerciseId;
      if (graphDom) {
        graphDom.css("display", "inline");
      }
      var gradeDom = $(".$ELEARNING_DOM_ID_RESULT_GRADE" + elearningResultId);
      if (gradeDom) {
        gradeDom.each(function() {
          $(this).html(grade);
        });
      }
      var ratioDom = $(".$ELEARNING_DOM_ID_RESULT_RATIO" + elearningResultId);
      if (ratioDom) {
        ratioDom.each(function() {
          $(this).html(nbCorrectAnswers);
        });
      }
      var ratioDom = $(".$ELEARNING_DOM_ID_RESULT_RATIO$ELEARNING_DOM_ID_READING" + elearningResultId);
      if (ratioDom) {
        ratioDom.each(function() {
          $(this).html(nbCorrectReadingAnswers);
        });
      }
      var ratioDom = $(".$ELEARNING_DOM_ID_RESULT_RATIO$ELEARNING_DOM_ID_WRITING" + elearningResultId);
      if (ratioDom) {
        ratioDom.each(function() {
          $(this).html(nbCorrectWritingAnswers);
        });
      }
      var ratioDom = $(".$ELEARNING_DOM_ID_RESULT_RATIO$ELEARNING_DOM_ID_LISTENING" + elearningResultId);
      if (ratioDom) {
        ratioDom.each(function() {
          $(this).html(nbCorrectListeningAnswers);
        });
      }
      var answersDom = $(".$ELEARNING_DOM_ID_RESULT_ANSWER" + elearningResultId);
      if (answersDom) {
        answersDom.each(function(index, answersDomValue) {
          $(answersDomValue).find('.result_correct_answers').each(function(index, that) {
            $(that).html(nbCorrectAnswers);
          });
          $(answersDomValue).find('.result_incorrect_answers').each(function(index, that) {
            $(that).html(nbIncorrectAnswers);
          });
        });
      }
      var answersDom = $(".$ELEARNING_DOM_ID_RESULT_ANSWER$ELEARNING_DOM_ID_READING" + elearningResultId);
      if (answersDom) {
        answersDom.each(function(index, answersDomValue) {
          $(answersDomValue).find('.result_correct_answers').each(function(index, that) {
            $(that).html(nbCorrectReadingAnswers);
          });
          $(answersDomValue).find('.result_incorrect_answers').each(function(index, that) {
            $(that).html(nbIncorrectReadingAnswers);
          });
        });
      }
      var answersDom = $(".$ELEARNING_DOM_ID_RESULT_ANSWER$ELEARNING_DOM_ID_WRITING" + elearningResultId);
      if (answersDom) {
        answersDom.each(function(index, answersDomValue) {
          $(answersDomValue).find('.result_correct_answers').each(function(index, that) {
            $(that).html(nbCorrectWritingAnswers);
          });
          $(answersDomValue).find('.result_incorrect_answers').each(function(index, that) {
            $(that).html(nbIncorrectWritingAnswers);
          });
        });
      }
      var answersDom = $(".$ELEARNING_DOM_ID_RESULT_ANSWER$ELEARNING_DOM_ID_LISTENING" + elearningResultId);
      if (answersDom) {
        answersDom.each(function(index, answersDomValue) {
          $(answersDomValue).find('.result_correct_answers').each(function(index, that) {
            $(that).html(nbCorrectListeningAnswers);
          });
          $(answersDomValue).find('.result_incorrect_answers').each(function(index, that) {
            $(that).html(nbIncorrectListeningAnswers);
          });
        });
      }
      var pointsDom = $(".$ELEARNING_DOM_ID_RESULT_POINT" + elearningResultId);
      if (pointsDom) {
        pointsDom.each(function() {
          $(this).html(nbPoints);
        });
      }
      var ratioDom = $(".$ELEARNING_DOM_ID_RESULT_POINT$ELEARNING_DOM_ID_READING" + elearningResultId);
      if (ratioDom) {
        ratioDom.each(function() {
          $(this).html(nbReadingPoints);
        });
      }
      var ratioDom = $(".$ELEARNING_DOM_ID_RESULT_POINT$ELEARNING_DOM_ID_WRITING" + elearningResultId);
      if (ratioDom) {
        ratioDom.each(function() {
          $(this).html(nbWritingPoints);
        });
      }
      var ratioDom = $(".$ELEARNING_DOM_ID_RESULT_POINT$ELEARNING_DOM_ID_LISTENING" + elearningResultId);
      if (ratioDom) {
        ratioDom.each(function() {
          $(this).html(nbListeningPoints);
        });
      }

      var preventImageCaching = new Date();
      var graphImageDomIdNoAnswerV = "$ELEARNING_DOM_ID_NO_ANSWER_V" + elearningResultId;
      var graphImageDomIdIncorrectV = "$ELEARNING_DOM_ID_INCORRECT_V" + elearningResultId;
      var graphImageDomIdCorrectV = "$ELEARNING_DOM_ID_CORRECT_V" + elearningResultId;
      var graphImageDomIdNoAnswerH = "$ELEARNING_DOM_ID_NO_ANSWER_H" + elearningResultId;
      var graphImageDomIdIncorrectH = "$ELEARNING_DOM_ID_INCORRECT_H" + elearningResultId;
      var graphImageDomIdCorrectH = "$ELEARNING_DOM_ID_CORRECT_H" + elearningResultId;
      $("."+graphImageDomIdNoAnswerV).each(function() {
        $(this).attr("src", liveResult.graphImageUrlNoAnswerV + "&" + preventImageCaching.getTime());
        $(this).attr("title", graphTitle);
      });
      $("."+graphImageDomIdIncorrectV).each(function() {
        $(this).attr("src", liveResult.graphImageUrlIncorrectV + "&" + preventImageCaching.getTime());
        $(this).attr("title", graphTitle);
      });
      $("."+graphImageDomIdCorrectV).each(function() {
        $(this).attr("src", liveResult.graphImageUrlCorrectV + "&" + preventImageCaching.getTime());
        $(this).attr("title", graphTitle);
      });
      $("."+graphImageDomIdNoAnswerH).each(function() {
        $(this).attr("src", liveResult.graphImageUrlNoAnswerH + "&" + preventImageCaching.getTime());
        $(this).attr("title", graphTitle);
      });
      $("."+graphImageDomIdIncorrectH).each(function() {
        $(this).attr("src", liveResult.graphImageUrlIncorrectH + "&" + preventImageCaching.getTime());
        $(this).attr("title", graphTitle);
      });
      $("."+graphImageDomIdCorrectH).each(function() {
        $(this).attr("src", liveResult.graphImageUrlCorrectH + "&" + preventImageCaching.getTime());
        $(this).attr("title", graphTitle);
      });
    }
  }
}

var elearningSocket;
$(function() {
  if ('undefined' != typeof io && 'undefined' == typeof elearningSocket) {
    elearningSocket = io.connect('$gSocketHostname:$NODEJS_SOCKET_PORT/elearning');
  }
  if ('undefined' != typeof elearningSocket) {
    elearningSocket.on('connect', function() {
      elearningSocket.emit('watchLiveResult');
    });

    if ('undefined' != typeof elearningSocket) {
      elearningSocket.on('updateResult', function(data) {
        var url = '$gElearningUrl/result/get_live_result.php?elearningResultId=' + data.elearningResultId;
        ajaxAsynchronousRequest(url, renderLiveResult);
      });
    }
  }
});

function renderInactiveParticipantImage(subscription) {
  var elearningSubscriptionId = subscription.elearningSubscriptionId;
  var elearningExerciseId = subscription.elearningExerciseId;
  var imageDom = $("#$ELEARNING_DOM_ID_INACTIVE" + elearningSubscriptionId + "_" + elearningExerciseId);
  if (imageDom) {
    imageDom.lastActive = subscription.lastActive;
  }
}

function checkInactiveParticipants() {
/*
  var imagesDom = $(".inactiveParticipant");
  if (imagesDom) {
    imagesDom.each(function() {
      var lastActive = $(this).lastActive;
      now = clockUtils->systemDateTimeToTimeStamp(clockUtils->getSystemDateTime());
      if (last && ((now - last) < elearningExerciseUtils->getAbsentDuration())) {
        isAbsent = '';
      }
      if (last && ((now - last) < elearningExerciseUtils->getInactiveDuration())) {
        isInactive = '';
      }
      if (!lastExercisePageId) {
        completed = '1';
      }
      if (isInactive) {
        $(this).show();
        this.title = "titleInactive";
        this.src = "gCommonImagesUrl/gImageLightOrangeSmallBlink";
      } else if (completed) {
        $(this).show();
        this.title = "titleCompleted";
        this.src = "gCommonImagesUrl/gImageLightGreenSmall";
      } else {
        $(this).hide();
        this.title = '';
        this.src = '';
      }
    });
  }
*/
}
window.setInterval("checkInactiveParticipants()", 5000);

</script>
HEREDOC;

    return($strLiveResultJs);
  }

  // Render the exercise results of the exercise of an exercise
  function renderExerciseResultsGraph($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, $hide, $horizontal, $titlePrefix) {
    if ($titlePrefix) {
      $title = $titlePrefix . ' - ';
    } else {
      $title = '';
    }
    $title .= $this->getExerciseResultsGraphTitle($nbQuestions, $nbCorrectAnswers);

    $sizes = $this->getExerciseResultsGraphImageSizes($nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers);

    $urlCorrectAnswers = $this->getExerciseCorrectAnswersImageUrl($sizes[0], $horizontal);

    $urlIncorrectAnswers = $this->getExerciseIncorrectAnswersImageUrl($sizes[1], $horizontal);

    $urlNoAnswers = $this->getExerciseNoAnswersImageUrl($sizes[2], $horizontal);

    if ($hide) {
      $strHideStyle = "display: none;";
    } else {
      $strHideStyle = '';
    }

    if ($horizontal) {
      $strWidthStyle = "height: " . $this->barThickness . "px;";
    } else {
      $strWidthStyle = "width: " . $this->barThickness . "px;";
    }

    $str = "<span class='" . ELEARNING_DOM_ID_LIVE_RESULT . "$elearningResultId' style='$strHideStyle $strWidthStyle'>";
    if ($horizontal) {
      $ELEARNING_DOM_ID_CORRECT = ELEARNING_DOM_ID_CORRECT_H;
      $ELEARNING_DOM_ID_INCORRECT = ELEARNING_DOM_ID_INCORRECT_H;
      $ELEARNING_DOM_ID_NO_ANSWER = ELEARNING_DOM_ID_NO_ANSWER_H;
      $str .= "<img class='$ELEARNING_DOM_ID_CORRECT$elearningResultId' style='white-space:nowrap;' src='$urlCorrectAnswers' title='$title' alt='' /><img class='$ELEARNING_DOM_ID_INCORRECT$elearningResultId' style='white-space:nowrap;' src='$urlIncorrectAnswers' title='$title' alt='' /><img class='$ELEARNING_DOM_ID_NO_ANSWER$elearningResultId' style='white-space:nowrap;' src='$urlNoAnswers' title='$title' alt='' />";
    } else {
      $ELEARNING_DOM_ID_CORRECT = ELEARNING_DOM_ID_CORRECT_V;
      $ELEARNING_DOM_ID_INCORRECT = ELEARNING_DOM_ID_INCORRECT_V;
      $ELEARNING_DOM_ID_NO_ANSWER = ELEARNING_DOM_ID_NO_ANSWER_V;
      $str .= "<img class='$ELEARNING_DOM_ID_NO_ANSWER$elearningResultId' style='display:block;' src='$urlNoAnswers' title='$title' alt='' /><img class='$ELEARNING_DOM_ID_INCORRECT$elearningResultId' style='display:block;' src='$urlIncorrectAnswers' title='$title' alt='' /><img class='$ELEARNING_DOM_ID_CORRECT$elearningResultId' style='display:block;' src='$urlCorrectAnswers' title='$title' alt='' />";
    }
    $str .= "</span>";

    return($str);
  }

  // Render the results on one question for all the class participants answers mixed together
  function renderQuestionClassResultsGraph($elearningQuestionId, $nbParticipants, $nbCorrectAnswers, $nbIncorrectAnswers, $hide, $horizontal, $titlePrefix) {
    if ($titlePrefix) {
      $title = $titlePrefix . ' - ';
    } else {
      $title = '';
    }
    $title .= $this->getClassResultsGraphTitle($nbParticipants, $nbCorrectAnswers);

    $sizes = $this->getExerciseResultsGraphImageSizes($nbParticipants, $nbCorrectAnswers, $nbIncorrectAnswers);

    $urlCorrectAnswers = $this->getExerciseCorrectAnswersImageUrl($sizes[0], $horizontal);

    $urlIncorrectAnswers = $this->getExerciseIncorrectAnswersImageUrl($sizes[1], $horizontal);

    $urlNoAnswers = $this->getExerciseNoAnswersImageUrl($sizes[2], $horizontal);

    if ($hide) {
      $strHideStyle = "display: none;";
    } else {
      $strHideStyle = '';
    }

    if ($horizontal) {
      $strWidthStyle = "height: " . $this->barThickness . "px;";
    } else {
      $strWidthStyle = "width: " . $this->barThickness . "px;";
    }

    $str = "<span class='" . ELEARNING_DOM_ID_LIVE_RESULT . "$elearningQuestionId' style='$strHideStyle $strWidthStyle'>";
    if ($horizontal) {
      $ELEARNING_DOM_ID_CORRECT = ELEARNING_DOM_ID_CORRECT_H;
      $ELEARNING_DOM_ID_INCORRECT = ELEARNING_DOM_ID_INCORRECT_H;
      $ELEARNING_DOM_ID_NO_ANSWER = ELEARNING_DOM_ID_NO_ANSWER_H;
      $str .= "<img class='$ELEARNING_DOM_ID_CORRECT$elearningQuestionId' style='white-space:nowrap;' src='$urlCorrectAnswers' title='$title' alt='' /><img class='$ELEARNING_DOM_ID_INCORRECT$elearningQuestionId' style='white-space:nowrap;' src='$urlIncorrectAnswers' title='$title' alt='' /><img class='$ELEARNING_DOM_ID_NO_ANSWER$elearningQuestionId' style='white-space:nowrap;' src='$urlNoAnswers' title='$title' alt='' />";
    } else {
      $ELEARNING_DOM_ID_CORRECT = ELEARNING_DOM_ID_CORRECT_V;
      $ELEARNING_DOM_ID_INCORRECT = ELEARNING_DOM_ID_INCORRECT_V;
      $ELEARNING_DOM_ID_NO_ANSWER = ELEARNING_DOM_ID_NO_ANSWER_V;
      $str .= "<img class='$ELEARNING_DOM_ID_NO_ANSWER$elearningQuestionId' style='display:block;' src='$urlNoAnswers' title='$title' alt='' /><img class='$ELEARNING_DOM_ID_INCORRECT$elearningQuestionId' style='display:block;' src='$urlIncorrectAnswers' title='$title' alt='' /><img class='$ELEARNING_DOM_ID_CORRECT$elearningQuestionId' style='display:block;' src='$urlCorrectAnswers' title='$title' alt='' />";
    }
    $str .= "</span>";

    return($str);
  }

  function getExerciseResultsGraphImageSizes($nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers) {
    $sizes = array('', '', '');

    if ($nbQuestions > 0) {
      $resultGradeScale = $this->elearningExerciseUtils->resultGradeScale();

      if ($resultGradeScale > 0) {
        // Calculate the number of correct answers relative to the highest number of questions
        $relativeNbCorrectAnswers = ($nbCorrectAnswers * $resultGradeScale) / $nbQuestions;

        // Calculate the bar size based on the number of correct answers
        $correctAnswersSize = round(($relativeNbCorrectAnswers * $this->maxBarSize) / $resultGradeScale);

        // Make sure to display the correct graphical results if any
        if ($nbCorrectAnswers > 0 && $correctAnswersSize == 0) {
          $correctAnswersSize = 1;
        }

        // Calculate the number of incorrect answers relative to the highest number of questions
        $relativeNbIncorrectAnswers = ($nbIncorrectAnswers * $resultGradeScale) / $nbQuestions;

        // Calculate the bar size based on the number of incorrect answers
        $incorrectAnswersSize = round(($relativeNbIncorrectAnswers * $this->maxBarSize) / $resultGradeScale);

        // Make sure to display the correct graphical results if any
        if ($nbIncorrectAnswers > 0 && $incorrectAnswersSize == 0) {
          $incorrectAnswersSize = 1;
        }
      } else {
        $correctAnswersSize = 0;
        $incorrectAnswersSize = 0;
      }

      // Have non null size to display an image
      if ($correctAnswersSize == 0) {
        $correctAnswersSize = 1;
      }

      $sizes[0] = $correctAnswersSize;

      // Have non null size to display an image
      if ($incorrectAnswersSize == 0) {
        $incorrectAnswersSize = 1;
      }

      $sizes[1] = $incorrectAnswersSize;

      $noAnswersSize = $this->maxBarSize - $correctAnswersSize - $incorrectAnswersSize;

      // Have non null size to always display an image as one is needed for the live update
      if ($noAnswersSize <= 0) {
        $noAnswersSize = 1;
      }

      $sizes[2] = $noAnswersSize;
    }

    return($sizes);
  }

  function getExerciseResultsGraphTitle($nbQuestions, $nbCorrectAnswers) {
    $this->loadLanguageTexts();

    $title = $nbCorrectAnswers . ' ' . $this->websiteText[26] . ' ' . $nbQuestions. ' ' . $this->websiteText[27];

    return($title);
  }

  function getClassResultsGraphTitle($nbParticipants, $nbCorrectAnswers) {
    $this->loadLanguageTexts();

    $title = $nbCorrectAnswers . ' ' . $this->websiteText[26] . ' ' . $nbParticipants. ' ' . $this->websiteText[31];

    return($title);
  }

  function renderExerciseResultsGraphNoAnswerImageUrl($nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, $horizontal) {
    $sizes = $this->getExerciseResultsGraphImageSizes($nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers);

    $url = $this->getExerciseNoAnswersImageUrl($sizes[2], $horizontal);

    return($url);
  }

  function renderExerciseResultsGraphIncorrectImageUrl($nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, $horizontal) {
    $sizes = $this->getExerciseResultsGraphImageSizes($nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers);

    $url = $this->getExerciseIncorrectAnswersImageUrl($sizes[1], $horizontal);

    return($url);
  }

  function renderExerciseResultsGraphCorrectImageUrl($nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, $horizontal) {
    $sizes = $this->getExerciseResultsGraphImageSizes($nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers);

    $url = $this->getExerciseCorrectAnswersImageUrl($sizes[0], $horizontal);

    return($url);
  }

  function getExerciseIncorrectAnswersImageUrl($size, $horizontal) {
    $color = urlencode($this->incorrectColor);

    // Avoid a 1 pixel wide answer color
    if ($size == 1) {
      $color = urlencode($this->noAnswerColor);
    }

    $url = $this->getExerciseResultsGraphImageUrl($color, $size, $horizontal);

    return($url);
  }

  function getExerciseNoAnswersImageUrl($size, $horizontal) {
    $color = urlencode($this->noAnswerColor);

    $url = $this->getExerciseResultsGraphImageUrl($color, $size, $horizontal);

    return($url);
  }

  function getExerciseCorrectAnswersImageUrl($size, $horizontal) {
    $color = urlencode($this->correctColor);

    // Avoid a 1 pixel wide answer color
    if ($size == 1) {
      $color = urlencode($this->noAnswerColor);
    }

    $url = $this->getExerciseResultsGraphImageUrl($color, $size, $horizontal);

    return($url);
  }

  function getExerciseResultsGraphImageUrl($color, $size, $horizontal) {
    global $gUtilsUrl;

    if ($horizontal) {
      $width = $size;
      $height = $this->barThickness;
    } else {
      $width = $this->barThickness;
      $height = $size;
    }

    $url = $gUtilsUrl . "/printBarImage.php?color=$color&width=$width&height=$height";

    return($url);
  }

  // Render the exercise results of a subscription or of a class
  function renderResultsGraph($elearningResults) {
    global $gUtilsUrl;
    global $gElearningUrl;
    global $gJSNoStatus;
    global $gIsPhoneClient;

    $str = '';

    $this->loadLanguageTexts();

    $totalCorrectAnswers = 0;
    $totalIncorrectAnswers = 0;
    $totalQuestions = 0;
    $totalPoints = 0;
    $nbExercise = 0;
    $highestQuestionNb = 0;

    $graphFilter = $this->elearningExerciseUtils->getGraphFilter();

    if (count($elearningResults) > 0) {
      $str .= "<table><tr>";

      foreach ($elearningResults as $elearningResult) {
        $elearningResultId = $elearningResult->getId();
        $elearningExerciseId = $elearningResult->getElearningExerciseId();
        $elearningSubscriptionId = $elearningResult->getSubscriptionId();
        $participantName = $this->getParticipantName($elearningResult);

        if ($elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
          $elearningCourseId = $elearningSubscription->getCourseId();
        } else {
          $elearningCourseId = '';
        }
        $displayInstantFeedback = $this->elearningExercisePageUtils->displayInstantFeedback($elearningExerciseId, $elearningSubscriptionId, $elearningCourseId);
        if (($graphFilter == 'ELEARNING_GRAPH_FILTER_NOT_INSTANT' && $displayInstantFeedback) || ($graphFilter == 'ELEARNING_GRAPH_FILTER_INSTANT' && !$displayInstantFeedback)) {
          continue;
        }

        if ($elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId)) {
          $exerciseName = $elearningExercise->getName();
        } else {
          $exerciseName = '';
        }

        $exerciseDate = $this->clockUtils->systemToLocalNumericDate($elearningResult->getExerciseDate());

        $resultTotals = $this->getExerciseTotals($elearningExerciseId, $elearningResultId);
        $nbQuestions = $this->getResultNbQuestions($resultTotals);
        $nbCorrectAnswers = $this->getResultNbCorrectAnswers($resultTotals);
        $nbIncorrectAnswers = $this->getResultNbIncorrectAnswers($resultTotals);
        $points = $this->getResultNbPoints($resultTotals);
        $grade = $this->elearningResultRangeUtils->calculateGrade($nbCorrectAnswers, $nbQuestions);

        $highestQuestionNb = max($highestQuestionNb, $nbQuestions);

        $totalCorrectAnswers = $totalCorrectAnswers + $nbCorrectAnswers;
        $totalIncorrectAnswers = $totalIncorrectAnswers + $nbIncorrectAnswers;
        $totalQuestions = $totalQuestions + $nbQuestions;
        $totalPoints = $totalPoints + $points;
        $nbExercise++;

        $titlePrefix = $participantName . ' - ' . $exerciseName . ' - ' . $exerciseDate;

        if ($nbQuestions > 0) {
          $str .= "<td>"
            . $this->renderExerciseResultsGraph($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, false, false, $titlePrefix)
            . "</td>";
        }
      }

      $str .= "</tr></table>";

      $averageCorrectAnswers = $this->calculateAverageCorrectAnswers($totalCorrectAnswers, $totalQuestions);
      $resultGradeScale = $this->elearningExerciseUtils->resultGradeScale();
      $grade = $this->elearningResultRangeUtils->calculateGrade($totalCorrectAnswers, $totalQuestions);
      $strResultGrades = $this->renderResultGrades('', $grade);
      $strResultRatio = $this->renderResultRatio('', $averageCorrectAnswers, $resultGradeScale);
      $strResultPoints = $this->renderResultPoints('', $totalPoints);
      $labelGrade = $this->userUtils->getTipPopup($this->websiteText[40], $this->websiteText[41], 300, 300);
      $labelRatio = $this->userUtils->getTipPopup($this->websiteText[42], $this->websiteText[43], 300, 200);
      $labelPoints = $this->userUtils->getTipPopup($this->websiteText[44], $this->websiteText[45], 300, 200);
      $str .= "<table>"
        . "<tr><td align='center'><div class='elearning_course_header'>$labelGrade</div></td><td align='center'><div class='elearning_course_header'>$labelRatio</div></td><td align='center'><div class='elearning_course_header'>$labelPoints</div></td></tr>"
        . "<tr><td align='center'><div class='elearning_course_points'>$strResultGrades</div></td><td align='center'><div class='elearning_course_points'>$strResultRatio</div></td><td align='center'><div class='elearning_course_points'>$strResultPoints</div></td></tr>"
        . "</table>";
    }

    return($str);
  }

  // Get the participant's name of the exercise results
  function getParticipantName($elearningResult) {
    $firstname = $elearningResult->getFirstname();
    $lastname = $elearningResult->getLastname();

    if (!$firstname || !$lastname) {
      $elearningSubscriptionId = $elearningResult->getSubscriptionId();
      if ($elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
        $userId = $elearningSubscription->getUserId();
        if ($user = $this->userUtils->selectById($userId)) {
          $firstname = $user->getFirstname();
          $lastname = $user->getLastname();
        }
      }
    }

    $strName = $firstname . ' ' . $lastname;

    return($strName);
  }

  // Render the exercise results of a class with all the results being collated
  // and spread out by the questions of the exercise
  function renderClassMixedResultsGraph($elearningResults) {
    global $gUtilsUrl;
    global $gElearningUrl;
    global $gJSNoStatus;
    global $gIsPhoneClient;

    $str = '';

    $this->loadLanguageTexts();

    $totalCorrectAnswers = 0;
    $totalIncorrectAnswers = 0;
    $totalQuestions = 0;
    $totalPoints = 0;
    $nbExercise = 0;
    $highestQuestionNb = 0;

    if (count($elearningResults) > 0) {
      $str .= "<table><tr>";

      $elearningMixedResultsForQuestions = array();

      foreach ($elearningResults as $elearningResult) {
        $elearningResultId = $elearningResult->getId();
        $elearningExerciseId = $elearningResult->getElearningExerciseId();

        $resultTotals = $this->getExerciseTotals($elearningExerciseId, $elearningResultId);
        $nbQuestions = $this->getResultNbQuestions($resultTotals);
        $nbCorrectAnswers = $this->getResultNbCorrectAnswers($resultTotals);
        $nbIncorrectAnswers = $this->getResultNbIncorrectAnswers($resultTotals);
        $points = $this->getResultNbPoints($resultTotals);
        $grade = $this->elearningResultRangeUtils->calculateGrade($nbCorrectAnswers, $nbQuestions);

        $highestQuestionNb = max($highestQuestionNb, $nbQuestions);

        $totalCorrectAnswers = $totalCorrectAnswers + $nbCorrectAnswers;
        $totalIncorrectAnswers = $totalIncorrectAnswers + $nbIncorrectAnswers;
        $totalQuestions = $totalQuestions + $nbQuestions;
        $totalPoints = $totalPoints + $points;
        $nbExercise++;
      }

      foreach ($elearningResults as $elearningResult) {
        $elearningExerciseId = $elearningResult->getElearningExerciseId();
        if ($elearningExercisePages = $this->elearningExercisePageUtils->selectByExerciseId($elearningExerciseId)) {
          foreach ($elearningExercisePages as $elearningExercisePage) {
            $elearningExercisePageId = $elearningExercisePage->getId();
            if ($elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePageId)) {
              foreach ($elearningQuestions as $elearningQuestion) {
                $elearningQuestionId = $elearningQuestion->getId();
                $isIncorrect = false;
                if ($isCorrect = $this->isACorrectAnswer($elearningResultId, $elearningQuestionId)) {
                } else if ($isAnswered = $this->isAnswered($elearningResultId, $elearningQuestionId)) {
                  $isIncorrect = true;
                }
                if (!isset($elearningMixedResultsForQuestions[$elearningQuestionId])) {
                  $elearningQuestionId = $elearningQuestion->getId();
                  $question = $elearningQuestion->getQuestion();
                  if ($isCorrect) {
                    $nbCorrectAnswers = 1;
                  }
                  if ($isIncorrect) {
                    $nbIncorrectAnswers = 1;
                  }
                  $elearningMixedResultsForQuestions[$elearningQuestionId] = array($nbCorrectAnswers, $nbIncorrectAnswers, $question);
                } else {
                  if ($isCorrect) {
                    $nbCorrectAnswers = $elearningMixedResultsForQuestions[$elearningQuestionId][0];
                    $nbCorrectAnswers++;
                    $elearningMixedResultsForQuestions[$elearningQuestionId][0] = $nbCorrectAnswers;
                  }
                  if ($isIncorrect) {
                    $nbIncorrectAnswers = $elearningMixedResultsForQuestions[$elearningQuestionId][1];
                    $nbIncorrectAnswers++;
                    $elearningMixedResultsForQuestions[$elearningQuestionId][1] = $nbIncorrectAnswers;
                  }
                }
              }
            }
          }
        }
      }

      $nbParticipants = count($elearningResults);

      foreach ($elearningMixedResultsForQuestions as $elearningQuestionId => $elearningMixedResultsForQuestion) {
        list($nbCorrectAnswers, $nbIncorrectAnswers, $question) = $elearningMixedResultsForQuestion;
        $titlePrefix = $question;
        $str .= "<td>"
          . $this->renderQuestionClassResultsGraph($elearningQuestionId, $nbParticipants, $nbCorrectAnswers, $nbIncorrectAnswers, false, false, $titlePrefix)
          . "</td>";
      }

      $str .= "</tr></table>";

      $averageCorrectAnswers = $this->calculateAverageCorrectAnswers($totalCorrectAnswers, $totalQuestions);
      $resultGradeScale = $this->elearningExerciseUtils->resultGradeScale();
      $grade = $this->elearningResultRangeUtils->calculateGrade($totalCorrectAnswers, $totalQuestions);
      $strResultGrades = $this->renderResultGrades('', $grade);
      $strResultRatio = $this->renderResultRatio('', $averageCorrectAnswers, $resultGradeScale);
      $strResultPoints = $this->renderResultPoints('', $totalPoints);
      $labelGrade = $this->userUtils->getTipPopup($this->websiteText[40], $this->websiteText[41], 300, 300);
      $labelRatio = $this->userUtils->getTipPopup($this->websiteText[42], $this->websiteText[43], 300, 200);
      $labelPoints = $this->userUtils->getTipPopup($this->websiteText[44], $this->websiteText[45], 300, 200);
      $str .= "<table>"
        . "<tr><td align='center'><div class='elearning_course_header'>$labelGrade</div></td><td align='center'><div class='elearning_course_header'>$labelRatio</div></td><td align='center'><div class='elearning_course_header'>$labelPoints</div></td></tr>"
        . "<tr><td align='center'><div class='elearning_course_points'>$strResultGrades</div></td><td align='center'><div class='elearning_course_points'>$strResultRatio</div></td><td align='center'><div class='elearning_course_points'>$strResultPoints</div></td></tr>"
        . "</table>";
    }

    return($str);
  }

  // Render the grade stars
  function renderStars($nbQuestions, $nbCorrectAnswers) {
    global $gCommonImagesUrl;
    global $gImageStarYellow;
    global $gImageStarWhite;

    if ($nbQuestions > 0) {
      $percentage = round($nbCorrectAnswers * 100 / $nbQuestions);
    } else {
      $percentage = 0;
    }

    $yellow = "<img src='$gCommonImagesUrl/$gImageStarYellow' border='0' title=''>";
    $white = "<img src='$gCommonImagesUrl/$gImageStarWhite' border='0' title=''>";

    if ($percentage == 0) {
      $str = "$white $white $white $white $white";
    } else if ($percentage > 0 && $percentage < 20) {
      $str = "$yellow $white $white $white $white";
    } else if ($percentage >= 20 && $percentage < 40) {
      $str = "$yellow $yellow $white $white $white";
    } else if ($percentage >= 40 && $percentage < 60) {
      $str = "$yellow $yellow $yellow $white $white";
    } else if ($percentage >= 60 && $percentage < 80) {
      $str = "$yellow $yellow $yellow $yellow $white";
    } else {
      $str = "$yellow $yellow $yellow $yellow $yellow";
    }

    return($str);
  }

  // Send by email a message to alert that an exercise has been done
  function sendExerciseAlert($elearningResultId) {
    global $gElearningUrl;
    global $gJSNoStatus;

    if ($elearningResult = $this->selectById($elearningResultId)) {
      $firstname = $elearningResult->getFirstname();
      $lastname = $elearningResult->getLastname();
      $message = $elearningResult->getMessage();
      $email = $elearningResult->getEmail();
      $elearningExerciseId = $elearningResult->getElearningExerciseId();
      $elearningSubscriptionId = $elearningResult->getSubscriptionId();

      if ($elearningSubscriptionId || $email) {
        if ($elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId)) {
          $name = $elearningExercise->getName();
          $description = $elearningExercise->getDescription();

          $body = $this->templateUtils->renderDefaultModelCssPageProperties();

          $body .= "\n<div class='system'>"
            . "<div class='system_email_content'>";

          if ($this->elearningExerciseUtils->displayWebsiteLogo()) {
            $logo = $this->profileUtils->getLogoFilename();
            if ($logo && is_file($this->profileUtils->filePath . $logo)) {
              $body .= "<img src='$this->profileUtils->fileUrl/$logo' title='' alt='' /><br><br>";
            }
          }

          // Create a temporary url for the link in the email
          $tokenName = ELEARNING_EXERCISE_ALERT_TOKEN_NAME;
          $tokenDuration = $this->adminUtils->getLoginTokenDuration();
          $tokenValue = $this->uniqueTokenUtils->create($tokenName, $tokenDuration);

          $websiteEmail = $this->profileUtils->getProfileValue("website.email");
          $websiteName = $this->profileUtils->getProfileValue("website.name");

          $recipientEmail = $websiteEmail;
          $recipientName = $websiteName;

          if ($elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
            $elearningTeacherId = $elearningSubscription->getTeacherId();
            if ($elearningTeacher = $this->elearningTeacherUtils->selectById($elearningTeacherId)) {
              $userId = $elearningTeacher->getUserId();
              if ($user = $this->userUtils->selectById($userId)) {
                $recipientEmail = $user->getEmail();
                $recipientName = $user->getFirstname() . ' ' . $user->getLastname();
              }
            }
          }

          $exerciseLink = "<a href='$gElearningUrl/exercise/compose.php?elearningExerciseId=$elearningExerciseId&tokenName=$tokenName&tokenValue=$tokenValue&siteEmail=$websiteEmail' $gJSNoStatus>$name</a>";

          $body .= $this->mlText[7] . ' <b>' . $firstname . ' ' . $lastname
            . '</b> ' . $this->mlText[8] . ' <b>' . $exerciseLink . '</b>';
          if ($description) {
            $body .= ' - ' . $description;
          }

          if (LibEmail::validate($email)) {
            if ($firstname || $lastname) {
              $visitorName = '<b>' . $firstname . ' ' . $lastname . '</b>';
            } else {
              $visitorName = ' ';
            }

            $body .= "<br><br>" . $this->mlText[32] . " <a href='$gElearningUrl/result/send_comment.php?elearningResultId=$elearningResultId' $gJSNoStatus>" . $this->mlText[33] . "</a> " . $this->mlText[34];

            $body .= '<br><br>' . $this->mlText[9] . ' ' . $visitorName . ' ' . $this->mlText[13] . ' ' . "<a href='mailto:$email'>$email</a>";
          }

          if (trim($message)) {
            $body .= "<br><br>" . $this->mlText[16] . ' ' . $message;
          }

          $resultsLink = $this->mlText[10] . ' ' . "<a href='$gElearningUrl/result/view.php?elearningResultId=$elearningResultId&tokenName=$tokenName&tokenValue=$tokenValue&siteEmail=$websiteEmail' $gJSNoStatus>" .  $this->mlText[12] . "</a>";

          $body .= '<br><br>' . $resultsLink;

          $body .= '<br><br>' . $this->renderResult($elearningResultId, true);

          $body = LibString::stripJavascriptTags($body);
          $body = str_replace("display:none", "display:block", $body);

          $attachedFiles = $this->getEmailAttachment($body);

          $body = $this->replaceImageTags($body);

          // Create a temporary url for the link in the email
          $tokenName = ELEARNING_EXERCISE_ALERT_TOKEN_NAME;
          $tokenDuration = $this->adminUtils->getLoginTokenDuration();
          $tokenValue = $this->uniqueTokenUtils->create($tokenName, $tokenDuration);

          $body .= '<br><br>' . $resultsLink;

          $body .= '<br><br>' . $websiteName;

          $body .= '</div></div>';

          $websiteName = $this->profileUtils->getProfileValue("website.name");

          $subject = $this->mlText[5] . ' ' . $websiteName . ' ' . $this->mlText[6];

          LibEmail::sendMail($recipientEmail, $recipientName, $subject, $body, $websiteEmail, $websiteName, $attachedFiles);
        }
      }
    }
  }

  // Format the results of an exercise for an email display
  function replaceImageTags($body) {
    global $gImagesUserUrl;

    $body = str_replace($this->elearningExerciseUtils->imageFileUrl . "/", "cid:", $body);
    $body = str_replace($this->elearningExercisePageUtils->imageFileUrl . "/", "cid:", $body);
    $body = str_replace($this->elearningQuestionUtils->imageFileUrl . "/", "cid:", $body);
    $body = str_replace($gImagesUserUrl . "/", "cid:", $body);
    $body = str_replace($this->profileUtils->fileUrl . "/", "cid:", $body);

    return($body);
  }

  // Get the email attached images
  function getEmailAttachment($body) {
    global $gImagesUserPath;
    global $gImagesUserUrl;

    // Create the array and transform the image tags in the body
    $attachedImages = array();

    // Transform the image urls into email image tags
    $urls = LibString::getAllUrls($body, "src");
    $matches = $urls['src'];
    if ($matches) {
      for($i = 0; $i < count($matches); $i++) {
        $matches[$i] = str_replace($this->elearningExerciseUtils->imageFileUrl . '/', $this->elearningExerciseUtils->imageFilePath, $matches[$i]);
        $matches[$i] = str_replace($this->elearningExercisePageUtils->imageFileUrl . '/', $this->elearningExercisePageUtils->imageFilePath, $matches[$i]);
        $matches[$i] = str_replace($this->elearningQuestionUtils->imageFileUrl . '/', $this->elearningQuestionUtils->imageFilePath, $matches[$i]);
        $matches[$i] = str_replace($this->profileUtils->fileUrl . '/', $this->profileUtils->filePath, $matches[$i]);
        $matches[$i] = str_replace($gImagesUserUrl . '/', $gImagesUserPath, $matches[$i]);
      }

      $temp = array_unique($matches);
      $matches = array_values($temp);
      $attachedImages = $matches;
    }

    return($attachedImages);
  }

  // Send the results comment to the participant
  function sendCommentToParticipant($elearningResult) {
    global $gElearningUrl;

    $this->loadLanguageTexts();

    $body = $this->templateUtils->renderDefaultModelCssPageProperties();

    $body .= "\n<div class='system'>" . "<div class='system_email_content'>";

    $body .= $this->renderLogo();

    $elearningExerciseId = $elearningResult->getElearningExerciseId();
    $firstname = $elearningResult->getFirstname();
    $lastname = $elearningResult->getLastname();
    $email = $elearningResult->getEmail();
    $comment = $elearningResult->getComment();

    if ($firstname || $lastname) {
      $strName = "$firstname $lastname";
    } else {
      $strName = $email;
    }

    $elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId);
    $strExerciseName = "<a href='$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus title=''>" . $elearningExercise->getName() . "</a>";

    $websiteName = $this->profileUtils->getProfileValue("website.name");
    $websiteEmail = $this->profileUtils->getProfileValue("website.email");

    $subject = $this->websiteText[18] . ' ' . $websiteName;

    $body .= $this->websiteText[35] . '<br><br>' . '"' . $comment . '"';

    $body .= '<br><br>' . $this->websiteText[37] . '<br><br>' . $strExerciseName;

    $body .= "<br><br>" . $this->websiteText[36]
      . "<br><br>" . $websiteName;

    $body .= '</div></div>';

    if ($email) {
      LibEmail::sendMail($email, $strName, $subject, $body, $websiteEmail, $websiteName);
    }
  }

  // Send by email the results of an exercise
  function sendResult($elearningResultId, $otherEmail = '') {
    global $gImagesUserPath;
    global $gImagesUserUrl;

    $this->loadLanguageTexts();

    $email = '';
    $firstname = '';
    $lastname = '';
    if ($elearningResult = $this->selectById($elearningResultId)) {
      $firstname = $elearningResult->getFirstname();
      $lastname = $elearningResult->getLastname();
      $email = $elearningResult->getEmail();
    }

    $body = $this->templateUtils->renderDefaultModelCssPageProperties();

    $body = "\n<div class='system'>" . "<div class='system_email_content'>";

    $body .= $this->renderLogo();

    $tokenName = USER_TOKEN_NAME;
    $tokenDuration = $this->userUtils->getLoginTokenDuration();
    $tokenValue = $this->uniqueTokenUtils->create($tokenName, $tokenDuration);

    $body .= $this->renderResultLink($elearningResultId, $tokenName, $tokenValue, $email);

    $body .= '<br><br>' . $this->renderResult($elearningResultId, true);

    $attachedImages = $this->getEmailAttachment($body);

    $body = $this->replaceImageTags($body);

    $websiteName = $this->profileUtils->getProfileValue("website.name");

    $body .= "<br><br>" . $this->websiteText[36] . "<br><br>" . $websiteName;

    $body .= '</div></div>';

    if ($otherEmail && $otherEmail != $email) {
      $firstname = '';
      $lastname = '';
      $email = $otherEmail;
    }

    if ($firstname || $lastname) {
      $strName = "$firstname $lastname";
    } else {
      $strName = $email;
    }

    $websiteName = $this->profileUtils->getProfileValue("website.name");
    $websiteEmail = $this->profileUtils->getProfileValue("website.email");

    $subject = $this->websiteText[2] . ' ' . $websiteName;

    if ($email) {
      LibEmail::sendMail($email, $strName, $subject, $body, $websiteEmail, $websiteName, $attachedImages);
    }
  }

  function renderLogo() {
    $strLogo = '';

    $logo = $this->profileUtils->getLogoFilename();
    if ($logo && is_file($this->profileUtils->filePath . $logo) && $this->elearningExerciseUtils->displayWebsiteLogo()) {
      $strLogo = "<img src='$this->profileUtils->fileUrl/$logo' title='' alt='' />";
    }

    return($strLogo);
  }

  // Print the result
  function printResult($elearningResultId) {
    $str = LibJavaScript::getJSLib();
    $str .= "\n<script type='text/javascript'>printPage();</script>";
    $str .= $this->renderResult($elearningResultId);

    return($str);
  }

  // Render a link to the exercise results
  function renderResultLink($elearningResultId, $tokenName, $tokenValue, $email) {
    global $gElearningUrl;
    global $gJSNoStatus;

    $str = $this->mlText[10] . ' ' . "<a href='$gElearningUrl/result/display.php?elearningResultId=$elearningResultId&tokenName=$tokenName&tokenValue=$tokenValue&email=$email' $gJSNoStatus>" .  $this->mlText[12] . "</a>";

    return($str);
  }


  // Render the results of an exercise after it has been done and its results saved
  // The number of right answers are calculated from the stored results
  // Another render method exists but it renders results for exercises being done
  // with the results not yet saved
  function renderResult($elearningResultId, $emailFormat = false) {
    global $gImagesUserUrl;
    global $gElearningUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $str = '';

    if ($elearningResult = $this->selectById($elearningResultId)) {
      $elearningExerciseId = $elearningResult->getElearningExerciseId();
      $exerciseDate = $this->clockUtils->systemToLocalNumericDate($elearningResult->getExerciseDate());
      $exerciseElapsedTime = $elearningResult->getExerciseElapsedTime();
      $firstname = $elearningResult->getFirstname();
      $lastname = $elearningResult->getLastname();
      $email = $elearningResult->getEmail();
      $comment = $elearningResult->getComment();
      $hideComment = $elearningResult->getHideComment();

      // Get the exercise details
      $name = '';
      $description = '';
      $maxDuration = 0;
      if ($elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId)) {
        $exerciseName = $elearningExercise->getName();
        $description = $elearningExercise->getDescription();
        $maxDuration = $elearningExercise->getMaxDuration();
      }

      // Get the global maximum allowed duration for the exercise if any
      $maxDuration = $this->elearningExerciseUtils->getMaximumDuration($elearningExerciseId);

      // Calculate the time taken by the participant to do the exercise
      $elapsedTime = $this->elearningExerciseUtils->renderElapsedTime($exerciseElapsedTime, 0, $maxDuration);

      // Calculate the results
      $resultTotals = $this->getExerciseTotals($elearningExerciseId, $elearningResultId);
      $nbQuestions = $this->getResultNbQuestions($resultTotals);
      $nbCorrectAnswers = $this->getResultNbCorrectAnswers($resultTotals);
      $nbNotAnswered = $this->getResultNbNotAnswered($resultTotals);

      // Calculate the points
      $points = $this->getResultNbPoints($resultTotals);

      $str = '';

      $str .= "\n<div class='elearning_exercise'>";

      $str .= "\n<div class='elearning_result_title'>" . $this->websiteText[17] . "</div>";

      $str .= "\n<div class='elearning_exercise_name'>" . $exerciseName . "</div>";

      $str .= "\n<div class='elearning_exercise_description'>" . $description . "</div>";

      $str .= "\n<div class='elearning_exercise_comment'>"
        . $this->websiteText[3] . ' ' . $exerciseDate
        . "</div>";

      if ($maxDuration > 0) {
        $str .= "\n<div class='elearning_exercise_max_duration'>"
          . $this->websiteText[14] . " <span class='elearning_exercise_max_duration_min'>" . $maxDuration . '</span>mn '
          . "</div>";
      }

      $str .= "\n<div class='elearning_exercise_elapsed_time'>"
        . $this->websiteText[15] . " " . $elapsedTime . "</div>";

      $str .= "\n<div class='elearning_exercise_comment'><span class='elearning_exercise_label'>"
        . $this->websiteText[1] . '</span> ' . $points . " <span class='elearning_exercise_label'>" . $this->websiteText[22] . "</span> " . $nbCorrectAnswers . " <span class='elearning_exercise_label'>" . $this->websiteText[26] . "</span> " . $nbQuestions . " <span class='elearning_exercise_label'>" . $this->websiteText[27]
        . "</div>";

      if ($nbNotAnswered > 0) {
        $nbWasAnswered = $nbQuestions - $nbNotAnswered;
        $str .= "\n<div class='elearning_exercise_comment'><span class='elearning_exercise_label'>"
          . $this->websiteText[30] . '</span> ' . $nbWasAnswered . " <span class='elearning_exercise_label'>" . $this->websiteText[27] . "</span>"
          . "</div>";

        $str .= "\n<div class='elearning_exercise_comment'><span class='elearning_exercise_label'>"
          . $this->websiteText[29] . '</span> ' . $nbNotAnswered . " <span class='elearning_exercise_label'>" . $this->websiteText[27] . "</span>"
          . "</div>";
      }

      if (trim($comment) && !$hideComment) {
        $str .= "\n<div class='elearning_exercise_comment'><span class='elearning_exercise_label'>"
          . $this->websiteText[28] . '</span> ' . $comment
          . "</div>";
      }

      // Get the exercise page of questions
      $elearningExercisePages = $this->elearningExercisePageUtils->selectByExerciseId($elearningExerciseId);
      foreach ($elearningExercisePages as $elearningExercisePage) {
        $elearningExercisePageId = $elearningExercisePage->getId();
        $name = $elearningExercisePage->getName();
        $description = $elearningExercisePage->getDescription();

        $str .= "\n<div class='elearning_exercise_page'>";

        $str .= "\n<div class='elearning_exercise_page_name'>$name</div>";
        if ($description) {
          $str .= "\n<div class='elearning_exercise_page_description'>$description</div>";
        }

        $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePageId);
        foreach ($elearningQuestions as $elearningQuestion) {
          $question = $elearningQuestion->getQuestion();
          $elearningQuestionId = $elearningQuestion->getId();

          $isCorrect = $this->isACorrectAnswer($elearningResultId, $elearningQuestionId);

          $participantQuestionAnswer = $this->elearningQuestionResultUtils->getParticipantAnswers($elearningResultId, $elearningQuestionId);

          $str .= $this->elearningExercisePageUtils->renderQuestionResult($elearningExercisePage, $elearningQuestion, $participantQuestionAnswer, $isCorrect, false);
        }

        $str .= '</div>';
      }

      $str .= $this->elearningExerciseUtils->renderCopyright();

      $str .= $this->elearningExerciseUtils->renderAddress();

      if (!$gIsPhoneClient && !$emailFormat) {
        if (!$this->preferenceUtils->getValue("ELEARNING_HIDE_SOCIAL_BUTTONS")) {
          $strLink = "$gElearningUrl/result/display.php?elearningResultId=$elearningResultId";
          $str .= "<div class='elearning_social_buttons'>";
          $str .= $this->commonUtils->renderSocialNetworksButtons($exerciseName, $strLink);
          $str .= " </div>";
        }
      }

      $str .= '</div>';
    }

    $str = "<table style='width:100%;'><tr><td>$str</td></tr></table>";

    return($str);
  }

  // Render the results graph of a course
  function renderGraph($elearningSubscriptionId) {
    global $gImagesUserUrl;

    $this->loadLanguageTexts();

    $str = '';

    if ($elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
      $courseId = $elearningSubscription->getCourseId();
      $classId = $elearningSubscription->getClassId();
      $userId = $elearningSubscription->getUserId();

      $courseName = '';
      if ($elearningCourse = $this->elearningCourseUtils->selectById($courseId)) {
        $courseName = $elearningCourse->getName();
      }

      if ($elearningClass = $this->elearningClassUtils->selectById($classId)) {
        $className = $elearningClass->getName();
      } else {
        $className = '';
      }

      if ($user = $this->userUtils->selectById($userId)) {
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $email = $user->getEmail();
        if ($firstname || $lastname) {
          $name = "$firstname $lastname";
        } else {
          $name = $email;
        }
      }

      $str = '';

      $str .= "\n<div class='elearning_exercise'>";

      $str .= "\n<div class='elearning_course_list_title'>" . $this->websiteText[20] . "</div>";

      $str .= "\n<div class='elearning_course_list_course_name'>" . $courseName . "</div>";

      $str .= "\n<br/>";

      $str .= "\n<div class='elearning_course_list_participant_name'>" . $this->websiteText[24] . ' ' . $name . "</div>";

      if ($className) {
        $str .= "\n<br/>";

        $str .= "\n<div class='elearning_course_list_class_name'>" . $this->websiteText[23] . ' ' . $className . "</div>";
      }

      if ($elearningResults = $this->selectBySubscriptionId($elearningSubscriptionId)) {
        $str .= "\n<br/>";

        $str .= $this->renderResultsGraph($elearningResults);

        $str = "<table style='width:100%;'><tr><td>$str</td></tr></table>";
      }

      $str .= "\n</div>";
    }

    return($str);
  }

}

?>
