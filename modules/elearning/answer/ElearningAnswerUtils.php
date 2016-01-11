<?

class ElearningAnswerUtils extends ElearningAnswerDB {

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $audioFileSize;
  var $audioFilePath;
  var $audioFileUrl;

  var $preferenceUtils;
  var $playerUtils;
  var $elearningQuestionUtils;
  var $elearningQuestionResultUtils;
  var $elearningResultUtils;
  var $elearningSolutionUtils;
  var $fileUploadUtils;

  function ElearningAnswerUtils() {
    $this->ElearningAnswerDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'elearning/answer/image/';
    $this->imageFileUrl = $gDataUrl . '/elearning/answer/image';

    $this->audioFileSize = 4096000;
    $this->audioFilePath = $gDataPath . 'elearning/answer/audio/';
    $this->audioFileUrl = $gDataUrl . '/elearning/answer/audio';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'elearning')) {
        mkdir($gDataPath . 'elearning');
      }
      if (!is_dir($gDataPath . 'elearning/answer')) {
        mkdir($gDataPath . 'elearning/answer');
      }
      mkdir($this->imageFilePath);
      chmod($this->imageFilePath, 0755);
    }

    if (!is_dir($this->audioFilePath)) {
      if (!is_dir($gDataPath . 'elearning')) {
        mkdir($gDataPath . 'elearning');
      }
      if (!is_dir($gDataPath . 'elearning/answer')) {
        mkdir($gDataPath . 'elearning/answer');
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
  function getNextListOrder($elearningQuestionId) {
    $listOrder = 1;
    if ($elearningAnswers = $this->selectByQuestion($elearningQuestionId)) {
      $total = count($elearningAnswers);
      if ($total > 0) {
        $elearningAnswer = $elearningAnswers[$total - 1];
        $listOrder = $elearningAnswer->getListOrder() + 1;
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
    if ($elearningAnswer = $this->selectById($id)) {
      $listOrder = $elearningAnswer->getListOrder();
      $elearningQuestionId = $elearningAnswer->getElearningQuestion();
      if ($elearningAnswers = $this->selectByListOrder($elearningQuestionId, $listOrder)) {
        if (($listOrder == 0) || (count($elearningAnswers)) > 1) {
          $this->resetListOrder($elearningQuestionId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($elearningAnswer = $this->selectById($id)) {
      $listOrder = $elearningAnswer->getListOrder();
      $elearningQuestionId = $elearningAnswer->getElearningQuestion();
      if ($elearningAnswer = $this->selectByNextListOrder($id, $elearningQuestionId, $listOrder)) {
        return($elearningAnswer);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($elearningAnswer = $this->selectById($id)) {
      $listOrder = $elearningAnswer->getListOrder();
      $elearningQuestionId = $elearningAnswer->getElearningQuestion();
      if ($elearningAnswer = $this->selectByPreviousListOrder($id, $elearningQuestionId, $listOrder)) {
        return($elearningAnswer);
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
    $elearningQuestionId = $targetObject->getElearningQuestion();
    $currentListOrder = $currentObject->getListOrder();

    if ($objects = $this->selectByQuestion($elearningQuestionId)) {
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
    $currentObject->setElearningQuestion($targetObject->getElearningQuestion());
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
    $elearningQuestionId = $targetObject->getElearningQuestion();
    $currentListOrder = $currentObject->getListOrder();

    if ($objects = $this->selectByQuestion($elearningQuestionId)) {
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
    $currentObject->setElearningQuestion($targetObject->getElearningQuestion());
    $this->update($currentObject);

    return(true);
  }

  // Specify the answer as NOT a solution
  function specifyAsNotSolution($elearningAnswerId) {
    // Get the question of the answer
    if ($elearningAnswer = $this->selectById($elearningAnswerId)) {
      $elearningQuestionId = $elearningAnswer->getElearningQuestion();

      $multipleAnswers = $this->preferenceUtils->getValue("ELEARNING_MULTIPLE_ANSWERS");

      // Check if multiple correct answers are possible for a question
      if ($multipleAnswers) {
        // Check if the answer is already specified as a correct solution
        if ($elearningSolution = $this->elearningSolutionUtils->selectByQuestionAndAnswer($elearningQuestionId, $elearningAnswerId)) {
          $elearningSolutionId = $elearningSolution->getId();
          $this->elearningSolutionUtils->delete($elearningSolutionId);
        }
      }
    }
  }

  // Specify the answer as a solution
  function specifyAsSolution($elearningAnswerId) {
    // Get the question of the answer
    if ($elearningAnswer = $this->selectById($elearningAnswerId)) {
      $elearningQuestionId = $elearningAnswer->getElearningQuestion();

      $multipleAnswers = $this->preferenceUtils->getValue("ELEARNING_MULTIPLE_ANSWERS");

      // Check if multiple correct answers are possible for a question
      if ($multipleAnswers) {
        // Check if the answer is not yet specified as a correct solution
        if (!$elearningSolution = $this->elearningSolutionUtils->selectByQuestionAndAnswer($elearningQuestionId, $elearningAnswerId)) {
          $elearningSolution = new ElearningSolution();
          $elearningSolution->setElearningAnswer($elearningAnswerId);
          $elearningSolution->setElearningQuestion($elearningQuestionId);
          $this->elearningSolutionUtils->insert($elearningSolution);
        }
      } else {
        // If some solutions already exists then delete them
        // This is to delete multiple solutions if the questions were to be
        // specified back as having only one possible solution
        if ($elearningSolutions = $this->elearningSolutionUtils->selectByQuestion($elearningQuestionId)) {
          foreach ($elearningSolutions as $elearningSolution) {
            $elearningSolutionId = $elearningSolution->getId();
            $this->elearningSolutionUtils->delete($elearningSolutionId);
          }
        }
        // And create it
        $elearningSolution = new ElearningSolution();
        $elearningSolution->setElearningAnswer($elearningAnswerId);
        $elearningSolution->setElearningQuestion($elearningQuestionId);
        $this->elearningSolutionUtils->insert($elearningSolution);
      }
    }
  }

  // Check if the content was created by the user
  function createdByUser($elearningAnswerId, $userId) {
    $byUser = false;

    if ($elearningAnswer = $this->selectById($elearningAnswerId)) {
      $elearningQuestionId = $elearningAnswer->getElearningQuestion();
      $byUser = $this->elearningQuestionUtils->createdByUser($elearningQuestionId, $userId);
    }

    return($byUser);
  }

  // Render the download link
  function renderDownload($audio) {
    global $gDataUrl;
    global $gDataPath;

    $str = '';

    if ($audio) {
      if (is_file($gDataPath . "elearning/answer/audio/$audio")) {
        $str = $this->playerUtils->renderDownload($gDataPath . "elearning/answer/audio/$audio");
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

      if (is_file($gDataPath . "elearning/answer/audio/$audio")) {
        $audioDownload = $this->preferenceUtils->getValue("ELEARNING_DISPLAY_AUDIO_DOWNLOAD");
        if ($audioDownload) {
          $str .= $this->playerUtils->renderDownload($gDataPath . "elearning/answer/audio/$audio") . ' ';
        }
        $str .= $this->playerUtils->renderPlayer("$gDataUrl/elearning/answer/audio/$audio");
      }
    }

    return($str);
  }

  // Render the image of an answer
  function renderImage($elearningAnswerId, $emailFormat = false) {
    global $gDataPath;
    global $gDataUrl;
    global $gUtilsUrl;
    global $gIsPhoneClient;

    if (!$elearningAnswer = $this->selectById($elearningAnswerId)) {
      return;
    }

    $image = $elearningAnswer->getImage();

    $imagePath = $this->imageFilePath;
    $imageUrl = $this->imageFileUrl;

    // Resize the image to the following width
    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("ELEARNING_PHONE_QUESTION_IMAGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("ELEARNING_QUESTION_IMAGE_WIDTH");
    }

    $str = '';

    if ($image && file_exists($imagePath . $image)) {
      $str .= "\n<span class='elearning_question_answer_image'>";

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

        $str .= "<img class='elearning_question_answer_image_file' src='$url' title='' alt='' $strWidth style='border-width:0px; vertical-align:middle;' />";
      } else {
        $libFlash = new LibFlash();
        if ($libFlash->isFlashFile($image)) {
          $str .= $libFlash->renderObject("$imageUrl/$image");
        }
      }

      $str .= "</span>";
    }

    return($str);
  }

  // Check if the answer is a possible solution
  function isASolution($elearningQuestion, $participantAnswer) {
    $isASolution = false;

    $elearningQuestionId = $elearningQuestion->getId();
    if (!$this->elearningQuestionUtils->isWrittenAnswer($elearningQuestion)) {
      if ($elearningSolutions = $this->elearningSolutionUtils->selectByQuestion($elearningQuestionId)) {
        foreach ($elearningSolutions as $elearningSolution) {
          $solutionElearningAnswerId = $elearningSolution->getElearningAnswer();
          if ($participantAnswer == $solutionElearningAnswerId) {
            $isASolution = true;
          }
        }
      }
    } else {
      // Otherwise it has been typed in
      if ($elearningSolutions = $this->elearningSolutionUtils->selectByQuestion($elearningQuestionId)) {
        foreach ($elearningSolutions as $elearningSolution) {
          $solutionElearningAnswerId = $elearningSolution->getElearningAnswer();
          if ($elearningAnswers = $this->selectByQuestion($elearningQuestionId)) {
            foreach ($elearningAnswers as $elearningAnswer) {
              $elearningAnswerId = $elearningAnswer->getId();
              if ($elearningAnswerId == $solutionElearningAnswerId) {
                $answer = $elearningAnswer->getAnswer();

                // The answer may contain a lexicon entry
                $answer = LibString::stripTags($answer);
                $participantAnswer = LibString::stripTags($participantAnswer);

                // Not to be mean to the participant...
                $answer = strtolower(LibString::stripNonTextChar(LibString::trim($answer, 0)));
                $participantAnswer = strtolower(LibString::stripNonTextChar(LibString::trim($participantAnswer, 0)));

                if ($participantAnswer == $answer) {
                  $isASolution = true;
                }
              }
            }
          }
        }
      }
      // One additional check needs to be made here
      // If the question type was changed from a choose one to a type in one,
      // right or long after, the participant answer was saved in the results,
      // then the answer would be deemed false when it may have been true before the
      // question type was changed
      // Indeed, if the answer is a numeric one then it could have been the id of a chosen answer
      // Thus it is checked for a possible match against a chosen answer
      if (!$isASolution && is_numeric($participantAnswer)) {
        foreach ($elearningSolutions as $elearningSolution) {
          $solutionElearningAnswerId = $elearningSolution->getElearningAnswer();
          if ($participantAnswer == $solutionElearningAnswerId) {
            $isASolution = true;
          }
        }
      }
    }

    return($isASolution);
  }

  // Check if an answer has some results
  function answerHasResults($elearningAnswerId) {
    $hasResults = false;

    if ($elearningAnswer = $this->selectById($elearningAnswerId)) {
      $elearningQuestionId = $elearningAnswer->getElearningQuestion();
      if (count($this->elearningQuestionResultUtils->selectByQuestionAndAnswerId($elearningQuestionId, $elearningAnswerId) > 0)) {
        $hasResults = true;
      }
    }

    return($hasResults);
  }

  // Delete an answer
  function deleteAnswer($elearningAnswerId) {
    $this->elearningResultUtils->deleteAnswerResults($elearningAnswerId);

    if ($elearningSolution = $this->elearningSolutionUtils->selectByAnswer($elearningAnswerId)) {
      $this->elearningSolutionUtils->delete($elearningSolution->getId());
    }

    $this->delete($elearningAnswerId);
  }

  // Check if an answer is of listening sort
  function isListeningContent($elearningAnswerId) {
    $isListening = false;

    if ($elearningAnswer = $this->selectById($elearningAnswerId)) {
      $audio = $elearningAnswer->getAudio();
      if ($audio) {
        $isListening = true;
      }
    }

    return($isListening);
  }

}

?>
