<?

class ElearningExercisePageUtils extends ElearningExercisePageDB {

  var $websiteText;

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $audioFileSize;
  var $audioFilePath;
  var $audioFileUrl;

  var $elearningExercisePageId;

  var $languageUtils;
  var $preferenceUtils;
  var $commonUtils;
  var $playerUtils;
  var $lexiconEntryUtils;
  var $elearningExerciseUtils;
  var $elearningQuestionUtils;
  var $elearningResultUtils;
  var $elearningQuestionResultUtils;
  var $elearningAnswerUtils;
  var $elearningSolutionUtils;
  var $elearningSubscriptionUtils;
  var $elearningCourseUtils;
  var $elearningAssignmentUtils;
  var $fileUploadUtils;

  function ElearningExercisePageUtils() {
    $this->ElearningExercisePageDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'elearning/exercise_page/image/';
    $this->imageFileUrl = $gDataUrl . '/elearning/exercise_page/image';

    $this->audioFileSize = 4096000;
    $this->audioFilePath = $gDataPath . 'elearning/exercise_page/audio/';
    $this->audioFileUrl = $gDataUrl . '/elearning/exercise_page/audio';

    $this->elearningExercisePageId = "elearningExercisePageId";
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'elearning')) {
        mkdir($gDataPath . 'elearning');
      }
      if (!is_dir($gDataPath . 'elearning/exercise_page')) {
        mkdir($gDataPath . 'elearning/exercise_page');
      }
      mkdir($this->imageFilePath);
      chmod($this->imageFilePath, 0755);
    }

    if (!is_dir($this->audioFilePath)) {
      mkdir($this->audioFilePath);
      chmod($this->audioFilePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
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

  // Get the width of the image
  function getImageWidth() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("ELEARNING_PHONE_EXERCISE_PAGE_IMAGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_PAGE_IMAGE_WIDTH");
    }

    return $width;
  }


  // Check if an image is being used
  function imageIsUsed($image) {
    $isUsed = true;

    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByImage($image)) {
      if ($result->getRowCount() < 1) {
        if ($result = $this->dao->selectTextLikeImage($image)) {
          if ($result->getRowCount() < 1) {
            $isUsed = false;
          }
        }
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
  function getNextListOrder($elearningExerciseId) {
    $listOrder = 1;
    if ($elearningExercisePages = $this->selectByExerciseId($elearningExerciseId)) {
      $total = count($elearningExercisePages);
      if ($total > 0) {
        $elearningExercisePage = $elearningExercisePages[$total - 1];
        $listOrder = $elearningExercisePage->getListOrder() + 1;
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
    if ($elearningExercisePage = $this->selectById($id)) {
      $listOrder = $elearningExercisePage->getListOrder();
      $elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
      if ($elearningExercisePages = $this->selectByListOrder($elearningExerciseId, $listOrder)) {
        if (($listOrder == 0) || (count($elearningExercisePages)) > 1) {
          $this->resetListOrder($elearningExerciseId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($elearningExercisePage = $this->selectById($id)) {
      $listOrder = $elearningExercisePage->getListOrder();
      $elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
      if ($elearningExercisePage = $this->selectByNextListOrder($elearningExerciseId, $listOrder)) {
        return($elearningExercisePage);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($elearningExercisePage = $this->selectById($id)) {
      $listOrder = $elearningExercisePage->getListOrder();
      $elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
      if ($elearningExercisePage = $this->selectByPreviousListOrder($elearningExerciseId, $listOrder)) {
        return($elearningExercisePage);
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
    $elearningExerciseId = $targetObject->getElearningExerciseId();
    $currentListOrder = $currentObject->getListOrder();

    if ($objects = $this->selectByExerciseId($elearningExerciseId)) {
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
    $currentObject->setElearningExerciseId($targetObject->getElearningExerciseId());
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
    $elearningExerciseId = $targetObject->getElearningExerciseId();
    $currentListOrder = $currentObject->getListOrder();

    if ($objects = $this->selectByExerciseId($elearningExerciseId)) {
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
    $currentObject->setElearningExerciseId($targetObject->getElearningExerciseId());
    $this->update($currentObject);

    return(true);
  }

  // Check if the content was created by the user
  function createdByUser($elearningExercisePageId, $userId) {
    $byUser = false;

    if ($elearningExercisePage = $this->selectById($elearningExercisePageId)) {
      $elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
      $byUser = $this->elearningExerciseUtils->createdByUser($elearningExerciseId, $userId);
    }

    return($byUser);
  }

  // Duplicate an exercise page
  function duplicate($elearningExercisePageId, $lastInsertElearningExerciseId, $name = '') {
    if ($elearningExercisePage = $this->selectById($elearningExercisePageId)) {
      if ($lastInsertElearningExerciseId) {
        $elearningExercisePage->setElearningExerciseId($lastInsertElearningExerciseId);
      }
      if ($name) {
        $elearningExercisePage->setName($name);
      }

      $this->insert($elearningExercisePage);
      $lastInsertElearningExercisePageId = $this->getLastInsertId();

      // Duplicate the questions
      $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePageId);
      foreach ($elearningQuestions as $elearningQuestion) {
        $elearningQuestionId = $elearningQuestion->getId();
        $lastInsertElearningQuestionId = $this->elearningQuestionUtils->duplicate($elearningQuestionId, $lastInsertElearningExercisePageId);
      }
    }
  }

  // Delete an exercise page from an exercise
  function deleteExercisePage($elearningExercisePageId) {
    if ($elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePageId)) {
      foreach ($elearningQuestions as $elearningQuestion) {
        $this->elearningResultUtils->deleteQuestionResults($elearningQuestion->getId());
        $this->elearningQuestionUtils->deleteQuestion($elearningQuestion->getId());
      }
    }

    $this->delete($elearningExercisePageId);
  }

  // Check if an exercise page displays the answer text input fields
  // within the text of the page of questions
  function typeIsWriteInText($elearningExercisePage) {
    $value = false;

    $questionType = $elearningExercisePage->getQuestionType();
    if ($questionType == 'WRITE_IN_TEXT') {
      $value = true;
    }

    return($value);
  }

  // Check if an exercise page displays an input field to type in a full text
  function typeIsWriteText($elearningExercisePage) {
    $value = false;

    $questionType = $elearningExercisePage->getQuestionType();
    if ($questionType == 'WRITE_TEXT') {
      $value = true;
    }

    return($value);
  }

  // Check if an exercise page displays the answers to choose
  // within the text of the page of questions
  function typeIsSelectInText($elearningExercisePage) {
    $selectInText = false;

    $questionType = $elearningExercisePage->getQuestionType();
    if ($questionType == 'SELECT_LIST_IN_TEXT') {
      $selectInText = true;
    }

    return($selectInText);
  }

  // Check if an exercise page requires answers to be typed in
  function isWrittenAnswer($elearningExercisePage) {
    $isWritten = false;

    if ($this->typeIsWriteInQuestion($elearningExercisePage) || $this->typeIsWriteInText($elearningExercisePage) || $this->typeIsWriteText($elearningExercisePage)) {
      $isWritten = true;
    }

    return($isWritten);
  }

  // Check if an exercise page requires answers to be typed in their respective questions
  function typeIsWriteInQuestion($elearningExercisePage) {
    $writeInQuestion = false;

    $questionType = $elearningExercisePage->getQuestionType();
    if ($questionType == 'WRITE_IN_QUESTION') {
      $writeInQuestion = true;
    }

    return($writeInQuestion);
  }

  // Check if an exercise page displays the answers to choose
  // within their respective questions
  function typeIsSelectInQuestion($elearningExercisePage) {
    $selectInQuestion = false;

    $questionType = $elearningExercisePage->getQuestionType();
    if ($questionType == 'SELECT_LIST') {
      $selectInQuestion = true;
    }

    return($selectInQuestion);
  }

  // Check if an exercise page require some but not all correct answers to a question
  // Such an exercise page accepts multiple answers
  // but not all possible correct answers are required
  function typeIsRequireOneOrMoreCorrectAnswers($elearningExercisePage) {
    $checkBoxes = false;

    $questionType = $elearningExercisePage->getQuestionType();
    if ($questionType == 'SOME_CHECKBOXES') {
      $checkBoxes = true;
    }

    return($checkBoxes);
  }

  // For an exercise page that accepts multiple answers for its multiple choice questions
  // check if it requires all its possible correct answers
  function typeIsRequireAllPossibleAnswers($elearningExercisePage) {
    $allCheckboxes = false;

    $questionType = $elearningExercisePage->getQuestionType();
    if ($questionType == 'ALL_CHECKBOXES') {
      $allCheckboxes = true;
    }

    return($allCheckboxes);
  }

  // Check if an exercise page has some results
  function exercisePageHasResults($elearningExercisePageId) {
    $hasResults = false;

    if ($elearningExercisePage = $this->selectById($elearningExercisePageId)) {
      $elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
      $hasResults = $this->elearningResultUtils->exerciseHasResults($elearningExerciseId);
    }

    return($hasResults);
  }

  // Count the number of words of an answer that is a typed in text
  function countAnswerNbWords($participantAnswer) {
    $answerNbWords = LibString::countNbRealWords($participantAnswer);

    return($answerNbWords);
  }

  // Check if a question is correctly answered
  // IMPORTANT! Another isACorrectAnswer function with ALMOST the same business logic
  // is defined in the class ElearningResultUtils but is it based on the persisted data
  // whereas this one is based on the session data of the exercise currently being done
  function isCorrectlyAnswered($elearningQuestionId, $participantAnswer) {
    $correctAnswer = false;

    if ($elearningQuestion = $this->elearningQuestionUtils->selectById($elearningQuestionId)) {
      $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
      $elearningExercisePage = $this->selectById($elearningExercisePageId);
      if ($this->typeIsDragAndDropOrderSentence($elearningExercisePage)) {
        // Check that the submitted text answer containing the sentence assembled
        // from all the possible answers is equal to the question itself
        $assembledQuestion = '';
        foreach ($participantAnswer as $participantAnswerId) {
          if ($elearningAnswer = $this->elearningAnswerUtils->selectById($participantAnswerId)) {
            $assembledQuestion .= ' ' . $elearningAnswer->getAnswer();
          }
        }
        $question = $elearningQuestion->getQuestion();
        if (trim($question) == trim($assembledQuestion)) {
          $correctAnswer = true;
        } else {
          $correctAnswer = false;
        }
      } else if ($this->typeIsRequireAllPossibleAnswers($elearningExercisePage)) {
        $correctAnswer = false;
        // Check that some possible answers have been checked
        foreach ($participantAnswer as $participantAnswerId) {
          if ($this->elearningAnswerUtils->isASolution($elearningQuestion, $participantAnswerId)) {
            $correctAnswer = true;
          }
        }
        // Check that only the possible answers have been checked
        foreach ($participantAnswer as $participantAnswerId) {
          if (!$this->elearningAnswerUtils->isASolution($elearningQuestion, $participantAnswerId)) {
            $correctAnswer = false;
          }
        }
        // Check that no possible answers have not been checked
        if ($elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId)) {
          foreach ($elearningAnswers as $elearningAnswer) {
            $elearningAnswerId = $elearningAnswer->getId();
            if (!in_array($elearningAnswerId, $participantAnswer) && $this->elearningAnswerUtils->isASolution($elearningQuestion, $elearningAnswerId)) {
              $correctAnswer = false;
            }
          }
        }
      } else if ($this->typeIsRequireOneOrMoreCorrectAnswers($elearningExercisePage) || $this->typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningExercisePage)) {
        $correctAnswer = false;
        if ($participantAnswer && $this->answerIsArrayOfAnswers($participantAnswer)) {
          // Check that some possible answers have been checked
          foreach ($participantAnswer as $participantAnswerId) {
            if ($this->elearningAnswerUtils->isASolution($elearningQuestion, $participantAnswerId)) {
              $correctAnswer = true;
            }
          }
          // Check that only the possible answers have been checked
          foreach ($participantAnswer as $participantAnswerId) {
            if (!$this->elearningAnswerUtils->isASolution($elearningQuestion, $participantAnswerId)) {
              $correctAnswer = false;
            }
          }
        }
      } else if ($this->typeIsWriteText($elearningExercisePage)) {
        // If the number of typed in words is roughly the one of required words
        // then the answer is considered correct
        // A teacher can review the results and correct that default assumption
        $correctAnswer = true;
        $nbAnswerWords = $elearningQuestion->getAnswerNbWords();
        if ($nbAnswerWords) {
          $nbParticipantAnswerWords = $this->countAnswerNbWords($participantAnswer);
          if ($nbParticipantAnswerWords < $nbAnswerWords / 2) {
            $correctAnswer = false;
          } else if ($nbParticipantAnswerWords > $nbAnswerWords * 1.5) {
            $correctAnswer = false;
          }
        }
      } else {
        if ($this->elearningAnswerUtils->isASolution($elearningQuestion, $participantAnswer)) {
          $correctAnswer = true;
        } else {
          $correctAnswer = false;
        }
      }
    }

    return($correctAnswer);
  }

  // Check if the exercise page is the first one for an exercise
  function isFirstExercisePage($elearningExercisePages, $elearningExercisePageId) {
    if (count($elearningExercisePages) > 0) {
      $elearningExercisePage = $elearningExercisePages[0];
      if ($elearningExercisePageId == $elearningExercisePage->getId()) {
        return(true);
      }
    }

    return(false);
  }

  // Check if the exercise page is the last one for an exercise
  function isLastExercisePage($elearningExercisePages, $elearningExercisePageId) {
    if ($elearningExercisePageId && count($elearningExercisePages) > 0) {
      $elearningExercisePage = $elearningExercisePages[count($elearningExercisePages) - 1];
      if ($elearningExercisePageId == $elearningExercisePage->getId()) {
        return(true);
      }
    }

    return(false);
  }

  // Get the first exercise page
  function getFirstExercisePage($elearningExercise) {
    $elearningExercisePageId = '';

    $elearningExercisePages = $this->selectByExerciseId($elearningExercise->getId());
    if (count($elearningExercisePages) > 0) {
      $elearningExercisePage = $elearningExercisePages[0];
      $elearningExercisePageId = $elearningExercisePage->getId();
    }

    return($elearningExercisePageId);
  }

  // Get the next exercise page
  function getNextPageOfQuestion($elearningExercisePageId) {
    if ($elearningExercisePage = $this->selectNext($elearningExercisePageId)) {
      $elearningExercisePageId = $elearningExercisePage->getId();
    }

    return($elearningExercisePageId);
  }

  // Check if a the questions of an exercise page have their hint displayed after the answer field
  function hintAfterAnswer($elearningExercisePage) {
    $after = false;

    $hintPlacement = $elearningExercisePage->getHintPlacement();
    if ($hintPlacement == 'ELEARNING_HINT_AFTER' || !$hintPlacement) {
      $after = true;
    } else if (!$this->typeIsWriteInQuestion($elearningExercisePage) && !$this->typeIsWriteInText($elearningExercisePage) && $hintPlacement == 'ELEARNING_HINT_INSIDE') {
      $after = true;
    }

    return($after);
  }

  // Check if a the questions of an exercise page have their hint displayed before the answer field
  function hintBeforeAnswer($elearningExercisePage) {
    $before = false;

    $hintPlacement = $elearningExercisePage->getHintPlacement();
    if ($hintPlacement == 'ELEARNING_HINT_BEFORE') {
      $before = true;
    }

    return($before);
  }

  // Check if a the questions of an exercise page have their hint displayed inside the answer field
  function hintInsideAnswer($elearningExercisePage) {
    $inside = false;

    $hintPlacement = $elearningExercisePage->getHintPlacement();
    if (($this->typeIsWriteInQuestion($elearningExercisePage) || $this->typeIsWriteInText($elearningExercisePage)) && $hintPlacement == 'ELEARNING_HINT_INSIDE') {
      $inside = true;
    }

    return($inside);
  }

  // Check if a the questions of an exercise page have their hint displayed at the end of the question
  function hintEndOfQuestion($elearningExercisePage) {
    $end = false;

    $hintPlacement = $elearningExercisePage->getHintPlacement();
    if ($hintPlacement == 'ELEARNING_HINT_END_OF_QUESTION') {
      $end = true;
    }

    return($end);
  }

  // Check if a the questions of an exercise page have their hint displayed in a popup window
  function hintInPopup($elearningExercisePage) {
    $inPopup = false;

    $hintPlacement = $elearningExercisePage->getHintPlacement();
    if ($hintPlacement == 'ELEARNING_HINT_IN_POPUP') {
      $inPopup = true;
    }

    return($inPopup);
  }

  // Check if an exercise page has draggable answers droppable in their question only
  function typeIsDragAndDropInQuestion($elearningExercisePage) {
    $dragAndDrop = false;

    $questionType = $elearningExercisePage->getQuestionType();
    if ($questionType == 'DRAG_ANSWER_IN_QUESTION') {
      $dragAndDrop = true;
    }

    return($dragAndDrop);
  }

  // Check if an exercise page has draggable answers droppable in any questions of the page
  // with only one answer being droppable in the question
  function typeIsDragAndDropOneAnswerInAnyQuestion($elearningExercisePage) {
    $dragAndDrop = false;

    $questionType = $elearningExercisePage->getQuestionType();
    if ($questionType == 'DRAG_ANSWER_IN_ANY_QUESTION') {
      $dragAndDrop = true;
    }

    return($dragAndDrop);
  }

  // Check if an exercise page has draggable answers droppable under any questions of the page
  // with several answers or images being droppable under the question
  function typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningExercisePage) {
    $dragAndDrop = false;

    $questionType = $elearningExercisePage->getQuestionType();
    if ($questionType == 'DRAG_ANSWERS_UNDER_ANY_QUESTION') {
      $dragAndDrop = true;
    }

    return($dragAndDrop);
  }

  // Check if an exercise page has draggable answers droppable in their question
  // so as to compose or complete a sentence
  function typeIsDragAndDropOrderSentence($elearningExercisePage) {
    $dragAndDrop = false;

    $questionType = $elearningExercisePage->getQuestionType();
    if ($questionType == 'DRAG_ORDER_SENTENCE') {
      $dragAndDrop = true;
    }

    return($dragAndDrop);
  }

  // Check if an exercise page has draggable answers
  // so as to fill in the blank spaces of the text of the page
  function typeIsDragAndDropInText($elearningExercisePage) {
    $dragAndDrop = false;

    $questionType = $elearningExercisePage->getQuestionType();
    if ($questionType == 'DRAG_ANSWER_IN_TEXT_HOLE') {
      $dragAndDrop = true;
    }

    return($dragAndDrop);
  }

  // Check if an exercise page should offer radio buttons
  function typeIsRadioButtonVertical($elearningExercisePage) {
    $radioButtonVertical = false;

    $questionType = $elearningExercisePage->getQuestionType();
    if ($questionType == 'RADIO_BUTTON_LIST_V') {
      $radioButtonVertical = true;
    }

    return($radioButtonVertical);
  }

  // Check if an exercise page should offer radio buttons
  function typeIsRadioButtonHorizontal($elearningExercisePage) {
    $radioButtonHorizontal = false;

    $questionType = $elearningExercisePage->getQuestionType();
    if ($questionType == 'RADIO_BUTTON_LIST_H') {
      $radioButtonHorizontal = true;
    }

    return($radioButtonHorizontal);
  }

  // Store the participant answers of the exercise page's questions in the session
  function sessionStoreParticipantAnswers($elearningExercisePage) {
    $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePage->getId());
    foreach ($elearningQuestions as $elearningQuestion) {
      $participantAnswers = $this->getPostedParticipantAnswers($elearningExercisePage, $elearningQuestion);
      $this->sessionStoreParticipantQuestionAnswers($participantAnswers);
    }
  }

  // Store the participant answers of a question in the session
  function sessionStoreParticipantQuestionAnswers($participantAnswers) {
    foreach ($participantAnswers as $uniqueQuestionId => $participantAnswer) {
      $this->sessionStoreParticipantQuestionAnswer($uniqueQuestionId, $participantAnswer) ;
    }
  }

  // Store a participant answer of a question in the session
  function sessionStoreParticipantQuestionAnswer($uniqueQuestionId, $participantAnswer) {
    LibSession::putSessionValue($uniqueQuestionId, $participantAnswer);
  }

  // Check if an answer is an array of participant answers
  // This is the case for the checkbox questions types
  function answerIsArrayOfAnswers($participantAnswer) {
    if (is_array($participantAnswer)) {
      return(true);
    } else {
      return(false);
    }
  }

  // Retrieve the participant answers to the question
  function getPostedParticipantAnswers($elearningExercisePage, $elearningQuestion) {
    $participantAnswers = array();

    // Retrieve all the answers of the question
    $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestion->getId());
    // Most of the questions have been given one answer but some, like the types of questions for 
    // several possible or required answers may have been given several ones
    if ($this->typeIsRequireOneOrMoreCorrectAnswers($elearningExercisePage) || $this->typeIsRequireAllPossibleAnswers($elearningExercisePage)) {
      foreach ($elearningAnswers as $elearningAnswer) {
        $elearningAnswerId = $elearningAnswer->getId();
        $participantAnswer = LibEnv::getEnvHttpPOST($this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId(), $elearningAnswerId));
        // Store only the chosen answers
        if ($participantAnswer) {
          $participantAnswers[$this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId(), $elearningAnswerId)] = $participantAnswer;
        } else {
          // Make sure to reset the possibly previously chosen answers
          LibSession::delSessionValue($this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId(), $elearningAnswerId));
        }
      }
    } else {
      $participantAnswer = LibEnv::getEnvHttpPOST($this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId()));
      $participantAnswers[$this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId())] = $participantAnswer;
    }

    return($participantAnswers);
  }

  // Retrieve the answers of the participants for the exercise page
  function sessionRetrieveParticipantQuestionsAnswers($elearningExercisePage) {
    $participantQuestionsAnswers = array();

    $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePage->getId());
    foreach ($elearningQuestions as $elearningQuestion) {
      $participantAnswers = $this->elearningQuestionUtils->sessionRetrieveParticipantAnswers($elearningExercisePage, $elearningQuestion);
      $participantQuestionsAnswers[$elearningQuestion->getId()] = $participantAnswers;
    }

    return($participantQuestionsAnswers);
  }

  // Render the hint in a hidden clickable element
  function renderHintHidden($hint) {
    global $gImagesUserUrl;

    $str = "<a href='javascript:void(0);' class='no_style_image_icon'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_COMMON_HINT . "' class='no_style_image_icon' title='" . $this->websiteText[5] . "' style='border-width:0px; vertical-align:middle; margin-right:4px;' onclick=\"var contentElement = this.parentNode.getElementsByTagName('span')[0]; toggleElementInline(contentElement); return false;\" /> "
      . "<span class='elearning_question_hint_tooltip' style='display:none;' class='no_style_image_icon'>$hint</span>"
      . "</a>";

    return($str);
  }

  // Render the hint in a popup window
  function renderHintInPopup($hint) {
    global $gImagesUserUrl;

    $hint = "<span class='elearning_question_hint_tooltip'>$hint</span>";

    $str = "<span class='elearning_tooltip' title=\"$hint\"><img src='$gImagesUserUrl/" . IMAGE_COMMON_HINT . "' class='no_style_image_icon' title='' alt='" . $this->websiteText[5] . "' /></span>";

    return($str);
  }

  // Check if the text of the page is to be hidden and replaced by a button to display it
  function hideText($elearningExercisePageId) {
    $hide = false;

    if ($elearningExercisePage = $this->selectById($elearningExercisePageId)) {
      $hide = $elearningExercisePage->getHideText();
    }

    return($hide);
  }

  // Render the congratulation for the correction
  function renderInstantCorrectionCongratulation() {
    global $gImagesUserUrl;

    $message = $this->preferenceUtils->getValue("ELEARNING_INSTANT_CONGRATULATION");

    $str = "<span class='elearning_question_congratulation'><img border='0' src='$gImagesUserUrl/" . IMAGE_ELEARNING_ANSWER_TRUE . "' title='' style='vertical-align:middle;' /> " . $message . "</span>";

    return($str);
  }

  // Render the explanation of a question instantaneously when answering the question
  function renderInstantCorrectionExplanation($elearningQuestionId, $participantAnswer, $instantSolution) {
    global $gImagesUserUrl;

    $this->loadLanguageTexts();

    $message = '';

    if (($this->answerIsArrayOfAnswers($participantAnswer) && count($participantAnswer) == 0) || (!$this->answerIsArrayOfAnswers($participantAnswer) && !$participantAnswer)) {
      if ($this->preferenceUtils->getValue("ELEARNING_INSTANT_ON_NO_ANSWER")) {
        $message = $this->websiteText[23];
        $message = "<img border='0' src='$gImagesUserUrl/" . IMAGE_ELEARNING_ANSWER_FALSE . "' title='' style='vertical-align:middle;' /> $message";
      }
    } else {
      if ($this->preferenceUtils->getValue("ELEARNING_INSTANT_EXPLANATION_ON")) {
        $message = $this->getQuestionExplanation($elearningQuestionId, $participantAnswer);
      }

      if (!$message) {
        $message = $this->preferenceUtils->getValue("ELEARNING_INSTANT_EXPLANATION");
      }

      $message = "<img border='0' src='$gImagesUserUrl/" . IMAGE_ELEARNING_ANSWER_FALSE . "' title='' style='vertical-align:middle;' /> $message";
    }

    $message = "<span class='elearning_question_instant_explanation'>$message</span>";

    $instantSolution = $this->preferenceUtils->getValue("ELEARNING_INSTANT_SOLUTION");

    // Do not display the instant solution for the sentence to order as it would give away
    // the solution right after the first drag and drop
    if ($instantSolution) {
      if ($elearningQuestion = $this->elearningQuestionUtils->selectById($elearningQuestionId)) {
        $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
        $elearningExercisePage = $this->selectById($elearningExercisePageId);
        if ($this->typeIsDragAndDropOrderSentence($elearningExercisePage)) {
          $instantSolution = false;
        }
      }
    }

    if ($instantSolution) {
      // Get the answers of the question
      $answerList = array();
      if ($elearningSolutions = $this->elearningSolutionUtils->selectByQuestion($elearningQuestionId)) {
        if (count($elearningSolutions) > 1) {
          $message .= ' ' . $this->websiteText[13];
        } else {
          $message .= ' ' . $this->websiteText[9];
        }
        foreach ($elearningSolutions as $elearningSolution) {
          $elearningAnswerId = $elearningSolution->getElearningAnswer();
          if ($elearningAnswer = $this->elearningAnswerUtils->selectById($elearningAnswerId)) {
            $answer = $elearningAnswer->getAnswer();
            $message .= ' ';
            if (count($elearningSolutions) > 1) {
              $message .= '(';
            }
            $message .= "<span class='elearning_question_instant_solution'>" . $answer . '</span>';
            if (count($elearningSolutions) > 1) {
              $message .= ')';
            }
          }
        }
      }
    }

    return($message);
  }

  // Render the explanation of a question in the results of the exercise
  function renderResultsExplanation($elearningQuestionId, $participantAnswer) {
    $explanation = $this->getQuestionExplanation($elearningQuestionId, $participantAnswer);

    if ($explanation) {
      $explanation = "<div class='elearning_result_explanation'>" . $explanation . "</div>";
    }

    return($explanation);
  }

  // Get the explanation of a question
  function getQuestionExplanation($elearningQuestionId, $participantAnswer) {
    $explanation = '';

    if ($elearningQuestion = $this->elearningQuestionUtils->selectById($elearningQuestionId)) {
      $currentLanguageCode = $this->languageUtils->getCurrentLanguageCode();
      $explanation = $this->languageUtils->getTextForLanguage($elearningQuestion->getExplanation(), $currentLanguageCode);

      $participantAnswerText = '';

      // Get the answer specific explanation if any
      // Note that an explanation can be given to a question or to each specific answer
      // If both are given then the answer specific explanation is used instead of the question explanation
      // An explanation specific to an answer requires the system to know which answer was chosen by the participant
      // and therefore does not make sense for the written answers
      // If the solution offers only one possible answer then it is useless to display an explanation
      // For a sentence to order, displaying an explanation would give away the solution right after the first drag and drop
      if ($this->elearningQuestionUtils->offersSeveralAnswers($elearningQuestionId) && !$this->elearningQuestionUtils->isWrittenAnswer($elearningQuestion) && !$this->elearningQuestionUtils->typeIsDragAndDropOrderSentence($elearningQuestion)) {
        // Get the answers of the question
        $answerList = array();
        $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId);
        foreach ($elearningAnswers as $elearningAnswer) {
          $wElearningAnswerId = $elearningAnswer->getId();
          $wAnswer = $elearningAnswer->getAnswer();
          $answerList[$wElearningAnswerId] = $wAnswer;
        }

        // If several answers were given then consider only the first one
        // Check if an answer is an array of participant answers
        // This is the case for the questions with several answers
        if ($this->answerIsArrayOfAnswers($participantAnswer)) {
          $participantAnswers = $participantAnswer;
          if (count($participantAnswers) > 0) {
            $participantAnswer = $participantAnswers[0];
          }
        }

        // Get the answer to be displayed in the explanation
        if ($participantAnswer && isset($answerList[$participantAnswer])) {
          $participantAnswerText = $answerList[$participantAnswer];
        }

        if ($participantAnswer) {
          if ($elearningAnswer = $this->elearningAnswerUtils->selectById($participantAnswer)) {
            $answerExplanation = $this->languageUtils->getTextForLanguage($elearningAnswer->getExplanation(), $currentLanguageCode);
            if ($answerExplanation) {
              $explanation = $answerExplanation;
            }
          }
        }
      }
      $explanation = str_replace(ELEARNING_ANSWER_MCQ_MARKER, $participantAnswerText, $explanation);
    }

    $explanation = trim($explanation);

    return($explanation);
  }

  // Render the result of a question
  function renderQuestionResult($elearningExercisePage, $elearningQuestion, $participantAnswer, $isCorrectlyAnswered, $hideSolutions) {
    global $gImagesUserUrl;

    $this->loadLanguageTexts();

    $str = "\n<div class='elearning_exercise_page_question'>";

    // Get the questions of the question exercise page
    $question = $elearningQuestion->getQuestion();
    $audio = $elearningQuestion->getAudio();

    // Get the answers of the question
    $answerList = array();
    $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestion->getId());
    foreach ($elearningAnswers as $elearningAnswer) {
      $wElearningAnswerId = $elearningAnswer->getId();
      $wAnswer = $elearningAnswer->getAnswer();
      $audio = $elearningAnswer->getAudio();
      $answerList[$wElearningAnswerId] = $wAnswer;
      if ($audio) {
        $strAudio = $this->elearningAnswerUtils->renderPlayer($audio);
        $answerList[$wElearningAnswerId] .= ' ' . "<span style='vertical-align:bottom;'>" . $strAudio . "</span>";
      }
    }

    // Get the question bits
    $questionBits = $this->getQuestionBits($question);

    // Render the thumb image
    $strThumb = $this->renderQuestionThumb($isCorrectlyAnswered);

    if ($hideSolutions) {
      $strAnswerClass = '';
      $strQuestionSolutions = '';
    } else {
      if ($isCorrectlyAnswered) {
        $strAnswerClass = 'elearning_question_right_answer';
        $strQuestionSolutions = '';
      } else {
        $strAnswerClass = 'elearning_question_wrong_answer';
        // Get the possible solutions
        $strQuestionSolutions = $this->renderQuestionSolutions($elearningQuestion->getId());
      }
    }

    if ($participantAnswer) {
      if ($this->typeIsWriteInQuestion($elearningExercisePage) || $this->typeIsWriteInText($elearningExercisePage)) {
        $strAnswerBit = '[' . "<span class='$strAnswerClass'>" . $participantAnswer . "</span>" . ']';
      } else if ($this->typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningExercisePage) || $this->typeIsDragAndDropOrderSentence($elearningExercisePage)) {
        $strAnswerBit = '';
        if ($this->answerIsArrayOfAnswers($participantAnswer) && count($participantAnswer) > 0) {
          $strAnswerBit .= ' [';
          foreach ($participantAnswer as $participantAnswerId) {
            if ($elearningAnswer = $this->elearningAnswerUtils->selectById($participantAnswerId)) {
              $answer = $elearningAnswer->getAnswer();
              $image = $elearningAnswer->getImage();
              if ($image) {
                $answer .= ' ' . $this->elearningAnswerUtils->renderImage($participantAnswerId);
              }
              $strAnswerBit .= "(<span class='$strAnswerClass'>$answer</span>) ";
            }
          }
          $strAnswerBit .= ']';
          // Remove the last blank space
          $strAnswerBit = str_replace(') ]', ')]', $strAnswerBit);
          // Empty the string if no answer was correct
          $strAnswerBit = str_replace(' []', '', $strAnswerBit);
        }
        if (!$strAnswerBit) {
          $strAnswerBit = '[' . "<span class='elearning_question_wrong_answer'>" . ELEARNING_ANSWER_MCQ_MARKER . "</span>" . ']';
        }
      } else if ($this->typeIsRequireOneOrMoreCorrectAnswers($elearningExercisePage) || $this->typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningExercisePage)) {
        $strAnswerBit = '';
        if ($this->answerIsArrayOfAnswers($participantAnswer) && count($participantAnswer) > 0) {
          $strAnswerBit .= ' [';
          foreach ($participantAnswer as $participantAnswerId) {
            if (isset($answerList[$participantAnswerId])) {
              $strAnswerBit .= '(' . "<span class='$strAnswerClass'>" . $answerList[$participantAnswerId] . "</span>" . ') ';
            }
          }
          $strAnswerBit .= ']';
          // Remove the last blank space
          $strAnswerBit = str_replace(') ]', ')]', $strAnswerBit);
          // Empty the string if no answer was correct
          $strAnswerBit = str_replace(' []', '', $strAnswerBit);
        }
        if (!$strAnswerBit) {
          $strAnswerBit = '[' . "<span class='elearning_question_wrong_answer'>" . ELEARNING_ANSWER_MCQ_MARKER . "</span>" . ']';
        }
      } else if ($this->elearningQuestionUtils->typeIsWriteText($elearningQuestion)) {
        if ($participantAnswer) {
          $uniqueQuestionId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId());
          $uniqueAnswerTextId = "exercise_question_text_$uniqueQuestionId";
          $uniqueAnswerTextButtonId = "exercise_question_button_$uniqueQuestionId";
          $textButtonShow = $this->websiteText[10];
          $textButtonHide = $this->websiteText[14];
          $strAnswerBit = <<<HEREDOC
<script type="text/javascript">
$(document).ready(function(){
  $('#$uniqueAnswerTextButtonId').click(function(event) {
    toggleElementDisplay('$uniqueAnswerTextButtonId', '$uniqueAnswerTextId', '$textButtonShow', '$textButtonHide');
    return false;
  });
});
</script>
HEREDOC;
          $strAnswerBit .= "<div><img src='$gImagesUserUrl/" . IMAGE_ELEARNING_TEXT . "' class='no_style_image_icon' title='' style='border-width:0px; vertical-align:middle; margin-right:4px;' /> <a href='#' id='$uniqueAnswerTextButtonId' onclick='return false;'>$textButtonShow</a></div>";
          if (!$isCorrectlyAnswered) {
            $strAnswerBit .= "<div class='no_style_image_icon'>" . $this->websiteText[15] . '</div>';
          }
          $strAnswerBit .= "<div id='$uniqueAnswerTextId' style='display:none;' class='elearning_exercise_page_text'>" . $participantAnswer . '</div>';
        } else {
          $strAnswerBit = '';
        }
        // Do not show the thumb
        $hideSolutions = true;
      } else {
        // Avoid a php error if a typed in answer stored in the session
        // is later retrieved in a type of exercise that offers answers to choose from
        // It is unlikely, but it did happen
        if (is_numeric($participantAnswer) && isset($answerList[$participantAnswer])) {
          $strAnswerBit = '[' . "<span class='$strAnswerClass'>" . $answerList[$participantAnswer] . "</span>" . ']';
        } else {
          $strAnswerBit = '';
        }
      }
    } else {
      // No answer was given
      $strAnswerBit = '[' . "<span class='elearning_question_wrong_answer'>" . ELEARNING_ANSWER_MCQ_MARKER . "</span>" . ']';
    }

    if (count($questionBits) > 1) {
      $strQuestion = "<span class='elearning_exercise_page_question_sentence'>"
        . $questionBits[0]
        . ' ' . $strAnswerBit . ' '
        . $questionBits[1]
        . '</span>';
    } else {
      $strQuestion = "<span class='elearning_exercise_page_question_sentence'>"
        . $questionBits[0]
        . ' ' . $strAnswerBit
        . '</span>';
    }

    if (!$hideSolutions) {
      $strQuestion .= ' ' . $strThumb . ' ' . $strQuestionSolutions;
    }

    $str .= $strQuestion;

    $str .= '</div>';

    return($str);
  }

  // Render the download link
  function renderDownload($audio) {
    global $gDataUrl;
    global $gDataPath;

    $str = '';

    if ($audio) {
      if (is_file($gDataPath . "elearning/exercise_page/audio/$audio")) {
        $str = $this->playerUtils->renderDownload($gDataPath . "elearning/exercise_page/audio/$audio");
      }
    }

    return($str);
  }

  // Render the player
  function renderPlayer($audio) {
    global $gDataUrl;
    global $gDataPath;

    $str = '';

    if ($audio) {
      $autoStartAudioPlayer = $this->elearningExerciseUtils->autoStartAudioPlayer();

      $this->playerUtils->setAutostart($autoStartAudioPlayer);

      if (is_file($gDataPath . "elearning/exercise_page/audio/$audio")) {
        $audioDownload = $this->preferenceUtils->getValue("ELEARNING_DISPLAY_AUDIO_DOWNLOAD");
        if ($audioDownload) {
          $str .= $this->playerUtils->renderDownload($gDataPath . "elearning/exercise_page/audio/$audio") . ' ';
        }
        $str .= $this->playerUtils->renderPlayer("$gDataUrl/elearning/exercise_page/audio/$audio");
      }
    }

    return($str);
  }

  // Print the question exercise page
  function printExercisePage($elearningExercise, $elearningExercisePage) {
    $str = LibJavaScript::getJSLib();
    $str .= "\n<script type='text/javascript'>printPage();</script>";

    $str .= $this->renderForPrint($elearningExercise, $elearningExercisePage, false);

    return($str);
  }

  // Render the exercise page for print
  function renderForPrint($elearningExercise, $elearningExercisePage, $showSolutions) {
    $elearningExercisePageId = $elearningExercisePage->getId();
    $name = $elearningExercisePage->getName();
    $description = $elearningExercisePage->getDescription();

    $str = "\n<div class='elearning_exercise_page'>";

    if ($name) {
      $str .= "\n<div class='elearning_exercise_page_name'>$name</div>";
    }

    if ($description) {
      $str .= "\n<div class='elearning_exercise_page_description'>$description</div>";
    }

    $str .= $this->renderImage($elearningExercisePage);

    $str .= $this->renderStartInstructions($elearningExercisePage);

    $text = $this->renderPageText($elearningExercise, $elearningExercisePage, '', true);
    if ($text) {
      $str .= "<div class='elearning_exercise_page_text'>$text</div>";
    }

    if ($this->elearningExerciseUtils->displayLexiconList()) {
      $pageText = $elearningExercisePage->getText();
      $str .= $this->lexiconEntryUtils->renderLexiconTooltipsForPrintFromContent($pageText);
    }

    $str .= $this->renderQuestionsForPrint($elearningExercisePage, $showSolutions);

    $str .= $this->renderEndInstructions();

    $str .= "\n</div>";

    return($str);
  }

  // Print the exercise page
  function printPageOfQuestions($elearningExercisePageId) {
    $str = LibJavaScript::getJSLib();
    $str .= "\n<script type='text/javascript'>printPage();</script>";

    if (!$elearningExercisePage = $this->selectById($elearningExercisePageId)) {
      return;
    }

    $str .= "\n<div class='elearning_exercise'>";

    $name = $elearningExercisePage->getName();
    $description = $elearningExercisePage->getDescription();

    $str .= "\n<div class='elearning_exercise_page'>";

    if ($name) {
      $str .= "\n<div class='elearning_exercise_page_name'>$name</div>";
    }

    if ($description) {
      $str .= "\n<div class='elearning_exercise_page_description'>$description</div>";
    }

    $str .= $this->renderQuestionsForPrint($elearningExercisePage, false);

    $str .= "\n</div>";

    $str .= "\n</div>";

    return($str);
  }

  // Render the solutions for a page of questions
  function renderSolutionsPageForPrint($elearningExercisePage) {
    $name = $elearningExercisePage->getName();
    $description = $elearningExercisePage->getDescription();

    $str = "\n<div class='elearning_exercise_page'>";

    if ($name) {
      $str .= "\n<div class='elearning_exercise_page_name'>$name</div>";
    }

    if ($description) {
      $str .= "\n<div class='elearning_exercise_page_description'>$description</div>";
    }

    $str .= $this->renderQuestionsForPrint($elearningExercisePage, true);

    $str .= "\n</div>";

    return($str);
  }

  // Render the questions of the exercise page, for print
  function renderQuestionsForPrint($elearningExercisePage, $showSolutions) {
    $elearningExercisePageId = $elearningExercisePage->getId();

    $str = '';

    $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePageId);

    if ($showSolutions) {
      foreach ($elearningQuestions as $elearningQuestion) {
        $str .= $this->elearningQuestionUtils->renderForPrint($elearningQuestion, $showSolutions);
      }
    } else if ($this->typeIsDragAndDropOneAnswerInAnyQuestion($elearningExercisePage) || $this->typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningExercisePage) || $this->typeIsDragAndDropInText($elearningExercisePage)) {
      // Shuffle the answers across all questions
      $shuffledAnswers = array();
      $allQuestionsAnswers = array();
      foreach ($elearningQuestions as $elearningQuestion) {
        $elearningQuestionId = $elearningQuestion->getId();
        $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId);
        foreach ($elearningAnswers as $elearningAnswer) {
          array_push($allQuestionsAnswers, $elearningAnswer);
        }
      }
      $allQuestionsAnswers = LibUtils::shuffleArray($allQuestionsAnswers);

      // Display the questions in a random order
      if ($this->shuffleQuestions()) {
        $elearningQuestions = LibUtils::shuffleArray($elearningQuestions);
      }

      $str .= "<table border='0' width='100%' cellspacing='10' cellpadding='2'>"
        . "<tr><td style='vertical-align:top; width:65%;'>";

      foreach ($elearningQuestions as $elearningQuestion) {
        $question = $this->elearningQuestionUtils->renderQuestionForPrint($elearningQuestion, $showSolutions);
        $questionBits = $this->getQuestionBits($question);
        if (count($questionBits) > 1) {
          $question = $questionBits[0] . ' ';
          $question .= ELEARNING_ANSWER_UNDERSCORE;
          $question .= ' ' . $questionBits[1];
        } else {
          $question .= ' ' . ELEARNING_ANSWER_UNDERSCORE;
        }
        $strImage = $this->elearningQuestionUtils->renderImage($elearningQuestion);
        $str .= "\n<div class='elearning_exercise_page_question'>" . $strImage . $question . "</div>";
      }

      $str .= "</td><td style='vertical-align:top; text-align:center; width:35%;'>";

      foreach ($allQuestionsAnswers as $elearningAnswer) {
        $elearningAnswerId = $elearningAnswer->getId();
        $answer = $elearningAnswer->getAnswer();
        $image = $elearningAnswer->getImage();
        if ($answer) {
          $answer = "[$answer]";
        }
        if ($image) {
          $answer .= $this->elearningAnswerUtils->renderImage($elearningAnswerId);
        }
        $str .= " <div class='elearning_question_answer'>$answer</div>";
      }

      $str .= "</td></tr></table>";
    } else if ($this->typeIsDragAndDropOrderSentence($elearningExercisePage)) {
      // Display the questions in a random order
      if ($this->shuffleQuestions()) {
        $elearningQuestions = LibUtils::shuffleArray($elearningQuestions);
      }
      foreach ($elearningQuestions as $elearningQuestion) {
        $str .= "<div>";
        $elearningQuestionId = $elearningQuestion->getId();
        $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId);
        $elearningAnswers = LibUtils::shuffleArray($elearningAnswers);
        foreach ($elearningAnswers as $elearningAnswer) {
          $elearningAnswerId = $elearningAnswer->getId();
          $answer = $elearningAnswer->getAnswer();
          $image = $elearningAnswer->getImage();
          if ($answer) {
            $answer = "[$answer]";
          }
          if ($image) {
            $answer .= $this->elearningAnswerUtils->renderImage($elearningAnswerId);
          }
          $str .= " <span class='elearning_question_answer'>$answer</span>";
        }
        $str .= "</div>";
      }
    } else if (!$this->typeIsWriteInText($elearningExercisePage)) {
      foreach ($elearningQuestions as $elearningQuestion) {
        $str .= $this->elearningQuestionUtils->renderForPrint($elearningQuestion, $showSolutions);
      }
    }

    return($str);
  }

  // Get the number of question markers in the text
  function getNumberOfInTextQuestionMarkers($elearningExercisePageId) {
    $nb = 0;

    if ($elearningExercisePage = $this->selectById($elearningExercisePageId)) {
      $text = $elearningExercisePage->getText();
      $nb = substr_count($text, ELEARNING_ANSWER_MCQ_MARKER);
    }

    return($nb);
  }

  // Replace a question marker from the text
  function replaceOnlyFirstQuestionMarkerInText($strQuestionInput, $pageText) {
    preg_match('/(\?\?\?)([0-9]*)/', $pageText, $matches);
    if (is_array($matches)) {
      if (isset($matches[0])) {
        $foundPattern = $matches[0];
        $inputSize = $matches[2];
        $strQuestionInput = str_replace("size='10'", "size='$inputSize'", $strQuestionInput);
        $pageText = LibString::replaceOnce($foundPattern, $strQuestionInput, $pageText);
      }
    }

    return($pageText);
  }

  // Get the number of questions of a page
  function getNumberOfQuestions($elearningExercisePageId) {
    $nb = count($elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePageId));

    return($nb);
  }

  // By default, the questions of a page of an exercise are always displayed in the same order. But it is possible to shuffle the questions of the page of an exercise. Every time the exercise is done, the questions appear in a random order and every participant gets the questions in a different order. This makes a bit more difficult a potential cheating by participants.
  function shuffleQuestions($elearningSubscription = '') {
    $shuffle = false;

    if ($elearningSubscription) {
      $elearningCourseId = $elearningSubscription->getCourseId();
      if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
        $shuffle = $elearningCourse->getShuffleQuestions();
      }
    }

    if (!$shuffle) {
      $shuffle = $this->preferenceUtils->getValue("ELEARNING_SHUFFLE_QUESTIONS");
    }

    return($shuffle);
  }

  // By default, the answers of a question are always displayed in the same order. But it is possible to shuffle the answers of a question. Every time the exercise is done, the answers appear in a random order and every participant gets the answers in a different order. This makes a bit more difficult a potential cheating by participants.
  function shuffleAnswers($elearningSubscription) {
    $shuffle = false;

    if ($elearningSubscription) {
      $elearningCourseId = $elearningSubscription->getCourseId();
      if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
        $shuffle = $elearningCourse->getShuffleAnswers();
      }
    }

    if (!$shuffle) {
      $shuffle = $this->preferenceUtils->getValue("ELEARNING_SHUFFLE_ANSWERS");
    }

    return($shuffle);
  }

  // Render the text of the exercise page
  function renderPageText($elearningExercise, $elearningExercisePage, $elearningSubscription, $forPrint) {
    global $gImagesUserUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $pageText = $elearningExercisePage->getText();

    $strText = '';

    if ($pageText) {
      if (!$forPrint) {
        // Replace the question markers within the text if any with answer input fields
        // This is to position the answers within the text itself and not below
        if ($this->typeIsWriteInText($elearningExercisePage) || $this->typeIsSelectInText($elearningExercisePage) || $this->typeIsDragAndDropInText($elearningExercisePage)) {
          $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePage->getId());
          foreach ($elearningQuestions as $elearningQuestion) {
            $elearningQuestionId = $elearningQuestion->getId();
            $hint = $elearningQuestion->getHint();

            $strQuestionInput = '';

            if ($hint && $this->hintBeforeAnswer($elearningExercisePage)) {
              $strQuestionInput .= " (<span class='elearning_question_hint'><img src='$gImagesUserUrl/" . IMAGE_COMMON_HINT . "' class='no_style_image_icon' title='' alt='' /> " . $hint . "</span>)";
            }

            $questionInputField = $this->renderQuestionInput($elearningExercise, $elearningExercisePage, $elearningQuestion, $elearningSubscription, true);

            $displayInstantFeedback = $this->displayInstantFeedback($elearningExercise, $elearningSubscription);

            if ($displayInstantFeedback || $watchLive) {
              $strQuestionInput .= "<span id='" . $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestionId) . "'>" . $questionInputField . "</span>";
              $strQuestionInput .= ' ' . "<span id='" . ELEARNING_INSTANT_FEEDBACK_ID . $elearningQuestionId . "'></span>";
            } else {
              $strQuestionInput .= $questionInputField;
            }

            if ($hint && ($this->hintAfterAnswer($elearningExercisePage) || $this->hintEndOfQuestion($elearningExercisePage))) {
              $strQuestionInput .= " (<span class='elearning_question_hint'><img src='$gImagesUserUrl/" . IMAGE_COMMON_HINT . "' class='no_style_image_icon' title='' alt='' /> " . $hint . "</span>)";
            } else if ($hint && $this->hintInPopup($elearningExercisePage)) {
              if ($gIsPhoneClient) {
                $strQuestionInput .= ' ' . $this->renderHintHidden($hint);
              } else {
                $strQuestionInput .= ' ' . $this->renderHintInPopup($hint);
              }
            }

            // Replace the first available occurence of the marker by the question input field
            $pageText = $this->replaceOnlyFirstQuestionMarkerInText($strQuestionInput, $pageText);
          }
        }

        // Display in a scrolling pane if the content is too large
        $textMaxHeight = $elearningExercisePage->getTextMaxHeight();
        if ($pageText && $textMaxHeight > 0) {
          $pageText = "<pre style='overflow:auto; white-space:normal; max-height:" . $textMaxHeight . "px;'>" . $pageText . '</pre>';
        }

        $hideText = $this->hideText($elearningExercisePage->getId());

        if ($hideText) {
          $textButtonShow = $this->websiteText[10];
          $textButtonHide = $this->websiteText[14];
          $strText .= <<<HEREDOC
<script type="text/javascript">
$(document).ready(function(){
  $('#exercise_page_text_button').click(function(event) {
    toggleElementDisplay('exercise_page_text_button', 'exercise_page_text', '$textButtonShow', '$textButtonHide');
    return false;
  });
});
</script>
HEREDOC;

          $strText .= "<div><img src='$gImagesUserUrl/" . IMAGE_ELEARNING_TEXT . "' class='no_style_image_icon' title='' style='border-width:0px; vertical-align:middle; margin-right:4px;' /> <a href='#' id='exercise_page_text_button' onclick='return false;'>$textButtonShow</a></div>"
            . "<div id='exercise_page_text' style='display:none;' class='no_style_image_icon'>"
            . $pageText
            . '</div>';
        } else {
          $strText .= $pageText;
        }
      } else {
        $pageText = preg_replace('/ELEARNING_ANSWER_MCQ_MARKER([0-9]*)/', ELEARNING_ANSWER_UNDERSCORE, $pageText);

        $strText .= $pageText;
      }
    }

    return($strText);
  }

  // Get the list of answers of a question
  function getQuestionAnswersByIds($elearningAnswers, $withMedia = false) {
    $answerList = array();

    // Check that the question has some answers
    if (count($elearningAnswers) > 0) {
      foreach ($elearningAnswers as $elearningAnswer) {
        $elearningAnswerId = $elearningAnswer->getId();
        $answer = $elearningAnswer->getAnswer();
        $answerList[$elearningAnswerId] = $answer;
        $image = $elearningAnswer->getImage();
        $audio = $elearningAnswer->getAudio();
        if ($withMedia && $image) {
          $answer .= $this->elearningAnswerUtils->renderImage($elearningAnswerId);
        }
        if ($withMedia && $audio) {
          $answer .= $this->elearningAnswerUtils->renderPlayer($audio);
        }
        $answerList[$elearningAnswerId] = $answer;
      }
    }

    return($answerList);
  }

  // Check if a feedback in the form of a correction or a congratulation message must
  // be displayed to the participant right after the question has been answered
  // This method is only part of the logic as its purpose is only to trigger or not
  // the ajax request to the server where the completing part of the logic resides
  function displayInstantFeedback($elearningExercise, $elearningSubscription) {
    $result = false;
    $instantCorrection = false;
    $instantCongratulation = false;

    if ($elearningSubscription) {
      // Do not offer the instant feedback when the exercise is an assignment
      if (!$elearningAssignment = $this->elearningAssignmentUtils->selectBySubscriptionIdAndExerciseId($elearningSubscription->getId(), $elearningExercise->getId())) {
        $instantCorrection = $this->preferenceUtils->getValue("ELEARNING_INSTANT_CORRECTION");
        $instantCongratulation = $this->preferenceUtils->getValue("ELEARNING_INSTANT_CONGRATULATION_ON");
        if (!$instantCorrection && !$instantCongratulation) {
          $elearningCourseId = $elearningSubscription->getCourseId();
          if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
            $instantCorrection = $elearningCourse->getInstantCorrection();
            $instantCongratulation = $elearningCourse->getInstantCongratulation();
          }
        }
      }
    } else {
      $instantCorrection = $this->preferenceUtils->getValue("ELEARNING_INSTANT_CORRECTION");
      $instantCongratulation = $this->preferenceUtils->getValue("ELEARNING_INSTANT_CONGRATULATION_ON");
    }

    if (($instantCorrection || $instantCongratulation)) {
      $result = true;
    }

    return($result);
  }

  // Render the input field of a question
  function renderQuestionInput($elearningExercise, $elearningExercisePage, $elearningQuestion, $elearningSubscription, $withAudio) {
    global $gJsUrl;
    global $gElearningUrl;
    global $gIsPhoneClient;
    global $gImagesUserUrl;

    $str = '';

    if ($withAudio) {
      $audio = $elearningQuestion->getAudio();
      if ($audio) {
        $str = $this->elearningQuestionUtils->renderPlayer($audio);
      }
    }

    if ($elearningSubscription) {
      $watchLive = $elearningSubscription->getWatchLive();
    } else {
      $watchLive = false;
    }

    // Get the answer chosen by the user
    $participantAnswer = '';
    $participantAnswers = $this->sessionRetrieveParticipantQuestionsAnswers($elearningExercisePage);

    if (isset($participantAnswers[$elearningQuestion->getId()])) {
      $participantAnswer = $participantAnswers[$elearningQuestion->getId()];
    }

    // Get the possible answers of the question
    $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestion->getId());

    $displayInstantFeedback = $this->displayInstantFeedback($elearningExercise, $elearningSubscription);

    // Display the answers in a random order
    if ($this->shuffleAnswers($elearningSubscription)) {
      shuffle($elearningAnswers);
      $elearningAnswers = LibUtils::shuffleArray($elearningAnswers);
    }

    $answerList = $this->getQuestionAnswersByIds($elearningAnswers);
    $answerWithMediaList = $this->getQuestionAnswersByIds($elearningAnswers, true);

    $handler = '';

    // Check the type of question
    if ($this->typeIsDragAndDropInQuestion($elearningExercisePage) || $this->typeIsDragAndDropInText($elearningExercisePage) || $this->typeIsDragAndDropOneAnswerInAnyQuestion($elearningExercisePage)) {
      // The answers of each question can be dragged

      $answer = '';
      if ($participantAnswer) {
        if ($elearningAnswer = $this->elearningAnswerUtils->selectById($participantAnswer)) {
          $elearningAnswerId = $elearningAnswer->getId();
          $answer = $elearningAnswer->getAnswer();
          $image = $elearningAnswer->getImage();
          if ($image) {
            $answer .= $this->elearningAnswerUtils->renderImage($elearningAnswerId);
          }
        }
      }

      if (!$answer) {
        $question = $elearningQuestion->getQuestion();
        $inputSize = $this->getQuestionInputFieldSize($question);
        for ($i = 0; $i < $inputSize; $i++) {
          $answer .= '&nbsp;';
        }
      }

      $elearningQuestionId = $elearningQuestion->getId();
      $uniqueQuestionId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestionId);

      $str .= "<span class='elearning_question_droppable' elearningQuestionId='$elearningQuestionId' uniqueQuestionId='$uniqueQuestionId'>"
        . "<span class='elearning_question_answer'><span class='no_style_elearning_dropped_single_answer' style='vertical-align:middle;' title='" . $this->websiteText[3] . "'>$answer</span></span>"
        . "<input type='hidden' name='$uniqueQuestionId' value='$participantAnswer' />"
        . "</span>";

    } else if ($this->typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningExercisePage)) {
      // The answers of the questions are dropped under the question

      $uniqueQuestionId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId());

      // Display the answers retrieved from the session
      $strDroppedAnswerIds = '';
      $str .= "<div class='no_style_elearning_dropped_multiple_answers' title='" . $this->websiteText[3] . "'>";
      // It may happen that the participant answer is not an array
      // if the type of exercise was changed while a participant was
      // already doing the exercise, an unlikely case that did happen
      if ($this->answerIsArrayOfAnswers($participantAnswer)) {
        foreach ($participantAnswer as $elearningAnswerId) {
          if ($elearningAnswer = $this->elearningAnswerUtils->selectById($elearningAnswerId)) {
            $answer = $elearningAnswer->getAnswer();
            $image = $elearningAnswer->getImage();
            if ($image) {
              $answer .= $this->elearningAnswerUtils->renderImage($elearningAnswerId);
            }
            $str .= "<div class='elearning_question_answer no_style_elearning_dropped_multiple_answer' elearningAnswerId='$elearningAnswerId'>$answer</div>";
            if ($strDroppedAnswerIds) {
              $strDroppedAnswerIds .= ELEARNING_ANSWERS_SEPARATOR;
            }
            $strDroppedAnswerIds .= $elearningAnswerId;
          }
        }
      }
      $str .= "</div>";

      $str .= "<input class='no_style_elearning_dropped_ids' type='hidden' name='$uniqueQuestionId' value='$strDroppedAnswerIds' />";

    } else if ($this->typeIsDragAndDropOrderSentence($elearningExercisePage)) {
      // The answers of the questions can be dragged so as to be reordered

      $str .= "<div class='no_style_elearning_question_sortable' elearningQuestionId='" . $elearningQuestion->getId() . "'>";

      // Get the order of the answers
      $pageParticipantAnswers = $this->sessionRetrieveParticipantQuestionsAnswers($elearningExercisePage);
      if (isset($pageParticipantAnswers[$elearningQuestion->getId()])) {
        $participantAnswers = $pageParticipantAnswers[$elearningQuestion->getId()];
        // Be defensive and avoid displaying less answers than there is offered
        if ($offeredAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestion->getId())) {
          if (count($participantAnswers) < count($offeredAnswers)) {
            $participantAnswers = array();
          }
        }
      } else {
        $participantAnswers = array();
      }

      // If no ordering yet happened then shuffle the answers and remember their order
      if (count($participantAnswers) == 0) {
        $answerList = LibUtils::shuffleArray($answerList);
        foreach ($answerList as $elearningAnswerId => $answer) {
          array_push($participantAnswers, $elearningAnswerId);
        }
      }

      // Display the answers according to their order
      foreach ($participantAnswers as $elearningAnswerId) {
        if (isset($answerList[$elearningAnswerId])) {
          $answer = $answerList[$elearningAnswerId];
        } else {
          $answer = '';
        }
        $uniqueAnswerId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId(), $elearningAnswerId);
        $str .= "<span id='$uniqueAnswerId' elearningAnswerId='$elearningAnswerId' class='elearning_question_answer_draggable elearning_question_answer' style='vertical-align: middle;'>$answer</span>";
      }
      $str .= "\n</div>" . "<div class='no_style_sortable_stop_float'></div>";

    } else if ($this->typeIsWriteText($elearningExercisePage)) {
      // The question displays an input field to type in a full text

      if (!$participantAnswer) {
        // Display the typed in text from an existing result if any
        if ($elearningResult = $this->elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscription->getId(), $elearningExercise->getId())) {
          $elearningResultId = $elearningResult->getId();
          $participantAnswer = $this->elearningQuestionResultUtils->getParticipantAnswers($elearningResultId, $elearningQuestion->getId());
        }
      }

      $answerNbWords = $elearningQuestion->getAnswerNbWords();
      $uniqueQuestionId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId());
      $elearningQuestionId = $elearningQuestion->getId();
      $uniqueTextareaId = $this->elearningQuestionUtils->renderUniqueQuestionTextareaId($elearningQuestionId);
      $uniqueTextareaNbWordsId = ELEARNING_WRITE_TEXT_NB_WORDS . $uniqueQuestionId;
      $uniqueTextareaProgressBarId = ELEARNING_WRITE_TEXT_PROGRESS . $uniqueQuestionId;
      if ($gIsPhoneClient) {
        $cols = '40';
      } else {
        $cols = '60';
      }
      $str .= "<div class='elearning_question_text_answer_field'><textarea class='elearning_question_answer_input' name='$uniqueQuestionId' id='$uniqueTextareaId' cols='$cols' rows='15' onfocus='focusElement(this);' />$participantAnswer</textarea></div>";

      if ($answerNbWords > 0) {
        $str .= "<div class='elearning_question_text_words_progress'>"
          . $this->websiteText[16]
          . " <span class='elearning_question_text_nb_words' id='$uniqueTextareaNbWordsId'>0</span> "
          . $this->websiteText[17] 
          . " <span class='elearning_question_text_max_nb_words'>" . $answerNbWords . '</span> ' . $this->websiteText[18]
          . "</div>"
          . "<div class='elearning_question_text_progressbar' id='$uniqueTextareaProgressBarId'></div>";

        $str .= <<<HEREDOC
<script type="text/javascript">
function checkTextareaNbWords(textareaId, nbWordsId, progressBarId, answerNbWords) {
  var text = $('#'+textareaId).val(); 
  renderTextareaNbWords(text, textareaId, nbWordsId, progressBarId, answerNbWords);
}

$(document).ready(function() {

checkTextareaNbWords('$uniqueTextareaId', '$uniqueTextareaNbWordsId', '$uniqueTextareaProgressBarId', $answerNbWords);

$('#$uniqueTextareaId').bind("keyup onload", function (event) {
  // Update only on additional words, that is, after a blank space is typed in
  if (event.which == 32 || event.which == 188 || event.which == 190 || event.which == 8 || event.which == 13 || event.which == 59) {
    checkTextareaNbWords('$uniqueTextareaId', '$uniqueTextareaNbWordsId', '$uniqueTextareaProgressBarId', $answerNbWords);
  }
});

$('#$uniqueTextareaProgressBarId').progressBar({
  width:100,
  boxImage:'$gJsUrl/jquery/jquery.progressbar.2.0/images/progressbar.gif',
  barImage:{
    0:'$gJsUrl/jquery/jquery.progressbar.2.0/images/progressbg_red.gif',
    30:'$gJsUrl/jquery/jquery.progressbar.2.0/images/progressbg_orange.gif',
    70:'$gJsUrl/jquery/jquery.progressbar.2.0/images/progressbg_green.gif'
  }
}).fadeIn();

});
</script>
HEREDOC;
      }

      $str .= <<<HEREDOC
<script type="text/javascript">
function saveTextareaLive(textareaId, elearningQuestionId) {
  var text = $('#'+textareaId).val();
  text = encodeURIComponent(text);
  var url = "$gElearningUrl/subscription/save_text_live.php";
  var params = []; params["elearningQuestionId"] = elearningQuestionId; params["text"] = text;
  ajaxAsynchronousPOSTRequest(url, params, postSaveTextareaLive);
}

function postSaveTextareaLive(responseText) {
  var response = eval('(' + responseText + ')');
  var elearningQuestionId = response.elearningQuestionId;
  unskipCopilotAnswerRefresh(elearningQuestionId);
}

$(document).ready(function() {

$('#$uniqueTextareaId').bind("keyup", function (event) {
  skipCopilotAnswerRefresh("$elearningQuestionId");
  // Update only on additional words
  if (event.which == 32 || event.which == 188 || event.which == 190 || event.which == 8 || event.which == 13 || event.which == 59) {
    // Always save live the text to type in
    var watchLive = 1;
    if (watchLive == 1) {
      saveTextareaLive('$uniqueTextareaId', '$elearningQuestionId');
    }
  }
});

});
</script>
HEREDOC;

    } else if ($this->typeIsWriteInQuestion($elearningExercisePage) || $this->typeIsWriteInText($elearningExercisePage)) {
      // The question displays a text input field

      $question = $elearningQuestion->getQuestion();
      $inputSize = $this->getQuestionInputFieldSize($question);

      // Display a hint in the input field itself if no answer has yet been typed in
      $hint = $elearningQuestion->getHint();
      if ($hint && !$participantAnswer && $this->hintInsideAnswer($elearningExercisePage)) {
        $participantAnswer = $hint;
      }

      $elearningQuestionId = $elearningQuestion->getId();
      $uniqueQuestionId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId());
      $uniqueQuestionInputId = $this->elearningQuestionUtils->renderUniqueQuestionInputId($elearningQuestion->getId());

      $str .= "<input class='elearning_question_answer_input auto_grow' type='text' name='$uniqueQuestionId' id='$uniqueQuestionInputId' size='$inputSize' maxlength='255' value=\"$participantAnswer\" onfocus='focusElement(this);' />";
      $handlerJs = <<<HEREDOC
<script type="text/javascript">
var watchLive = '$watchLive';
var displayInstantFeedback = '$displayInstantFeedback';

$(document).ready(function() {
$('#$uniqueQuestionInputId').focus(function() {
  latestChangedField = $('#$uniqueQuestionInputId');
  // The skip has to start when the participant starts typing in, and not when he is done typing in a field
  // otherwise his typing will be removed by the empty field on the remote copilot page
  skipCopilotAnswerRefresh("$elearningQuestionId");
});
$('#$uniqueQuestionInputId').blur(function() {
  setTimeout(function() {
    if (keyboardClicked == 0) {
      if (displayInstantFeedback) {
        getInstantFeedback('$elearningQuestionId', document.getElementById('$uniqueQuestionInputId').value);
      }
      if (watchLive == 1) {
        saveAnswersLive('$elearningQuestionId', document.getElementById('$uniqueQuestionInputId').value);
      }
    } else {
      keyboardClicked = 0;
    }
  }, 500);
});
});
</script>
HEREDOC;
      $str .= $handlerJs;
    } else if ($this->typeIsRequireOneOrMoreCorrectAnswers($elearningExercisePage) || $this->typeIsRequireAllPossibleAnswers($elearningExercisePage)) {
      // The question displays a series of checkboxes

      $elearningQuestionId = $elearningQuestion->getId();
      $handler = '';
      if ($displayInstantFeedback) {
        $handler .= "getInstantFeedback('$elearningQuestionId', getQuestionCheckboxesValues('$elearningQuestionId'));";
      }
      if ($watchLive) {
        $handler .= "saveAnswersLive('$elearningQuestionId', getQuestionCheckboxesValues('$elearningQuestionId'));";
      }
      if (!$handler) {
        $handler = '#';
      }

      $str .= "<br />";
      foreach ($answerWithMediaList as $elearningAnswerId => $value) {
        if (in_array($elearningAnswerId, $participantAnswer)) {
          $checked = 'checked';
        } else {
          $checked = '';
        }
        $uniqueAnswerId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId(), $elearningAnswerId);
        $str .= " <span><input class='system_input' style='vertical-align: middle;' type='checkbox' name='$uniqueAnswerId' id='$uniqueAnswerId' $checked value='$elearningAnswerId' onclick=\"$handler\"> <span onclick=\"clickAdjacentInputElement(this); $handler\" class='elearning_question_answer'> $value</span></span>";
      }

    } else if ($this->typeIsRadioButtonVertical($elearningExercisePage)) {
      // The question displays a series of radio buttons vertically

      $elearningQuestionId = $elearningQuestion->getId();
      $uniqueQuestionId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestionId);

      foreach ($answerWithMediaList as $elearningAnswerId => $answer) {
        if ($participantAnswer && $participantAnswer == $elearningAnswerId) {
          $checked = "checked";
        } else {
          $checked = '';
        }
        $uniqueAnswerId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId(), $elearningAnswerId);
        $handler = '';
        if ($displayInstantFeedback) {
          $handler .= "getInstantFeedback('$elearningQuestionId', getAnswerRadioValue('$uniqueAnswerId'));";
        }
        if ($watchLive) {
          $handler .= "saveAnswersLive('$elearningQuestionId', getAnswerRadioValue('$uniqueAnswerId'));";
        }
        if (!$handler) {
          $handler = '#';
        }
        $str .= "<div><input class='system_input' style='border:none 0px; vertical-align: middle;' type='radio' name='$uniqueQuestionId' id='$uniqueAnswerId' $checked value='$elearningAnswerId' onclick=\"$handler\"> <span onclick=\"clickAdjacentInputElement(this); $handler\" class='elearning_question_answer'>$answer</span></div>";
      }
    } else if ($this->typeIsRadioButtonHorizontal($elearningExercisePage)) {
      // The question displays a series of radio buttons horizontally

      $elearningQuestionId = $elearningQuestion->getId();
      $uniqueQuestionId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestionId);

      $str .= '<br />';
      foreach ($answerWithMediaList as $elearningAnswerId => $answer) {
        if ($participantAnswer && $participantAnswer == $elearningAnswerId) {
          $checked = "checked";
        } else {
          $checked = '';
        }
        $uniqueAnswerId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId(), $elearningAnswerId);
        $handler = '';
        if ($displayInstantFeedback) {
          $handler .= "getInstantFeedback('" . $elearningQuestion->getId() . "', getAnswerRadioValue('$uniqueAnswerId'));";
        }
        if ($watchLive) {
          $handler .= "saveAnswersLive('$elearningQuestionId', getAnswerRadioValue('$uniqueAnswerId'));";
        }
        if (!$handler) {
          $handler = '#';
        }
        $str .= " <span><input class='system_input' style='border:none 0px; vertical-align: middle;' type='radio' name='$uniqueQuestionId' id='$uniqueAnswerId' $checked value='$elearningAnswerId' onclick=\"$handler\"> <span onclick=\"clickAdjacentInputElement(this); $handler\" class='elearning_question_answer'>$answer</span></span>";
      }
    } else {
      // The question displays a drop down select list

      // Prepend an empty value in the list
      $answerList = LibUtils::arrayMerge(array('0' => ''), $answerList);

      $elearningQuestionId = $elearningQuestion->getId();
      $handler = '';
      if ($displayInstantFeedback) {
        $handler .= "getInstantFeedback('$elearningQuestionId', this[this.selectedIndex].value);";
      }
      if ($watchLive) {
        $handler .= "saveAnswersLive('$elearningQuestionId', this[this.selectedIndex].value);";
      }
      if (!$handler) {
        $handler = '#';
      }
      $uniqueQuestionId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId());
      $str .= "<select class='system_input' size='1' name='$uniqueQuestionId' onchange=\"$handler\">";
      foreach ($answerList as $elearningAnswerId => $value) {
        $uniqueAnswerId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestion->getId(), $elearningAnswerId);
        if (strlen($participantAnswer) > 0 && $participantAnswer == $elearningAnswerId) {
          $selected = "selected='selected'";
        } else {
          $selected = '';
        }
        $str .= " <option " . $selected . " id='$uniqueAnswerId' value='$elearningAnswerId'>$value</option>";
      }
      $str .= "</select>";
    }

    return($str);
  }

  // By default the on-screen keyboard displays a series of letters. But it is possible to specify the letters that are to be offered to the participant. If letters are typed in, then only these letters will be displayed as the on-screen keyboard.
  function getKeyboardLetters() {
    $keyboardLetters = $this->preferenceUtils->getValue("ELEARNING_KEYBOARD_LETTERS");

    return($keyboardLetters);
  }

  // By default, when displaying a page of questions that require the participant to type in the answers, a series of letters is displayed on top of the page. These letters are the ones that may be missing from the keyboard of the computer used by the participant. It is possible to hide this on-screen keyboard.
  function hideKeyboard($elearningExercise) {
    $hide = false;

    $hide = $elearningExercise->getHideKeyboard();

    if (!$hide) {
      $hide = $this->preferenceUtils->getValue("ELEARNING_HIDE_KEYBOARD");
    }

    return($hide);
  }

  // Render the question exercise page
  function render($elearningExercise, $elearningExercisePage, $elearningSubscription) {
    global $gDataPath;
    global $gDataUrl;
    global $gJsUrl;
    global $gUtilsUrl;
    global $gImagesUserUrl;
    global $gElearningUrl;
    global $gIsPhoneClient;
    global $gHostname;

    $this->loadLanguageTexts();

    $name = $elearningExercisePage->getName();
    $description = $elearningExercisePage->getDescription();
    $audio = $elearningExercisePage->getAudio();
    $video = $elearningExercisePage->getVideo();
    $videoUrl = $elearningExercisePage->getVideoUrl();

    if ($gIsPhoneClient) {
      $video = $this->commonUtils->adjustVideoWidthToPhone($video);
    }

    $watchLive = '';
    $elearningSubscriptionId = '';
    if ($elearningSubscription) {
      $watchLive = $elearningSubscription->getWatchLive();
      $elearningSubscriptionId = $elearningSubscription->getId();
    }

    $displayInstantFeedback = $this->displayInstantFeedback($elearningExercise, $elearningSubscription);

    $str = '';

    $str .= <<<HEREDOC
<script type="text/javascript">
var displayInstantFeedback = '$displayInstantFeedback';
var watchLive = '$watchLive';

$(document).ready(function() {
  $(".elearning_tooltip").wTooltip({
    follow: false,
    fadeIn: 300,
    fadeOut: 500,
    delay: 200,
    style: {
      width: "500px", // Required to avoid the tooltip being displayed off the right
      background: "#ffffff"
    }
  });
});

// Skip the automatic refresh of a question local answer if this answer has just been chosen by the participant
var copilotSkipRefresh = [];
function skipCopilotAnswerRefresh(elearningQuestionId) {
  copilotSkipRefresh[elearningQuestionId] = 1;
}
function unskipCopilotAnswerRefresh(elearningQuestionId) {
  copilotSkipRefresh[elearningQuestionId] = '';
}
function allowCopilotAnswerRefresh(elearningQuestionId) {
  return !copilotSkipRefresh[elearningQuestionId];
}

</script>
HEREDOC;

    if ($this->typeIsDragAndDropInQuestion($elearningExercisePage) || $this->typeIsDragAndDropInText($elearningExercisePage) || $this->typeIsDragAndDropOneAnswerInAnyQuestion($elearningExercisePage) || $this->typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningExercisePage)) {
      $ELEARNING_QUESTION_ID = ELEARNING_QUESTION_ID;
      $ELEARNING_ANSWERS_SEPARATOR = ELEARNING_ANSWERS_SEPARATOR;
      $str .= <<<HEREDOC
<style type="text/css">
.elearning_question_answer_draggable {
  cursor:pointer;
}
.droppable-hover {
  outline:2px dashed;
}
.no_style_elearning_dropped_multiple_answer {
  cursor:pointer;
  margin:2px;
}
.no_style_elearning_dropped_single_answer {
  cursor:pointer;
  border-width:0px 0px 1px 0px;
  border-style:solid;
}
.elearning_question_answer_input {
  border-width:0px 0px 1px 0px;
  border-style:solid;
}
</style>
<script type="text/javascript">
function removeAnswerId(strDroppedAnswerIds, elearningAnswerId) {
  var droppedAnswerIds = strDroppedAnswerIds.split('$ELEARNING_ANSWERS_SEPARATOR');
  var newDroppedAnswerIds = '';
  for (i = 0; i < droppedAnswerIds.length; i++) {
    if (droppedAnswerIds[i] != elearningAnswerId) {
      if (newDroppedAnswerIds.length == 0) {
        newDroppedAnswerIds = droppedAnswerIds[i];
      } else {
        newDroppedAnswerIds = newDroppedAnswerIds + '$ELEARNING_ANSWERS_SEPARATOR' + droppedAnswerIds[i];
      }
    }
  }
  return(newDroppedAnswerIds);
}

$(document).ready(function() {
  $(".elearning_question_answer_draggable").draggable({
    helper: 'clone', // Drag a copy of the element
    ghosting: true, // Display the element in semi transparent fashion when dragging
    opacity: 0.5, // The transparency level of the dragged element
    cursorAt: { top: 10, left: 10 }, // Position the mouse cursor in the dragged element when starting to drag
    cursor: 'move', // Change the cursor shape when dragging
    revert: 'invalid', // Put back the dragged element if it could not be dropped
    containment: '.elearning_exercise_page' // Limit the area of dragging
  });

  $(".elearning_question_droppable").droppable({
    accept: '.elearning_question_answer_draggable', // Specify what kind of element can be dropped
    hoverClass: 'droppable-hover', // Styling a droppable when hovering on it
    tolerance: 'pointer', // Assume a droppable fit when the mouse cursor hovers
    drop: // Handle a drop event
      function(ev, ui) {
        var participantAnswer = '';
        if ($(this).attr("multipleAnswers")) {
          // The unique answer id needs to be built after the answer has been dropped
          // as an answer may be dropped in another question than its own
          var uniqueQuestionId = $(this).attr("uniqueQuestionId");
          // All the answer ids are concatenated into a string and stored in the input field
          var strDroppedAnswerIds = $(this).find('input.no_style_elearning_dropped_ids').attr("value");
          if (strDroppedAnswerIds.length > 0) {
            strDroppedAnswerIds = strDroppedAnswerIds + '$ELEARNING_ANSWERS_SEPARATOR';
          }
          // Do not accept an already dropped answer
          var alreadyDropped = false;
          var droppedAnswerIds = strDroppedAnswerIds.split('$ELEARNING_ANSWERS_SEPARATOR');
          for (i = 0; i < droppedAnswerIds.length; i++) {
            if (droppedAnswerIds[i] == ui.draggable.attr("elearningAnswerId")) {
              alreadyDropped = true;
            }
          }
          if (!alreadyDropped) {
            strDroppedAnswerIds = strDroppedAnswerIds + ui.draggable.attr("elearningAnswerId");
            $(this).find('input.no_style_elearning_dropped_ids').attr("value", strDroppedAnswerIds);
            participantAnswer = strDroppedAnswerIds;
            var droppedContent = "<div class='elearning_question_answer no_style_elearning_dropped_multiple_answer' elearningAnswerId='" + ui.draggable.attr("elearningAnswerId") + "'>" + trim(ui.draggable.html()) + "</div>";
            $(this).find('div.no_style_elearning_dropped_multiple_answers').append(droppedContent);

            // Get an instant correction if required
            if (displayInstantFeedback) {
              getInstantFeedback($(this).attr("elearningQuestionId"), participantAnswer);
            }
            if (watchLive == 1) {
              saveAnswersLive($(this).attr("elearningQuestionId"), participantAnswer);
            }
          }
        } else {
          // Update the answer id and display
          $(this).find('input').attr("value", ui.draggable.attr("elearningAnswerId"));
          $(this).find('span.no_style_elearning_dropped_single_answer').html(ui.draggable.html());
          participantAnswer = $(this).find('input').attr("value");

          // Get an instant correction if required
          if (displayInstantFeedback) {
            getInstantFeedback($(this).attr("elearningQuestionId"), participantAnswer);
          }
          if (watchLive == 1) {
            saveAnswersLive($(this).attr("elearningQuestionId"), participantAnswer);
          }
        }
      }
  });

  // Clear the answer, the displayed content and the instant correction
  $(".elearning_question_droppable").click(function(event) {
    if ($(this).attr("multipleAnswers")) {
      var domDroppedIds = $(this).find('input.no_style_elearning_dropped_ids');
      var droppedObj = $(event.target).closest("div.no_style_elearning_dropped_multiple_answer")[0];
      if (droppedObj.className.indexOf('no_style_elearning_dropped_multiple_answer') != -1) {
        var elearningAnswerId = $(droppedObj).attr("elearningAnswerId");
        var newDroppedAnswerIds = removeAnswerId(domDroppedIds.attr('value'), elearningAnswerId);
        domDroppedIds.attr("value", newDroppedAnswerIds);
        $(droppedObj).remove();
        if (displayInstantFeedback) {
          getInstantFeedback($(this).attr("elearningQuestionId"), newDroppedAnswerIds);
        }
        if (watchLive == 1) {
          saveAnswersLive($(this).attr("elearningQuestionId"), newDroppedAnswerIds);
        }
      }
    } else {
      $(this).find('input').attr("value", "");
      $(this).find('span.no_style_elearning_dropped_single_answer').html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
      if (displayInstantFeedback) {
        getInstantFeedback($(this).attr("elearningQuestionId"), '');
      }
      if (watchLive == 1) {
        saveAnswersLive($(this).attr("elearningQuestionId"), '');
      }
    }
  });
});
</script>
HEREDOC;
    } else if ($this->typeIsDragAndDropOrderSentence($elearningExercisePage)) {
      $ELEARNING_QUESTION_ID = ELEARNING_QUESTION_ID;
      $ELEARNING_ANSWERS_SEPARATOR = ELEARNING_ANSWERS_SEPARATOR;

      $str .= <<<HEREDOC
<style type="text/css">
.no_style_elearning_question_sortable {
  white-space:nowrap;
  height:20px; // Hack: a height is required to prevent an offset of the dragged element
}
.elearning_question_answer_draggable {
  float:left;
  cursor:move;
  margin-right:4px;
}
.no_style_sortable_helper {
  float:left;
  width:100px; height:20px; // Hack: display an empty space while hovering between two items
}
.no_style_sortable_stop_float {
  clear:left;
}
</style>

<script type="text/javascript">
$(document).ready(function(){
  $('.no_style_elearning_question_sortable').sortable({
    accept: 'elearning_question_answer_draggable',
    placeholder: 'no_style_sortable_helper',
    axis: 'x',
    revert: true,
    containment: 'parent',
    start: function(ev, ui) {
        var bits = ui.item.attr("id").split("_");
        var elearningQuestionId = bits[bits.length - 2];
        skipCopilotAnswerRefresh(elearningQuestionId);
      },
    update: // Handle an update event
      function(ev, ui) {
        // Update the order values and the sentence
        var newOrderIds = '';
        var sentence = '';
        var uniqueAnswerIds = $(this).sortable("toArray");
        for (i = 0; i < uniqueAnswerIds.length; i++) {
          var uniqueAnswerId = uniqueAnswerIds[i];
          var bits = uniqueAnswerId.split("_");
          var elearningAnswerId = bits[bits.length - 1];

          if (newOrderIds.length > 0) {
            newOrderIds = newOrderIds + "$ELEARNING_ANSWERS_SEPARATOR";
          }
          newOrderIds = newOrderIds + elearningAnswerId;
          if (sentence.length > 0) {
            sentence = sentence + ' ';
          }
          sentence = sentence + stripTags($("#" + uniqueAnswerId).html());
        }
        var bits = ui.item.attr("id").split("_");
        var elearningQuestionId = bits[bits.length - 2];
        if (displayInstantFeedback) {
          getInstantFeedback($(this).attr("elearningQuestionId"), newOrderIds);
        }
        if (watchLive == 1) {
          saveAnswersLive($(this).attr("elearningQuestionId"), newOrderIds);
        }
      }
  });
});
</script>
HEREDOC;
    } else if ($this->typeIsWriteText($elearningExercisePage)) {
      $str .= <<<HEREDOC
<script type='text/javascript'>
// Check the number of words in a textarea
function renderTextareaNbWords(str, textareaId, renderNbWordsId, progressBarId, answerNbWords) {
  // Get the number of words in a string
  renderNbWordsId = encodeURIComponent(renderNbWordsId);
  str = encodeURIComponent(str);
  var url = '$gUtilsUrl/get_nb_words.php?textareaId='+textareaId+'&renderNbWordsId='+renderNbWordsId+'&progressBarId='+progressBarId+'&answerNbWords='+answerNbWords+'&str='+str;
  ajaxAsynchronousRequest(url, renderNbWordsMessage);
}

// Render a progress bar on the number of words typed in a textarea
// relative to the number of expected words
function renderNbWordsMessage(responseText) {
  var response = eval('(' + responseText + ')');
  var textareaId = response.textareaId;
  var renderNbWordsId = response.renderNbWordsId;
  var progressBarId = response.progressBarId;
  var answerNbWords = parseInt(response.answerNbWords);
  var nbWords = parseInt(response.nbWords);

  // Prevent the typing of words if the number of typed in words exceeds the specified limit
  if (nbWords > answerNbWords) {
    // Remove the last two words
    $('#'+textareaId).val($('#'+textareaId).val().replace(/\s*[^\s]\s*[^\s]*\s*$/, ' '));
  } else {
    // Update the displayed number of typed in words
    if (nbWords > 1) {
      $('#'+renderNbWordsId).html(nbWords); 

      // Render a multi colored progress bar on the percentage of words typed in the textarea
      var percentage = nbWords * 100 / answerNbWords;
      $('#'+progressBarId).progressBar(percentage);
    }
  }
}
</script>
HEREDOC;
    } else if ($this->typeIsWriteInQuestion($elearningExercisePage) || $this->typeIsWriteInText($elearningExercisePage)) {
      $str .= <<<HEREDOC
<script type='text/javascript'>
$(document).ready(function() {
// Turn the enter key into a tab to the next input field
$('.elearning_question_answer_input').live("keypress", function(e) {
  if (e.keyCode == 13) {
    var inputFields = $(this).parents('form:eq(0),body').find(':input.elearning_question_answer_input').not('[type=hidden]');
    var index = inputFields.index(this);
    if (index == inputFields.length - 1) {
      inputFields[0].select();
    } else {
      inputFields[index + 1].focus(); // handles submit buttons
      inputFields[index + 1].select();
    }
    return false;
  }
});
});
</script>
HEREDOC;
    }

    $str .= "\n<div class='elearning_exercise_page'>";

    if ($name) {
      $str .= "\n<div class='elearning_exercise_page_name'>$name</div>";
    }

    if ($description) {
      $str .= "\n<div class='elearning_exercise_page_description'>$description</div>";
    }

    $str .= $this->renderImage($elearningExercisePage);

    if ($video) {
      $str .= "\n<div class='elearning_exercise_page_video'>$video</div>";
    }

    if ($videoUrl) {
      $video = LibStreaming::renderVideoFromUrl($videoUrl);
      if (!$video) {
        $video = "<a href='$videoUrl' title='" . $this->websiteText[12] . "' onclick=\"window.open(this.href, '_blank'); return(false);\">"
          . $this->websiteText[11]
          . "</a>";
      }
      $str .= "\n<div class='elearning_exercise_page_video'>" . $video . "</div>";
    }

    if ($audio) {
      $strPlayer = $this->renderPlayer($audio);
      $str .= "<div class='elearning_exercise_player'>$strPlayer</div>";
    }

    // Render some instructions at the top of the page of questions
    $str .= $this->renderStartInstructions($elearningExercisePage);

    if ($this->isWrittenAnswer($elearningExercisePage)) {
      $str .= <<<HEREDOC
<script type='text/javascript'>
var focusedElement = '';
function focusElement(element) {
  focusedElement = element;
}
</script>
HEREDOC;

      $keyboardLetters = $this->getKeyboardLetters();
      if (!$this->hideKeyboard($elearningExercise)) {
        $str .= "<div class='elearning_exercise_page_keyboard'>" 
          . "<span title='" . $this->websiteText[20] . "'>" . $this->websiteText[19] . "</span>"
          . $this->commonUtils->renderKeyboard($keyboardLetters, $this->websiteText[21]) 
          . "</div>";
      }
    }

    if ($displayInstantFeedback || $watchLive) {
      $ELEARNING_QUESTION_ID = ELEARNING_QUESTION_ID;
      $ELEARNING_INSTANT_FEEDBACK_ID = ELEARNING_INSTANT_FEEDBACK_ID;
      $ELEARNING_ANSWERS_SEPARATOR = ELEARNING_ANSWERS_SEPARATOR;

      $str .= <<<HEREDOC
<script type='text/javascript'>
function getAnswerRadioValue(uniqueAnswerId) {
  var value = document.getElementById(uniqueAnswerId).value;
  return value;
}

// Get the values of all the checkboxes for the question
function getQuestionCheckboxesValues(elearningQuestionId) {
  var participantAnswers = '';
  var checkboxes = document.getElementById('exercise_form').elements;
  var answerCheckboxNamePrefix = '$ELEARNING_QUESTION_ID' + elearningQuestionId;
  for (i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i].name.indexOf(answerCheckboxNamePrefix, 0) != -1) {
      var questionCheckbox = checkboxes[i];
      if (questionCheckbox.checked) {
        if (participantAnswers != '') {
          participantAnswers = participantAnswers + '$ELEARNING_ANSWERS_SEPARATOR';
        }
      participantAnswers = participantAnswers + questionCheckbox.value;
      }
    }
  }
  return participantAnswers;
}

// Get the instant solution for the question
function getInstantFeedback(elearningQuestionId, participantAnswer) {
  participantAnswer = encodeURIComponent(participantAnswer);
  var url = '$gElearningUrl/exercise_page/getInstantFeedback.php?elearningSubscriptionId=$elearningSubscriptionId&elearningQuestionId='+elearningQuestionId+'&participantAnswer='+participantAnswer;
console.log(url);
  ajaxAsynchronousRequest(url, renderInstantFeedback);
}

// Render an instant correction, that is, when the participant answers the question
function renderInstantFeedback(responseText) {
  var response = eval('(' + responseText + ')');
  var questionDivId = '$ELEARNING_QUESTION_ID' + response.elearningQuestionId;
  var explanationDivId = '$ELEARNING_INSTANT_FEEDBACK_ID' + response.elearningQuestionId;
  if (response.displayInstantFeedback) {
    // No error message is displayed if the answer is correct
    // or if no answers were given
    if (response.isCorrectlyAnswered || response.nbGivenAnswers == 0) {
      resetBorder(document.getElementById(questionDivId));
    } else {
      if (response.displayInstantCorrection && response.displayInstantCorrectionNoAnswer) {
        underlineBorder(document.getElementById(questionDivId));
      }
    }
    document.getElementById(explanationDivId).innerHTML = response.explanation + " ";
  } else {
    resetBorder(document.getElementById(questionDivId));
    document.getElementById(explanationDivId).innerHTML = "";
  }
}

var underlineBackupClassName = '';
function underlineBorder(element) {
  element.className = 'elearning_question_instant_correction';
}
function resetBorder(element) {
  element.className = '';
}
</script>
HEREDOC;
    }

    if ($watchLive) {
      $NODEJS_SOCKET_PORT = NODEJS_SOCKET_PORT;

      $str .= <<<HEREDOC
<script type="text/javascript">
$(function() {
  if ('undefined' != typeof io) {
    elearningSocket = io.connect('$gHostname:$NODEJS_SOCKET_PORT/elearning');
    elearningSocket.on('connect', function() {
      console.log('The elearning namespace socket connected');
      elearningSocket.emit('watchLiveCopilot', {'elearningSubscriptionId': '$elearningSubscriptionId'});
    });
    elearningSocket.on('message', function(message) {
      console.log(message);
    });
  }
});
</script>
<ul id="watchLiveInfo"></ul>
HEREDOC;

      $str .= <<<HEREDOC
<script type='text/javascript'>
// Save the results for the live watch
function saveAnswersLive(elearningQuestionId, participantAnswer) {
  participantAnswer = encodeURIComponent(participantAnswer);

  skipCopilotAnswerRefresh(elearningQuestionId);

  var url = '$gElearningUrl/subscription/save_answers_live.php?elearningQuestionId='+elearningQuestionId+'&participantAnswer='+participantAnswer;
  ajaxAsynchronousRequest(url, postSaveLiveAnswers);
}

function postSaveLiveAnswers(responseText) {
  var response = eval('(' + responseText + ')');
  var elearningQuestionId = response.elearningQuestionId;
  unskipCopilotAnswerRefresh(elearningQuestionId);

  url = '$gElearningUrl/subscription/get_live_question_answers.php?elearningQuestionId='+elearningQuestionId;
  ajaxAsynchronousRequest(url, copilotRefreshQuestion);
}

function copilotRefreshQuestion(responseText) {
  var response = eval('(' + responseText + ')');
  var elearningResultId = response.elearningResultId;
  var elearningQuestionId = response.elearningQuestionId;
  var questionType = response.questionType;
  var uniqueQuestionId = response.uniqueQuestionId;
  var uId = response.uId;
  var uiId = response.uiId;
  var utId = response.utId;
  var isCorrect = response.isCorrect;
  var isAnswered = response.isAnswered;
  var answers = response.answers;

  if ('undefined' != typeof elearningSocket) {
    elearningSocket.emit('updateQuestion', {'elearningResultId': elearningResultId, 'elearningSubscriptionId': '$elearningSubscriptionId', 'elearningQuestionId': elearningQuestionId, 'questionType': questionType, 'uniqueQuestionId': uniqueQuestionId, 'uId': uId, 'uiId': uiId, 'utId': utId, 'isCorrect': isCorrect, 'isAnswered': isAnswered, 'answers': answers});
  }
}
</script>
HEREDOC;
    }

    // Render the text of the page of questions
    $text = $this->renderPageText($elearningExercise, $elearningExercisePage, $elearningSubscription, false);
    if ($text) {
      $str .= "<div class='elearning_exercise_page_text'>$text</div>";
    }

    // Render the draggable answers located within the text
    if ($this->typeIsDragAndDropInText($elearningExercisePage)) {

      $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePage->getId());
      $shuffledAnswers = array();
      foreach ($elearningQuestions as $elearningQuestion) {
        $elearningQuestionId = $elearningQuestion->getId();
        if ($elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId)) {
          foreach ($elearningAnswers as $elearningAnswer) {
            $elearningAnswerId = $elearningAnswer->getId();
            $answer = $elearningAnswer->getAnswer();
            $uniqueAnswerId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestionId, $elearningAnswerId);
            $shuffledAnswers[$elearningAnswerId] = array($uniqueAnswerId, $answer);
          }
        }
      }
      $shuffledAnswers = LibUtils::shuffleArray($shuffledAnswers);
      foreach ($shuffledAnswers as $elearningAnswerId => $item) {
        list($uniqueAnswerId, $answer) = $item;
        $str .= "<span class='elearning_question_answer'><span id='$uniqueAnswerId' elearningAnswerId='$elearningAnswerId' class='elearning_question_answer_draggable' style='vertical-align: middle;'>$answer</span></span>";
      }

    } else if ($this->typeIsDragAndDropOrderSentence($elearningExercisePage)) {
      // Render the draggable answers for questions that are sentences
      // assembled from ordered questions

      $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePage->getId());

      // Display the questions in a random order
      if ($this->shuffleQuestions($elearningSubscription)) {
        $elearningQuestions = LibUtils::shuffleArray($elearningQuestions);
      }

      foreach ($elearningQuestions as $elearningQuestion) {
        $elearningQuestionId = $elearningQuestion->getId();

        $str .= "\n<div class='elearning_exercise_page_question' id='" . $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestionId) . "'>";

        $str .= "<div class='elearning_exercise_page_question_sentence'>";

        $questionInputField = $this->renderQuestionInput($elearningExercise, $elearningExercisePage, $elearningQuestion, $elearningSubscription, false);
        $str .= $questionInputField;

        $str .= "</div>";

        if ($displayInstantFeedback || $watchLive) {
          $str .= ' ' . "<div id='" . ELEARNING_INSTANT_FEEDBACK_ID . $elearningQuestionId . "'></div>";
        }

        $str .= "</div>";
      }

    } else if ($this->typeIsDragAndDropOneAnswerInAnyQuestion($elearningExercisePage) || $this->typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningExercisePage)) {
      // Render the draggable answers and/or images on the right of the questions
      // with all questions answers and/or images being shuffled and draggable under any question
      // It is possible to drag either one, or several answers and/or images under each question

      if ($this->typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningExercisePage)) {
        $multipleAnswers = true;
      } else {
        $multipleAnswers = false;
      }

      $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePage->getId());

      $str .= "<table border='0' width='100%' cellspacing='10' cellpadding='2'>";

      // Shuffle the answers across all questions
      $shuffledAnswers = array();
      $allQuestionsAnswers = array();
      foreach ($elearningQuestions as $elearningQuestion) {
        $elearningQuestionId = $elearningQuestion->getId();
        $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId);
        foreach ($elearningAnswers as $elearningAnswer) {
          array_push($allQuestionsAnswers, $elearningAnswer);
        }
      }
      shuffle($allQuestionsAnswers);
      $numberOfAnswers = count($allQuestionsAnswers);
      if (count($elearningQuestions) > 0) {
        $numberOfAnswersPerQuestion = floor($numberOfAnswers / count($elearningQuestions));
      } else {
        $numberOfAnswersPerQuestion = 0;
      }
      $numberOfRemainingAnswers = $numberOfAnswers - ($numberOfAnswersPerQuestion * count($elearningQuestions));
      $j = 0;
      foreach ($elearningQuestions as $elearningQuestion) {
        $oneQuestionsAnswers = array();
        for ($i = $j * $numberOfAnswersPerQuestion; $i < ($j + 1) * $numberOfAnswersPerQuestion; $i++) {
          array_push($oneQuestionsAnswers, $allQuestionsAnswers[$i]);
        }
        $elearningQuestionId = $elearningQuestion->getId();
        $shuffledAnswers[$elearningQuestionId] = $oneQuestionsAnswers;
        $j++;
      }
      $i = $numberOfAnswers - $numberOfRemainingAnswers;
      foreach ($elearningQuestions as $elearningQuestion) {
        if ($i < $numberOfAnswers) {
          $elearningQuestionId = $elearningQuestion->getId();
          $oneQuestionsAnswers = $shuffledAnswers[$elearningQuestionId];
          array_push($oneQuestionsAnswers, $allQuestionsAnswers[$i]);
          $shuffledAnswers[$elearningQuestionId] = $oneQuestionsAnswers;
        }
        $i++;
      }

      // Display the questions in a random order
      if ($this->shuffleQuestions($elearningSubscription)) {
        $elearningQuestions = LibUtils::shuffleArray($elearningQuestions);
      }

      foreach ($elearningQuestions as $elearningQuestion) {
        $elearningQuestionId = $elearningQuestion->getId();

        // The answers of the questions can be dragged so as to be dropped on the question
        // The droppable area reaches under the question and encloses its already dropped answers
        $uniqueQuestionId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestionId);
        $str .= "<tr><td style='vertical-align:top; width:65%;' class='elearning_question_droppable' elearningQuestionId='$elearningQuestionId' uniqueQuestionId='$uniqueQuestionId' multipleAnswers='$multipleAnswers'>";

        $str .= "\n<div class='elearning_exercise_page_question' id='$uniqueQuestionId'>";

        $str .= $this->elearningQuestionUtils->renderImage($elearningQuestion);

        $audio = $elearningQuestion->getAudio();
        if ($audio) {
          $strPlayer = $this->elearningQuestionUtils->renderPlayer($audio);
          $str .= ' ' . "<span class='elearning_question_player' style='vertical-align:middle;'>" . $strPlayer . "</span>";
        }

        $strQuestion = '';

        $question = $elearningQuestion->getQuestion();

        // Render the answer input field for the question
        $questionInputField = $this->renderQuestionInput($elearningExercise, $elearningExercisePage, $elearningQuestion, $elearningSubscription, false);

        // Split the question in bits enclosing the answer location
        $questionBits = $this->getQuestionBits($question);

        $strQuestion .= "<span class='elearning_exercise_page_question_sentence'>";
        $strQuestion .= $questionBits[0];
        $strQuestion .= "</span>";
        if (count($questionBits) > 1) {
          $strQuestion .= "<span class='elearning_exercise_page_question_sentence'>";
          $strQuestion .= ' ' . $questionBits[1];
          $strQuestion .= "</span>";
        }

        // Render a hint
        $hint = $elearningQuestion->getHint();
        if ($hint) {
          if ($hint && $this->hintInPopup($elearningExercisePage)) {
            if ($gIsPhoneClient) {
              $strQuestion .= ' ' . $this->renderHintHidden($hint);
            } else {
              $strQuestion .= ' ' . $this->renderHintInPopup($hint);
            }
          } else {
            $strQuestion .= " (<span class='elearning_question_hint'><img src='$gImagesUserUrl/" . IMAGE_COMMON_HINT . "' class='no_style_image_icon' title='' alt='' /> " . $hint . "</span>)";
          }
        }

        $strQuestion .= ' ' . $questionInputField;

        $str .= ' ' . $strQuestion;

        if ($displayInstantFeedback || $watchLive) {
          $str .= ' ' . "<div id='" . ELEARNING_INSTANT_FEEDBACK_ID . $elearningQuestionId . "'></div>";
        }

        $str .= "</div>";

        $str .= "</td><td style='vertical-align:top; text-align:center; width:35%;'>";

        if (isset($shuffledAnswers[$elearningQuestionId])) {
          $elearningAnswers = $shuffledAnswers[$elearningQuestionId];
          $answerList = $this->getQuestionAnswersByIds($elearningAnswers);
          foreach ($elearningAnswers as $elearningAnswer) {
            $elearningAnswerId = $elearningAnswer->getId();
            $answer = $elearningAnswer->getAnswer();
            $image = $elearningAnswer->getImage();
            if ($image) {
              $answer .= $this->elearningAnswerUtils->renderImage($elearningAnswerId);
            }
            $uniqueAnswerId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestionId, $elearningAnswerId);
            $str .= "<span class='elearning_question_answer'><span id='$uniqueAnswerId' elearningAnswerId='$elearningAnswerId' class='elearning_question_answer_draggable' style='vertical-align:middle;'>$answer</span></span><br />";
          }
        }

        $str .= "</td></tr>";
      }

      $str .= "</table>";

    } else if ($this->typeIsWriteInQuestion($elearningExercisePage) || $this->typeIsSelectInQuestion($elearningExercisePage) || $this->typeIsRequireOneOrMoreCorrectAnswers($elearningExercisePage) || $this->typeIsRequireAllPossibleAnswers($elearningExercisePage) || $this->typeIsDragAndDropInQuestion($elearningExercisePage) || $this->typeIsRadioButtonVertical($elearningExercisePage) || $this->typeIsRadioButtonHorizontal($elearningExercisePage) || $this->typeIsWriteText($elearningExercisePage)) {
      // Render all the other types of questions of the exercise page
      // that are not displayed within the page text

      $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePage->getId());

      // Display the questions in a random order
      if ($this->shuffleQuestions($elearningSubscription)) {
        $elearningQuestions = LibUtils::shuffleArray($elearningQuestions);
      }

      foreach ($elearningQuestions as $elearningQuestion) {
        $elearningQuestionId = $elearningQuestion->getId();

        $str .= "\n<div class='elearning_exercise_page_question' id='" . $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestionId) . "'>";

        $str .= $this->elearningQuestionUtils->renderImage($elearningQuestion);

        $audio = $elearningQuestion->getAudio();
        if ($audio) {
          $strPlayer = $this->elearningQuestionUtils->renderPlayer($audio);
          $str .= ' ' . "<span class='elearning_question_player' style='vertical-align:middle;'>" . $strPlayer . "</span>";
        }

        $strQuestion = '';

        // Render the answer input field for the question
        $questionInputField = $this->renderQuestionInput($elearningExercise, $elearningExercisePage, $elearningQuestion, $elearningSubscription, false);

        // Split the question in bits enclosing the answer location
        $question = $elearningQuestion->getQuestion();
        $questionBits = $this->getQuestionBits($question);

        // Some types of questions must have the answers not displayed within their questions for a more readable display
        $answerNotWithinQuestion = false;
        if (count($questionBits) > 1 && ($this->typeIsRequireOneOrMoreCorrectAnswers($elearningExercisePage) || $this->typeIsRequireAllPossibleAnswers($elearningExercisePage) || $this->typeIsRadioButtonVertical($elearningExercisePage) || $this->typeIsRadioButtonHorizontal($elearningExercisePage) || $this->typeIsWriteText($elearningExercisePage))) {
          $answerNotWithinQuestion = true;
        }

        $strQuestion .= "<span class='elearning_exercise_page_question_sentence'>";

        $strQuestion .= $questionBits[0];

        $hint = $elearningQuestion->getHint();
        if ($hint && $this->hintBeforeAnswer($elearningExercisePage)) {
          $strQuestion .= " (<span class='elearning_question_hint'><img src='$gImagesUserUrl/" . IMAGE_COMMON_HINT . "' class='no_style_image_icon' title='' alt='' /> " . $hint . "</span>)";
        }

        if ($answerNotWithinQuestion) {
          $strQuestion .= ' ' . ELEARNING_ANSWER_UNDERSCORE;
        } else {
          $strQuestion .= ' ' . $questionInputField;
        }

        // Render a hint
        if ($hint && ($this->hintAfterAnswer($elearningExercisePage))) {
          $strQuestion .= " (<span class='elearning_question_hint'><img src='$gImagesUserUrl/" . IMAGE_COMMON_HINT . "' class='no_style_image_icon' title='' alt='' /> " . $hint . "</span>)";
        } else if ($hint && $this->hintInPopup($elearningExercisePage)) {
          if ($gIsPhoneClient) {
            $strQuestion .= ' ' . $this->renderHintHidden($hint);
          } else {
            $strQuestion .= ' ' . $this->renderHintInPopup($hint);
          }
        }

        if (count($questionBits) > 1) {
          $strQuestion .= $questionBits[1];
        }

        $strQuestion .= "</span>";

        if ($hint && $this->hintEndOfQuestion($elearningExercisePage)) {
          $strQuestion .= " (<span class='elearning_question_hint'><img src='$gImagesUserUrl/" . IMAGE_COMMON_HINT . "' class='no_style_image_icon' title='' alt='' /> " . $hint . "</span>)";
        }

        if ($answerNotWithinQuestion) {
          $strQuestion .= ' ' . $questionInputField;
        }

        $str .= ' ' . $strQuestion;

        // Render the draggable answers for questions whose answers can be dragged
        if ($this->typeIsDragAndDropInQuestion($elearningExercisePage)) {
          $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId);
          $answerList = $this->getQuestionAnswersByIds($elearningAnswers);

          $str .= '<div>';
          foreach ($answerList as $elearningAnswerId => $answer) {
            $uniqueAnswerId = $this->elearningQuestionUtils->renderUniqueQuestionId($elearningQuestionId, $elearningAnswerId);
            $str .= "<span class='elearning_question_answer'><span id='$uniqueAnswerId' elearningAnswerId='$elearningAnswerId' class='elearning_question_answer_draggable' style='vertical-align: middle;'>$answer</span></span>";
          }
          $str .= '</div>';
        }

        if ($displayInstantFeedback || $watchLive) {
          $str .= ' ' . "<div id='" . ELEARNING_INSTANT_FEEDBACK_ID . $elearningQuestionId . "'></div>";
        }

        $str .= "</div>";
      }

    }

    $str .= $this->renderEndInstructions();

    $str .= "\n</div>";

    if ($elearningSubscription) {
      if ($elearningSubscription->getWatchLive()) {
        $elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
        $elearningExercisePageId = $elearningExercisePage->getId();
        $ELEARNING_ANSWER_ORDER_ID = ELEARNING_ANSWER_ORDER_ID;
        $ELEARNING_ANSWERS_SEPARATOR = ELEARNING_ANSWERS_SEPARATOR;
        $strRefresh = <<<HEREDOC
<script type="text/javascript">
$(function() {
  if ('undefined' != typeof elearningSocket) {
    elearningSocket.on('updateTab', function(data) {
      if (data.elearningExercisePageId != '$elearningExercisePageId') {
        var url = "$gElearningUrl/exercise/exercise_controller.php";
        document.exercise_form.action = url;
        document.exercise_form.elearningExercisePageId.value = data.elearningExercisePageId;
        document.exercise_form.submit();
      }
    });

    elearningSocket.on('updateQuestion', function(data) {
      copilotRefreshQuestionAnswers(data.elearningQuestionId, data.questionType, data.uId, data.uiId, data.utId, data.isCorrect, data.isAnswered, data.answers);
    });
  }
});

function copilotRefreshQuestionAnswers(elearningQuestionId, questionType, uId, uiId, utId, isCorrect, isAnswered, answers) {
    var uniqueQuestionId = uId;
    if (questionType == 'SELECT_LIST' || questionType == 'SELECT_LIST_IN_TEXT') {
      // Clear current participant answers
      $('#'+uniqueQuestionId).find('select').each(function() {
        if ($(this).prop("selectedIndex") > 0) {
          $(this).prop("selectedIndex", 0);
          if (isAnswered != 1) {
            if (displayInstantFeedback) {
              getInstantFeedback(elearningQuestionId, '');
            }
          }
        }
      });

      for (j = 0; j < answers.length; j++) {
        var elearningAnswerId = answers[j].id;
        var uniqueAnswerId = answers[j].uId;
        var remoteParticipantAnswer = answers[j].pA;
        if (remoteParticipantAnswer > 0) {
          $('#'+uniqueAnswerId).attr('selected', true);
          if (displayInstantFeedback) {
            getInstantFeedback(elearningQuestionId, elearningAnswerId);
          }
        }
      }
    } else if (questionType == 'RADIO_BUTTON_LIST_H' || questionType == 'RADIO_BUTTON_LIST_V') {
      for (j = 0; j < answers.length; j++) {
        var elearningAnswerId = answers[j].id;
        var uniqueAnswerId = answers[j].uId;
        var remoteParticipantAnswer = answers[j].pA;
        if (!$('#'+uniqueAnswerId).attr('checked')) {
          if (remoteParticipantAnswer > 0) {
            $('#'+uniqueAnswerId).attr('checked', true);
            if (displayInstantFeedback) {
              getInstantFeedback(elearningQuestionId, elearningAnswerId);
            }
          }
        }
      }
    } if (questionType == 'SOME_CHECKBOXES' || questionType == 'ALL_CHECKBOXES') {
      // Clear current participant answers
      $('#'+uniqueQuestionId).find('input[type=checkbox]:checked').each(function() {
        $(this).attr('checked', false);
        if (isAnswered != 1) {
          if (displayInstantFeedback) {
            getInstantFeedback(elearningQuestionId, getQuestionCheckboxesValues(elearningQuestionId));
          }
        }
      });

      for (j = 0; j < answers.length; j++) {
        var elearningAnswerId = answers[j].id;
        var uniqueAnswerId = answers[j].uId;
        var remoteParticipantAnswer = answers[j].pA;
        if (remoteParticipantAnswer < 1 && $('#'+uniqueAnswerId).attr('checked') == 'checked') {
          $('#'+uniqueAnswerId).attr('checked', false);
          if (displayInstantFeedback) {
            getInstantFeedback(elearningQuestionId, getQuestionCheckboxesValues(elearningQuestionId));
          }
        } else if (remoteParticipantAnswer > 0 && $('#'+uniqueAnswerId).attr('checked') != 'checked') {
          $('#'+uniqueAnswerId).attr('checked', true);
          if (displayInstantFeedback) {
            getInstantFeedback(elearningQuestionId, getQuestionCheckboxesValues(elearningQuestionId));
          }
        }
      }
    } else if (questionType == 'WRITE_IN_QUESTION' || questionType == 'WRITE_IN_TEXT') {
      for (j = 0; j < answers.length; j++) {
        var remoteParticipantAnswer = answers[j].pA;
        var uniqueQuestionInputId = questions[i].uiId;
        if (remoteParticipantAnswer != $('#'+uniqueQuestionInputId).val()) {
          $('#'+uniqueQuestionInputId).val(remoteParticipantAnswer);
          if (displayInstantFeedback) {
            getInstantFeedback(elearningQuestionId, $('#'+uniqueQuestionInputId).val());
          }
        }
      }
    } else if (questionType == 'WRITE_TEXT') {
      for (j = 0; j < answers.length; j++) {
        var remoteParticipantAnswer = answers[j].pA;
        var uniqueTextareaId = questions[i].utId;
        if (remoteParticipantAnswer != $('#'+uniqueTextareaId).val()) {
          $('#'+uniqueTextareaId).val(remoteParticipantAnswer);
        }
      }
    } else if (questionType == 'DRAG_ANSWER_IN_QUESTION' || questionType == 'DRAG_ANSWER_IN_ANY_QUESTION' || questionType == 'DRAG_ANSWER_IN_TEXT_HOLE') {
      for (j = 0; j < answers.length; j++) {
        var remoteParticipantAnswer = answers[j].pA;
        var displayedAnswer = answers[j].dAn;
        if (remoteParticipantAnswer != $('.elearning_question_droppable[uniqueQuestionId="'+uniqueQuestionId+'"]').find('input').attr("value")) {
          $('.elearning_question_droppable[uniqueQuestionId="'+uniqueQuestionId+'"]').find('input').attr('value', remoteParticipantAnswer);
          $('.elearning_question_droppable[uniqueQuestionId="'+uniqueQuestionId+'"]').find('.no_style_elearning_dropped_single_answer').html(displayedAnswer);
          if (displayInstantFeedback) {
            getInstantFeedback(elearningQuestionId, $('.elearning_question_droppable[uniqueQuestionId="'+uniqueQuestionId+'"]').find('input').attr('value'));
          }
        }
      }
    } else if (questionType == 'DRAG_ANSWERS_UNDER_ANY_QUESTION') {
      // Loop on all local answers and if one is not in the remote ones then remove it
      var strDroppedAnswerIds = $('.elearning_question_droppable[uniqueQuestionId="'+uniqueQuestionId+'"]').find('input.no_style_elearning_dropped_ids').attr("value");
      var droppedAnswerIds = strDroppedAnswerIds.split('$ELEARNING_ANSWERS_SEPARATOR');
      for (k = 0; k < droppedAnswerIds.length; k++) {
        if (droppedAnswerIds[k] > 0) {
          var removed = true;
          for (var j in answers) {
            if (droppedAnswerIds[k] == answers[j].id) {
              removed = false;
              break;
            }
          }
          if (removed == 1) {
            var newDroppedAnswerIds = removeAnswerId(strDroppedAnswerIds, droppedAnswerIds[k]);
            $('.elearning_question_droppable[uniqueQuestionId="'+uniqueQuestionId+'"]').find('input.no_style_elearning_dropped_ids').attr("value", newDroppedAnswerIds);
            $('.elearning_question_droppable[uniqueQuestionId="'+uniqueQuestionId+'"]').find('div.no_style_elearning_dropped_multiple_answer[elearningAnswerId="'+droppedAnswerIds[k]+'"]').remove();
            if (displayInstantFeedback) {
              getInstantFeedback(elearningQuestionId, newDroppedAnswerIds);
            }
          }
        }
      }

      for (j = 0; j < answers.length; j++) {
        var elearningAnswerId = answers[j].id;
        var uniqueAnswerId = answers[j].uId;
        var remoteParticipantAnswer = answers[j].pA;
        var displayedAnswer = answers[j].dAn;
        var strDroppedAnswerIds = $('.elearning_question_droppable[uniqueQuestionId="'+uniqueQuestionId+'"]').find('input.no_style_elearning_dropped_ids').attr("value");
        var droppedAnswerIds = strDroppedAnswerIds.split('$ELEARNING_ANSWERS_SEPARATOR');
        var alreadyDropped = false;
        for (l = 0; l < droppedAnswerIds.length; l++) {
          if (droppedAnswerIds[l] == remoteParticipantAnswer) {
            alreadyDropped = true;
          }
        }
        if (alreadyDropped != true) {
          if (strDroppedAnswerIds.length == 0) {
            strDroppedAnswerIds = remoteParticipantAnswer;
          } else {
            strDroppedAnswerIds = strDroppedAnswerIds + '$ELEARNING_ANSWERS_SEPARATOR' + remoteParticipantAnswer;
          }
          $('.elearning_question_droppable[uniqueQuestionId="'+uniqueQuestionId+'"]').find('input.no_style_elearning_dropped_ids').attr("value", strDroppedAnswerIds);
          var droppedContent = "<div class='elearning_question_answer no_style_elearning_dropped_multiple_answer' elearningAnswerId='" + elearningAnswerId + "'>" + displayedAnswer + "</div>";
          $('.elearning_question_droppable[uniqueQuestionId="'+uniqueQuestionId+'"]').find('div.no_style_elearning_dropped_multiple_answers').append(droppedContent);
          if (displayInstantFeedback) {
            getInstantFeedback(elearningQuestionId, strDroppedAnswerIds);
          }
        }
      }
    } else if (questionType == 'DRAG_ORDER_SENTENCE') {
      var displayedAnswer = '';
      var remoteOrder = '';
      for (j = 0; j < answers.length; j++) {
        var elearningAnswerId = answers[j].id;
        var uniqueAnswerId = answers[j].uId;
        if (remoteOrder.length > 0) {
          remoteOrder = remoteOrder + "$ELEARNING_ANSWERS_SEPARATOR";
        }
        remoteOrder = remoteOrder + elearningAnswerId;
        var displayedAnswer = displayedAnswer + "<span id='" + uniqueAnswerId + "' elearningAnswerId='" + elearningAnswerId + "' class='elearning_question_answer_draggable elearning_question_answer' style='vertical-align: middle;'>" + answers[j].dAn + "</span>";
      }
      // Check if the order has changed
      var localOrder = '';
      $('.no_style_elearning_question_sortable[elearningQuestionId="'+elearningQuestionId+'"]').find("span.elearning_question_answer_draggable").each(function() {
      var uniqueAnswerIds = $(this).sortable("toArray");
        var elearningAnswerId = $(this).attr('elearningAnswerId');
        if (localOrder.length > 0) {
          localOrder = localOrder + "$ELEARNING_ANSWERS_SEPARATOR";
        }
        localOrder = localOrder + elearningAnswerId;
      });
      if (remoteOrder.length > 0 && remoteOrder != localOrder) {
        $('.no_style_elearning_question_sortable[elearningQuestionId="'+elearningQuestionId+'"]').html(displayedAnswer);
        getInstantFeedback(elearningQuestionId, remoteOrder);
      }
    }
}

// Update the current page of questions
function copilotRefreshTab(response) {
  var lastExercisePageId = response.lastExercisePageId;
  if (lastExercisePageId != '$elearningExercisePageId') {
    var url = "$gElearningUrl/exercise/exercise_controller.php";
    document.exercise_form.action = url;
    document.exercise_form.elearningExercisePageId.value = lastExercisePageId;
    document.exercise_form.submit();
  }
}

// Update the participant answers
function copilotRefreshAllQuestionsAnswers(response) {
  var questionType = response.questionType;
  var questions = response.questions;

  for (i = 0; i < questions.length; i++) {
    var elearningQuestionId = questions[i].id;

    if (!allowCopilotAnswerRefresh(elearningQuestionId)) {
      continue;
    }

    var uId = questions[i].uId;
    var uiId = questions[i].uiId;
    var utId = questions[i].utId;
    var isCorrect = questions[i].isCorrect;
    var isAnswered = questions[i].isAnswered;
    var answers = questions[i].answers;

    copilotRefreshQuestionAnswers(elearningQuestionId, questionType, uId, uiId, utId, isCorrect, isAnswered, answers);
  }
}

</script>
HEREDOC;
        $str .= $strRefresh;
      }
    }

    return($str);
  }

  // Render the correction of the a page of questions
  function renderCorrection($elearningExercisePageId) {
    global $gDataPath;
    global $gDataUrl;
    global $gUtilsUrl;
    global $gImagesUserUrl;

    $this->loadLanguageTexts();

    if (!$elearningExercisePage = $this->selectById($elearningExercisePageId)) {
      return;
    }

    if (!$elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePageId)) {
      return;
    }

    $str = '';

    $name = $elearningExercisePage->getName();
    if ($name) {
      $str .= "\n<div class='elearning_exercise_page_name'>$name</div>";
    }

    $description = $elearningExercisePage->getDescription();
    if ($description) {
      $str .= "\n<div class='elearning_exercise_page_description'>$description</div>";
    }

    $participantAnswers = $this->sessionRetrieveParticipantQuestionsAnswers($elearningExercisePage);

    $strQuestions = '';
    $nbCorrectAnswers = 0;
    foreach ($elearningQuestions as $elearningQuestion) {
      $elearningQuestionId = $elearningQuestion->getId();
      if (isset($participantAnswers[$elearningQuestionId])) {
        $participantAnswer = $participantAnswers[$elearningQuestionId];
      } else {
        $participantAnswer = '';
      }
      $isCorrectlyAnswered = $this->isCorrectlyAnswered($elearningQuestionId, $participantAnswer);
      $strQuestions .= $this->renderQuestionResult($elearningExercisePage, $elearningQuestion, $participantAnswer, $isCorrectlyAnswered, false);

      if ($isCorrectlyAnswered) {
        $nbCorrectAnswers++;
      } else {
        $strQuestions .= $this->renderResultsExplanation($elearningQuestionId, $participantAnswer);
      }
    }

    if ($nbCorrectAnswers == count($elearningQuestions)) {
      $strMessage = $this->websiteText[6];
      $messageClass = 'elearning_question_right_answer';
    } else if ($nbCorrectAnswers > 0) {
      $strMessage = $this->websiteText[8];
      $messageClass = 'elearning_question_wrong_answer';
    } else {
      $strMessage = $this->websiteText[7];
      $messageClass = 'elearning_question_wrong_answer';
    }

    $str .= "\n<div class='elearning_exercise_comment'>\n<div class='$messageClass'>$strMessage</div></div>";

    $str .= $strQuestions;

    return($str);
  }

  // Get some instructions at the top of the page of questions
  function getStartInstructions($elearningExercisePage) {
    $instructions = '';

    $currentLanguageCode = $this->languageUtils->getCurrentLanguageCode();
    $instructions = $elearningExercisePage->getInstructions();
    $instructions = $this->languageUtils->getTextForLanguage($instructions, $currentLanguageCode);
    if (!$instructions) {
      $instructions = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_PAGE_INSTRUCTIONS_START");
    }

    return($instructions );
  }

  // Render some instructions at the top of the page of questions
  function renderStartInstructions($elearningExercisePage) {
    $instructions = $this->getStartInstructions($elearningExercisePage);

    if ($instructions) {
      $instructions = "<div class='elearning_exercise_page_instruction'>$instructions</div>";
    }

    return($instructions );
  }

  // Get some instructions at the bottom of the page of questions
  function getEndInstructions() {
    $instructions = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_PAGE_INSTRUCTIONS_END");

    return($instructions );
  }

  // Render some instructions at the bottom of the page of questions
  function renderEndInstructions() {
    $instructions = $this->getEndInstructions();

    if ($instructions) {
      $instructions = "<div class='elearning_exercise_page_instruction'>$instructions</div>";
    }

    return($instructions );
  }

  function getQuestionInputFieldSize($question) {
    $questionBits = $this->getQuestionBits($question);
    if (count($questionBits) > 2) {
      $inputSize = $questionBits[2];
    } else {
      $inputSize = ELEARNING_DEFAULT_INPUT_SIZE;
    }

    return($inputSize);
  }

  function getQuestionBits($question) {
    // Replace the answer location marker by the answer select list if any
    // For a text input question the marker is followed by a number of digits
    // The number of digits represents the size of the input field
    // Check if the answer location is specified
    if (strstr($question, ELEARNING_ANSWER_MCQ_MARKER)) {
      // Check if an input field size is specified
      $inputSize = 0;
      $questionBits = explode(ELEARNING_ANSWER_MCQ_MARKER, $question);
      if (count($questionBits) > 1) {
        $tmpBits = explode(' ', $questionBits[1]);
        if (count($tmpBits) > 1) {
          // The question contains ???99 and ends with some text
          $inputSize = $tmpBits[0];
        } else {
          // The question ends with ???99
          $inputSize = $questionBits[1];
          unset($questionBits[1]);
        }
      }
      if ($inputSize) {
        // Remove the specified size number from the question
        $question = str_replace(ELEARNING_ANSWER_MCQ_MARKER . $inputSize, ELEARNING_ANSWER_MCQ_MARKER, $question);
        $questionBits = explode(ELEARNING_ANSWER_MCQ_MARKER, $question);

        // Add the input field size to the array
        $questionBits[2] = $inputSize;
      } else {
        // The answer location is within the question but there is no specified input size
        $questionBits = explode(ELEARNING_ANSWER_MCQ_MARKER, $question);
      }
    } else {
      // The answer location is at the end of the question
      $questionBits = array($question);
    }

    return($questionBits);
  }

  // Render the solutions to a question
  function renderQuestionSolutions($elearningQuestionId) {
    global $gImagesUserUrl;

    $str = '';

    // Render the possible answers
    if ($elearningSolutions = $this->elearningSolutionUtils->selectByQuestion($elearningQuestionId)) {
      foreach ($elearningSolutions as $elearningSolution) {
        $elearningAnswerId = $elearningSolution->getElearningAnswer();
        if ($elearningAnswer = $this->elearningAnswerUtils->selectById($elearningAnswerId)) {
          $answer = $elearningAnswer->getAnswer();
          $str .= ' (' . "<span class='elearning_question_solution'>" . $answer . "</span>";
          $audio = $elearningAnswer->getAudio();
          if ($audio) {
            $strAudio = $this->elearningAnswerUtils->renderPlayer($audio);
            $str .= " <span style='vertical-align:bottom;'>" . $strAudio . "</span>" . ')';
          } else {
            $str .= ')';
          }
          $str = str_replace(' )', ')', $str);
        }
      }
    }

    return($str);
  }

  // Render the thumb image to a question
  function renderQuestionThumb($isCorrectlyAnswered) {
    global $gImagesUserUrl;

    if ($isCorrectlyAnswered) {
      $str = "<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_ANSWER_TRUE . "' class='no_style_image_icon' title='' alt='' />";
    } else {
      $str = "<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_ANSWER_FALSE . "' class='no_style_image_icon' title='' alt='' />";
    }

    return($str);
  }

  // Render the progression bar
  function renderProgressionBar($elearningExercisePage) {
    global $gUtilsUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    // The height and maximum width of the bar
    if ($gIsPhoneClient) {
      $barHeight = 10;
      $maxBarWidth = 120;
    } else {
      $barHeight = 10;
      $maxBarWidth = 120;
    }

    // Get the order number of the exercise page and count the number of exercise pages for the exercise
    $nb = 1;
    $totalNb = 0;
    // The order number is not the list order value used to position the exercise page
    // as it can start at any number and grow a bit big 
    // Rather it is the position index of the exercise page
    if ($elearningExercisePage) {
      $listOrder = $elearningExercisePage->getListOrder();
      $elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
      if ($elearningExercisePages = $this->selectByExerciseId($elearningExerciseId)) {
        $totalNb = count($elearningExercisePages);
        foreach ($elearningExercisePages as $wElearningExercisePage) {
          $wListOrder = $wElearningExercisePage->getListOrder();
          if ($wListOrder < $listOrder) {
            $nb++;
          }
        }
      }
    }

    // Calculate the bar width
    if ($totalNb > 0) {
      $leftWidth = round(($nb * $maxBarWidth) / $totalNb);
      $percentage = round(($nb * 100) / $totalNb);
      $rightWidth = $maxBarWidth - $leftWidth;
    } else {
      $leftWidth = 1;
      $percentage = 0;
      $rightWidth = $maxBarWidth;
    }

    // Have non null width to display an image
    if ($leftWidth == 0) {
      $leftWidth = 1;
    }
    if ($rightWidth == 0) {
      $rightWidth = 1;
    }

    $str = "\n<div class='elearning_exercise_page_progression_bar' style='vertical-align:middle; white-space:nowrap;'>";

    $str .= $this->websiteText[2] . ' ';
    $leftColor = '#66cc99';
    $leftColor = urlencode($leftColor);
    $rightColor = '#d0d0d0';
    $rightColor = urlencode($rightColor);
    $url = $gUtilsUrl .  "/printBarImage.php?color=$leftColor&amp;width=$leftWidth&amp;height=$barHeight";
    $title = $this->websiteText[0] . ' ' . $nb . ' ' . $this->websiteText[4];
    $str .= "<img style='vertical-align:middle;' src='$url' title='$title' alt='$title' />";
    $url = $gUtilsUrl .  "/printBarImage.php?color=$rightColor&amp;width=$rightWidth&amp;height=$barHeight";
    $title = ($totalNb - $nb) . ' ' . $this->websiteText[1];
    $str .= "<img style='vertical-align:middle;' src='$url' title='$title' alt='$title' />";

    $str .= "</div>";

    return($str);
  }

  // Render the image of an exercise page
  function renderImage($elearningExercisePage, $emailFormat = false) {
    global $gDataPath;
    global $gDataUrl;
    global $gUtilsUrl;
    global $gIsPhoneClient;

    $image = $elearningExercisePage->getImage();

    if (!$image) {
      return;
    }

    $imagePath = $this->imageFilePath;
    $imageUrl = $this->imageFileUrl;

    $str = '';

    if ($image && file_exists($imagePath . $image)) {
      $str .= "\n<div class='elearning_exercise_page_image'>";

      if (LibImage::isImage($imagePath . $image)) {
        // Check if the images are to be rendered in an email format
        // If so the image file path will be replaced bi 'cid' sequences
        // and no on-the-fly image resizing should take place
        if ($emailFormat) {
          $url = $imageUrl . '/' . $image;
        } else {
          if ($gIsPhoneClient && !$this->fileUploadUtils->isGifImage($imagePath . $image)) {
            // The image is created on the fly
            $width = $this->preferenceUtils->getValue("ELEARNING_PHONE_EXERCISE_PAGE_IMAGE_WIDTH");
            $filename = urlencode($imagePath . $image);
            $url = $gUtilsUrl .  "/printImage.php?filename=" . $filename
              . "&amp;width=" . $width .  "&amp;height=";
          } else {
            $url = $imageUrl . '/' . $image;
          }
        }

        $str .= "<img class='elearning_exercise_page_image_file' src='$url' title='' alt='' />";
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

}

?>
