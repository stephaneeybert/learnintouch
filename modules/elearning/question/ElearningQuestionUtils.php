<?

class ElearningQuestionUtils extends ElearningQuestionDB {

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $audioFileSize;
  var $audioFilePath;
  var $audioFileUrl;

  var $currentExercise;

  var $preferenceUtils;
  var $playerUtils;
  var $elearningAnswerUtils;
  var $elearningSolutionUtils;
  var $elearningResultUtils;
  var $elearningExerciseUtils;
  var $elearningExercisePageUtils;
  var $elearningQuestionResultUtils;
  var $fileUploadUtils;

  function ElearningQuestionUtils() {
    $this->ElearningQuestionDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'elearning/question/image/';
    $this->imageFileUrl = $gDataUrl . '/elearning/question/image';

    $this->audioFileSize = 4096000;
    $this->audioFilePath = $gDataPath . 'elearning/question/audio/';
    $this->audioFileUrl = $gDataUrl . '/elearning/question/audio';

    $this->currentExercise = "elearningCurrentExercise";
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'elearning')) {
        mkdir($gDataPath . 'elearning');
      }
      if (!is_dir($gDataPath . 'elearning/question')) {
        mkdir($gDataPath . 'elearning/question');
      }
      mkdir($this->imageFilePath);
      chmod($this->imageFilePath, 0755);
    }

    if (!is_dir($this->audioFilePath)) {
      if (!is_dir($gDataPath . 'elearning')) {
        mkdir($gDataPath . 'elearning');
      }
      if (!is_dir($gDataPath . 'elearning/question')) {
        mkdir($gDataPath . 'elearning/question');
      }
      mkdir($this->audioFilePath);
      chmod($this->audioFilePath, 0755);
    }
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imageFilePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->imageIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (file_exists($this->imageFilePath . $oneFile)) {
            unlink($this->imageFilePath . $oneFile);
          }
        }
      }
    }
    closedir($handle);
  }

  // Check if an image is being used
  function imageIsUsed($image) {
    $isUsed = true;

    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByImage($image)) {
      if ($result->getRowCount() < 1) {
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedAudioFiles() {
    $handle = opendir($this->audioFilePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->audioIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (file_exists($this->audioFilePath . $oneFile)) {
            unlink($this->audioFilePath . $oneFile);
          }
        }
      }
    }
    closedir($handle);
  }

  // Check if an audio file is being used
  function audioIsUsed($audio) {
    $isUsed = true;

    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByAudio($audio)) {
      if ($result->getRowCount() < 1) {
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Get the next available list order
  function getNextListOrder($elearningExercisePageId) {
    $listOrder = 1;
    if ($elearningQuestions = $this->selectByExercisePage($elearningExercisePageId)) {
      $total = count($elearningQuestions);
      if ($total > 0) {
        $elearningQuestion = $elearningQuestions[$total - 1];
        $listOrder = $elearningQuestion->getListOrder() + 1;
      }
    }

    return($listOrder);
  }

  // Swap the curent object with the next one
  function swapWithNext($id) {
    $this->repairListOrder($id);

    $currentObject = $this->selectById($id);
    $currentListOrder = $currentObject->getListOrder();

    // Get the next object and its list order
    if (!$nextObject = $this->selectNext($id)) {
      return;
    }
    $nextListOrder = $nextObject->getListOrder();

    // Update the list orders
    $currentObject->setListOrder($nextListOrder);
    $this->update($currentObject);
    $nextObject->setListOrder($currentListOrder);
    $this->update($nextObject);
  }

  // Swap the curent object with the previous one
  function swapWithPrevious($id) {
    $this->repairListOrder($id);

    $currentObject = $this->selectById($id);
    $currentListOrder = $currentObject->getListOrder();

    // Get the previous object and its list order
    if (!$previousObject = $this->selectPrevious($id)) {
      return;
    }
    $previousListOrder = $previousObject->getListOrder();

    // Update the list orders
    $currentObject->setListOrder($previousListOrder);
    $this->update($currentObject);
    $previousObject->setListOrder($currentListOrder);
    $this->update($previousObject);
  }

  // Repair the order if some order numbers are identical
  // If, by accident, some objects have the same list order
  // (it shouldn't happen) then assign a new list order to each of them
  function repairListOrder($id) {
    if ($elearningQuestion = $this->selectById($id)) {
      $listOrder = $elearningQuestion->getListOrder();
      $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
      if ($elearningQuestions = $this->selectByListOrder($elearningExercisePageId, $listOrder)) {
        if (($listOrder == 0) || (count($elearningQuestions)) > 1) {
          $this->resetListOrder($elearningExercisePageId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($elearningQuestion = $this->selectById($id)) {
      $listOrder = $elearningQuestion->getListOrder();
      $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
      if ($elearningQuestion = $this->selectByNextListOrder($elearningExercisePageId, $listOrder)) {
        return($elearningQuestion);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($elearningQuestion = $this->selectById($id)) {
      $listOrder = $elearningQuestion->getListOrder();
      $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
      if ($elearningQuestion = $this->selectByPreviousListOrder($elearningExercisePageId, $listOrder)) {
        return($elearningQuestion);
      }
    }
  }

  // Place the current object before another target one
  function placeBefore($currentObjectId, $targetObjectId) {
    if ($currentObjectId == $targetObjectId) {
      return;
    }

    if ($nextObject = $this->selectNext($currentObjectId)) {
      if ($nextObject->getId() == $targetObjectId) {
        return;
      }
    }

    $currentObject = $this->selectById($currentObjectId);

    if ($targetObject = $this->selectById($targetObjectId)) {
      $targetObjectListOrder = $targetObject->getListOrder();
    } else {
      $targetObjectListOrder = '';
    }

    // Reset the list order of the target object and all its followers
    $elearningExercisePageId = $targetObject->getElearningExercisePage();
    $currentListOrder = $currentObject->getListOrder();

    if ($objects = $this->selectByExercisePage($elearningExercisePageId)) {
      $nextListOrder = $targetObjectListOrder + 1;
      foreach($objects as $object) {
        $listOrder = $object->getListOrder();
        // Do not reset the list order of the objects preceding the target object
        if ($listOrder < $targetObjectListOrder) {
          continue;
        }
        $object->setListOrder($nextListOrder);
        $this->update($object);
        $nextListOrder++;
      }
    }

    // Update the list order of the current object
    // and set it with the list order of the specified target
    $currentObject->setListOrder($targetObjectListOrder);
    $currentObject->setElearningExercisePage($targetObject->getElearningExercisePage());
    $this->update($currentObject);

    return(true);
  }

  // Place the current object after another target one
  function placeAfter($currentObjectId, $targetObjectId) {
    if ($currentObjectId == $targetObjectId) {
      return;
    }

    if ($nextObject = $this->selectPrevious($currentObjectId)) {
      if ($nextObject->getId() == $targetObjectId) {
        return;
      }
    }

    $currentObject = $this->selectById($currentObjectId);

    if ($targetObject = $this->selectById($targetObjectId)) {
      $targetObjectListOrder = $targetObject->getListOrder();
    } else {
      $targetObjectListOrder = '';
    }

    // Reset the list order of the followers of the target object
    $elearningExercisePageId = $targetObject->getElearningExercisePage();
    $currentListOrder = $currentObject->getListOrder();

    if ($objects = $this->selectByExercisePage($elearningExercisePageId)) {
      $nextListOrder = $targetObjectListOrder + 2;
      foreach($objects as $object) {
        $listOrder = $object->getListOrder();
        // Do not reset the list order of the objects preceding or equal to the target object
        if ($listOrder <= $targetObjectListOrder) {
          continue;
        }
        $object->setListOrder($nextListOrder);
        $this->update($object);
        $nextListOrder++;
      }
    }

    // Update the list order of the current object
    // and set it with the list order of the specified target
    $currentObject->setListOrder($targetObjectListOrder + 1);
    $currentObject->setElearningExercisePage($targetObject->getElearningExercisePage());
    $this->update($currentObject);

    return(true);
  }

  // Duplicate a question
  function duplicate($elearningQuestionId, $elearningExercisePageId, $question = '', $points = '') {
    if ($elearningQuestion = $this->selectById($elearningQuestionId)) {
      // Set the properties
      if ($elearningExercisePageId) {
        $elearningQuestion->setElearningExercisePage($elearningExercisePageId);
      }
      if ($question) {
        $elearningQuestion->setQuestion($question);
      }
      if ($points) {
        $elearningQuestion->setPoints($points);
      }

      $this->insert($elearningQuestion);
      $lastInsertElearningQuestionId = $this->getLastInsertId();

      // Duplicate the answers
      $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId);
      foreach ($elearningAnswers as $elearningAnswer) {
        $elearningAnswerId = $elearningAnswer->getId();
        $elearningAnswer->setElearningQuestion($lastInsertElearningQuestionId);
        $this->elearningAnswerUtils->insert($elearningAnswer);
        $lastInsertElearningAnswerId = $this->elearningAnswerUtils->getLastInsertId();

        // Duplicate the solution
        if ($elearningSolution = $this->elearningSolutionUtils->selectByQuestionAndAnswer($elearningQuestionId, $elearningAnswerId)) {
          $elearningSolution->setElearningQuestion($lastInsertElearningQuestionId);
          $elearningSolution->setElearningAnswer($lastInsertElearningAnswerId);
          $this->elearningSolutionUtils->insert($elearningSolution);
        }
      }

      return($lastInsertElearningQuestionId);
    }
  }

  // Delete a question
  function deleteQuestion($elearningQuestionId) {
    $this->elearningResultUtils->deleteQuestionResults($elearningQuestionId);

    if ($elearningSolutions = $this->elearningSolutionUtils->selectByQuestion($elearningQuestionId)) {
      foreach ($elearningSolutions as $elearningSolution) {
        $this->elearningSolutionUtils->delete($elearningSolution->getId());
      }
    }

    if ($elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId)) {
      foreach ($elearningAnswers as $elearningAnswer) {
        $this->elearningAnswerUtils->deleteAnswer($elearningAnswer->getId());
      }
    }

    $this->delete($elearningQuestionId);
  }

  // Check if the content was created by the user
  function createdByUser($elearningQuestionId, $userId) {
    $byUser = false;

    if ($elearningQuestion = $this->selectById($elearningQuestionId)) {
      $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
      $byUser = $this->elearningExercisePageUtils->createdByUser($elearningExercisePageId, $userId);
    }

    return($byUser);
  }

  // Check if the questions could be reordered
  // That is, if the maximum order number is not equal to the number of questions
  function couldBeReordered($elearningExercisePageId) {
    if ($elearningQuestions = $this->selectByExercisePage($elearningExercisePageId)) {
      if (count($elearningQuestions) > 0) {
        $lastListOrder = $elearningQuestions[count($elearningQuestions) - 1]->getListOrder();
        if ($lastListOrder > count($elearningQuestions)) {
          return(true);
        }
      }
    }

    return(false);
  }

  // Reset the answers of the question
  function resetAnswers($elearningQuestionId) {
    if ($elearningQuestion = $this->selectById($elearningQuestionId)) {
      $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
      if ($elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId)) {
        if ($elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId)) {
          if ($this->elearningExercisePageUtils->typeIsRequireOneOrMoreCorrectAnswers($elearningExercisePage) || $this->elearningExercisePageUtils->typeIsRequireAllPossibleAnswers($elearningExercisePage)) {
            foreach ($elearningAnswers as $elearningAnswer) {
              $elearningAnswerId = $elearningAnswer->getId();
              LibSession::delSessionValue($this->renderUniqueQuestionId($elearningQuestionId, $elearningAnswerId));
            }
          } else if ($this->elearningExercisePageUtils->typeIsDragAndDropOrderSentence($elearningExercisePage)) {
            LibSession::delSessionValue($this->renderUniqueQuestionId($elearningQuestionId));
            LibSession::delSessionValue($this->renderUniqueAnswerOrderId($elearningQuestionId));
          } else {
            LibSession::delSessionValue($this->renderUniqueQuestionId($elearningQuestionId));
          }
        }
      }
    }
  }

  // Reset all the list orders some are mistakenly the same
  function resetListOrder($elearningExercisePageId, $forceReset = false, $chronological = false) {
    if ($elearningExercisePageId && $this->countDuplicateListOrderRows($elearningExercisePageId) > 0 || $forceReset) {
      if ($chronological) {
        $elearningQuestions = $this->selectByExercisePageOrderById($elearningExercisePageId);
      } else {
        $elearningQuestions = $this->selectByExercisePage($elearningExercisePageId);
      }
      if ($elearningQuestions) {
        if (count($elearningQuestions) > 0) {
          $listOrder = 0;
          foreach ($elearningQuestions as $elearningQuestion) {
            $listOrder = $listOrder + 1;
            $elearningQuestion->setListOrder($listOrder);
            $this->update($elearningQuestion);
          }
        }
      }
    }
  }

  function countDuplicateListOrderRows($elearningExercisePageId) {
    $count = 0;

    $this->dataSource->selectDatabase();

    $result = $this->dao->countDuplicateListOrderRows($elearningExercisePageId);

    if ($result) {
      $row = $result->getRow(0);
      $count = $row['count'];
    }

    return($count);
  }

  // Check if a question offers several answers
  function offersSeveralAnswers($elearningQuestionId) {
    $hasSeveral = false;

    $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId);

    if (count($elearningAnswers) > 1) {
      $hasSeveral = true;
    }

    return($hasSeveral);
  }

  // Check if a question displays the an input field to type in a full text
  function typeIsWriteText($elearningQuestion) {
    $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
    $elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId);
    $writeText = $this->elearningExercisePageUtils->typeIsWriteText($elearningExercisePage);

    return($writeText);
  }

  // Check if an exercise page has draggable answers droppable under any questions of the page
  // with several answers or images being droppable under the question
  function typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningQuestion) {
    $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
    $elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId);
    $dragAndDrop = $this->elearningExercisePageUtils->typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningExercisePage);

    return($dragAndDrop);
  }

  // Check if an exercise page has draggable answers droppable in their question
  // so as to compose or complete a sentence
  function typeIsDragAndDropOrderSentence($elearningQuestion) {
    $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
    $elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId);
    return($this->elearningExercisePageUtils->typeIsDragAndDropOrderSentence($elearningExercisePage));
  }

  // Check if a question is of listening sort
  function isListeningContent($elearningQuestionId) {
    $isListening = false;

    if ($elearningQuestion = $this->selectById($elearningQuestionId)) {
      $audio = $elearningQuestion->getAudio();
      if ($audio) {
        $isListening = true;
      } else {
        $audio = '';
        $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
        if ($elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId)) {
          $audio = $elearningExercisePage->getAudio();
          $video = $elearningExercisePage->getVideo();
          $videoUrl = $elearningExercisePage->getVideoUrl();
          if ($audio || $video || $videoUrl) {
            $isListening = true;
          } else {
            $elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
            if ($elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId)) {
              $audio = $elearningExercise->getAudio();
              if ($audio) {
                $isListening = true;
              }
            }
          }
        }
        if (!$isListening) {
          $audio = '';
          if ($elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId)) {
            foreach ($elearningAnswers as $elearningAnswer) {
              $audio = $elearningAnswer->getAudio();
              if ($audio) {
                $isListening = true;
              }
            }
          }
        }
      }
    }

    return($isListening);
  }

  // Check if a question is of writing type
  function isWrittenAnswer($elearningQuestion) {
    $isWritten = false;

    $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
    if ($elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      if ($this->elearningExercisePageUtils->isWrittenAnswer($elearningExercisePage)) {
        $isWritten = true;
      }
    }

    return($isWritten);
  }

  // Check if a question has some results
  function questionHasResults($elearningQuestionId) {
    $hasResults = false;

    if ($elearningQuestion = $this->selectById($elearningQuestionId)) {
      if (count($this->elearningQuestionResultUtils->selectByQuestionId($elearningQuestionId) > 0)) {
        $hasResults = true;
      }
    }

    return($hasResults);
  }

  // Check if a the questions of an exercise page have their hint displayed after the answer field
  function hintAfterAnswer($elearningQuestion) {
    $after = false;

    $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
    if ($elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      $hintPlacement = $elearningExercisePage->getHintPlacement();
      if ($hintPlacement == 'ELEARNING_HINT_AFTER' || !$hintPlacement) {
        $after = true;
      } else if (!$this->elearningExercisePageUtils->typeIsWriteInQuestion($elearningExercisePage) && !$this->elearningExercisePageUtils->typeIsWriteInText($elearningExercisePage) && $hintPlacement == 'ELEARNING_HINT_INSIDE') {
        $after = true;
      }
    }

    return($after);
  }

  // Check if a the questions of an exercise page have their hint displayed before the answer field
  function hintBeforeAnswer($elearningQuestion) {
    $before = false;

    $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
    if ($elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      $hintPlacement = $elearningExercisePage->getHintPlacement();
      if ($hintPlacement == 'ELEARNING_HINT_BEFORE') {
        $before = true;
      }
    }

    return($before);
  }

  // Check if a the questions of an exercise page have their hint displayed inside the answer field
  function hintInsideAnswer($elearningQuestion) {
    $inside = false;

    $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
    if ($elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      $hintPlacement = $elearningExercisePage->getHintPlacement();
      if (($this->elearningExercisePageUtils->typeIsWriteInQuestion($elearningExercisePage) || $this->elearningExercisePageUtils->typeIsWriteInText($elearningExercisePage)) && $hintPlacement == 'ELEARNING_HINT_INSIDE') {
        $inside = true;
      }
    }

    return($inside);
  }

  // Check if a the questions of an exercise page have their hint displayed at the end of the question
  function hintEndOfQuestion($elearningQuestion) {
    $end = false;

    $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
    if ($elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      $hintPlacement = $elearningExercisePage->getHintPlacement();
      if ($hintPlacement == 'ELEARNING_HINT_END_OF_QUESTION') {
        $end = true;
      }
    }

    return($end);
  }

  // Check if a the questions of an exercise page have their hint displayed in a popup window
  function hintInPopup($elearningQuestion) {
    $inPopup = false;

    $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
    if ($elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      $hintPlacement = $elearningExercisePage->getHintPlacement();
      if ($hintPlacement == 'ELEARNING_HINT_IN_POPUP') {
        $inPopup = true;
      }
    }

    return($inPopup);
  }

  // Render a unique DOM id for a question or an answer
  function renderUniqueQuestionId($elearningQuestionId, $elearningAnswerId = '') {
    $id = ELEARNING_QUESTION_ID . $elearningQuestionId;

    if ($elearningAnswerId) {
      $id .= '_' . $elearningAnswerId;
    }

    return($id);
  }

  // Render a unique DOM id for a question input field
  function renderUniqueQuestionInputId($elearningQuestionId) {
    $id = ELEARNING_WRITE_IN_QUESTION . $elearningQuestionId;

    return($id);
  }

  // Render a unique DOM id for a question textarea field
  function renderUniqueQuestionTextareaId($elearningQuestionId) {
    $id = ELEARNING_WRITE_TEXT . $elearningQuestionId;

    return($id);
  }

  // Render a unique DOM id for the order of an answer
  // for a sentence to reorder
  function renderUniqueAnswerOrderId($elearningQuestionId) {
    $id = ELEARNING_ANSWER_ORDER_ID . $elearningQuestionId;

    return($id);
  }

  // Get the answers into an array
  function getAnswersFromStringOfConcatenatedAnswers($participantAnswer) {
    $participantAnswers = array();

    if (strstr($participantAnswer, ELEARNING_ANSWERS_SEPARATOR)) {
      $participantAnswers = explode(ELEARNING_ANSWERS_SEPARATOR, $participantAnswer);

      return($participantAnswers);
    }
  }

  // Check if an answer is a string of concatenated participant answers
  // This is the case for the drag and drop under any questions type
  // with more than one answer given by the participant
  // It is not the case if the participant has given only one answer
  // even if the question type is drag and drop under any questions
  function answerIsStringOfConcatenatedAnswers($participantAnswer) {
    if (is_string($participantAnswer) && strstr($participantAnswer, ELEARNING_ANSWERS_SEPARATOR)) {
      return(true);
    } else {
      return(false);
    }
  }

  // Retrieve the participant answers from the session
  // The session holds the participant answers until the participant has finished the exercise
  function sessionRetrieveParticipantAnswers($elearningExercisePage, $elearningQuestion) {
    // Retrieve all the answers of the question
    $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestion->getId());
    if (count($elearningAnswers) > 0) {
      if ($this->typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningQuestion) || $this->typeIsDragAndDropOrderSentence($elearningQuestion)) {
        $participantAnswer = LibSession::getSessionValue($this->renderUniqueQuestionId($elearningQuestion->getId()));
        if ($this->answerIsStringOfConcatenatedAnswers($participantAnswer)) {
          $participantAnswers = $this->getAnswersFromStringOfConcatenatedAnswers($participantAnswer);
        } else if ($participantAnswer) {
          // Even if there is only one participant answer, consider it as an array
          $participantAnswers = array($participantAnswer);
        } else {
          // Even if there is no participant answer, consider it as an array
          $participantAnswers = array();
        }
        return($participantAnswers);
      } else if ($this->elearningExercisePageUtils->typeIsRequireOneOrMoreCorrectAnswers($elearningExercisePage) || $this->elearningExercisePageUtils->typeIsRequireAllPossibleAnswers($elearningExercisePage)) {
        $participantAnswers = Array();
        foreach ($elearningAnswers as $elearningAnswer) {
          $elearningAnswerId = $elearningAnswer->getId();
          $participantAnswer = LibSession::getSessionValue($this->renderUniqueQuestionId($elearningQuestion->getId(), $elearningAnswerId));
          // Store only the answers selected by the participant
          if ($participantAnswer) {
            array_push($participantAnswers, $elearningAnswerId);
          }
        }

        return($participantAnswers);
      } else {
        $participantAnswer = LibSession::getSessionValue($this->renderUniqueQuestionId($elearningQuestion->getId()));

        return($participantAnswer);
      }
    } else if ($this->typeIsWriteText($elearningQuestion)) {
      $participantAnswer = LibSession::getSessionValue($this->renderUniqueQuestionId($elearningQuestion->getId()));

      return($participantAnswer);
    }
  }

  // Render the download link
  function renderDownload($audio) {
    global $gDataUrl;
    global $gDataPath;

    $str = '';

    if ($audio) {
      if (is_file($gDataPath . "elearning/question/audio/$audio")) {
        $str = $this->playerUtils->renderDownload($gDataPath . "elearning/question/audio/$audio");
      }
    }

    return($str);
  }

  // Render the player
  function renderPlayer($audio) {
    global $gDataPath;
    global $gDataUrl;

    $str = '';

    if ($audio) {
      $this->playerUtils->setPlayer(PLAYER_FLASH_AUDIO_MP3_SPEAKER);

      $this->playerUtils->setAutostart(false);

      if (is_file($gDataPath . "elearning/question/audio/$audio")) {
        $audioDownload = $this->preferenceUtils->getValue("ELEARNING_DISPLAY_AUDIO_DOWNLOAD");
        if ($audioDownload) {
          $str .= $this->playerUtils->renderDownload($gDataPath . "elearning/question/audio/$audio") . ' ';
        }
        $str .= $this->playerUtils->renderPlayer("$gDataUrl/elearning/question/audio/$audio");
      }
    }

    return($str);
  }

  // Render the question sentence
  function renderQuestionForPrint($elearningQuestion, $showSolutions) {
    global $gImagesUserUrl;

    $question = $elearningQuestion->getQuestion();

    if (!$question) {
      $question = '-';
    }

    if (!$showSolutions) {
      $hint = $elearningQuestion->getHint();
      if ($hint) {
        $strHint = "(<span class='elearning_question_hint'><img src='$gImagesUserUrl/" . IMAGE_COMMON_HINT . "' class='no_style_image_icon' title='' alt='' /> " . $hint . "</span>)";
        if ($this->hintBeforeAnswer($elearningQuestion)) {
          $question = str_replace(ELEARNING_ANSWER_MCQ_MARKER, $strHint . ' ' . ELEARNING_ANSWER_MCQ_MARKER, $question);
        } else if ($this->hintAfterAnswer($elearningQuestion) || $this->hintInPopup($elearningQuestion) || $this->hintInsideAnswer($elearningQuestion)) {
          $questionBits = $this->elearningExercisePageUtils->getQuestionBits($question);
          $inputSize = $this->elearningExercisePageUtils->getQuestionInputFieldSize($question);
          $question = $questionBits[0] . ' ' . ELEARNING_ANSWER_MCQ_MARKER . $inputSize . ' ' . $strHint;
          if (count($questionBits) > 1) {
            $question .= ' ' . $questionBits[1];
          }
        } else {
          $question .= ' ' . $strHint;
        }
      }
    }

    return($question);
  }

  // Render the question
  function renderForPrint($elearningQuestion, $showSolutions) {
    global $gCommonImagesUrl;

    $elearningQuestionId = $elearningQuestion->getId();

    $str = '';

    $str .= $this->renderImage($elearningQuestion);

    $question = $this->renderQuestionForPrint($elearningQuestion, $showSolutions);

    $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId);

    $str .= "\n<div class='elearning_exercise_page_question'>";

    // Underscores are displayed for the questions of type written answer when displaying the questions without solutions, all answers are displayed for the questions of type other than written answer when displaying the questions without solutions, and only the solutions are displayed when displaying the questions with their solutions
    if (!$showSolutions) {
      if ($this->isWrittenAnswer($elearningQuestion)) {
        $questionBits = $this->elearningExercisePageUtils->getQuestionBits($question);
        if (count($questionBits) > 1) {
          $question = $questionBits[0] . ' ';
          $question .= ELEARNING_ANSWER_UNDERSCORE;
          $question .= ' ' . $questionBits[1];
        } else {
          $question .= ' ' . ELEARNING_ANSWER_UNDERSCORE;
        }
      } else {
        $answers = '';
        foreach ($elearningAnswers as $elearningAnswer) {
          $elearningAnswerId = $elearningAnswer->getId();
          $answer = $elearningAnswer->getAnswer();
          $answers .= " <span class='elearning_question_answer'>[$answer]</span>";
        }
        if (strstr($question, ELEARNING_ANSWER_MCQ_MARKER)) {
          $question = str_replace(ELEARNING_ANSWER_MCQ_MARKER, $answers, $question);
        } else {
          $question .= '<br />' . $answers;
        }
      }
    } else {
      $answers = '';
      foreach ($elearningAnswers as $elearningAnswer) {
        $elearningAnswerId = $elearningAnswer->getId();
        $answer = $elearningAnswer->getAnswer();
        if ($this->elearningAnswerUtils->isASolution($elearningQuestion, $elearningAnswerId)) {
          $answers .= " <span class='elearning_question_answer'>[$answer]</span>";
        }
      }
      $questionBits = $this->elearningExercisePageUtils->getQuestionBits($question);
      $inputSize = $this->elearningExercisePageUtils->getQuestionInputFieldSize($question);
      if (count($questionBits) > 1) {
        $question = $questionBits[0] . ' ' . $answers . ' ' . $questionBits[1];
      } else {
        $question .= ' ' . $answers;
      }
    }

    $str .= $question;

    foreach ($elearningAnswers as $elearningAnswer) {
      $elearningAnswerId = $elearningAnswer->getId();
      if (!$showSolutions || $this->elearningAnswerUtils->isASolution($elearningQuestion, $elearningAnswerId)) {
        $image = $elearningAnswer->getImage();
        if ($image) {
          $answer = $this->elearningAnswerUtils->renderImage($elearningAnswerId);
          $str .= " <span class='elearning_question_answer'>$answer</span>";
        }
      }
    }

    $str .= "\n</div>";

    return($str);
  }

  // Get the width of an image
  function getImageWidth() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("ELEARNING_PHONE_QUESTION_IMAGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("ELEARNING_QUESTION_IMAGE_WIDTH");
    }

    return($width);
  }

  // Render the image of a question
  function renderImage($elearningQuestion, $emailFormat = false) {
    global $gDataPath;
    global $gDataUrl;
    global $gUtilsUrl;

    $image = $elearningQuestion->getImage();

    $imagePath = $this->imageFilePath;
    $imageUrl = $this->imageFileUrl;

    // Resize the image to the following width
    $width = $this->getImageWidth();

    $str = '';

    if ($image && file_exists($imagePath . $image)) {
      $str .= "\n<div class='elearning_question_image'>";

      if (LibImage::isImage($imagePath . $image)) {

        // Check if the images are to be rendered in an email format
        // If so the image file path will be replaced bi 'cid' sequences
        // and no on-the-fly image resizing should take place
        if ($emailFormat) {
          $url = $imageUrl . '/' . $image;
        } else {
          if ($width && !$this->fileUploadUtils->isGifImage($imagePath . $image)) {
            // The image is created on the fly
            $filename = urlencode($imagePath . $image);
            $url = $gUtilsUrl .  "/printImage.php?filename=" . $filename
              . "&amp;width=" . $width .  "&amp;height=";
          } else {
            $url = $imageUrl . '/' . $image;
          }
        }

        if ($width) {
          $strWidth = "width = '$width'";
        } else {
          $strWidth = '';
        }

        $str .= "<img class='elearning_question_image_file' src='$url' title='' alt='' $strWidth />";
      } else {
        $libFlash = new LibFlash();
        if ($libFlash->isFlashFile($image)) {
          $str .= $libFlash->renderObject("$imageUrl/$image");
        }
      }
      $str .= "</div>";
    }

    return($str);
  }

// TODO
  // Render any question from an exercise or a course
  function renderAnyQuestion($elearningExerciseId, $elearningSubscriptionId) {
    // Select a question
    // Check if the question has not been selected before
    // Check if the question should be selected again
    $elearningQuestionUtils->resetAnswers($elearningQuestionId);
    // The question can only be a multiple choices question
  }

}

?>
