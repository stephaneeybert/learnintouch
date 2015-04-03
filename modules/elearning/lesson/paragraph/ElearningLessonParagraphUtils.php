<?

class ElearningLessonParagraphUtils extends ElearningLessonParagraphDB {

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $audioFileSize;
  var $audioFilePath;
  var $audioFileUrl;

  var $languageUtils;
  var $preferenceUtils;
  var $playerUtils;
  var $elearningLessonUtils;
  var $elearningExerciseUtils;
  var $fileUploadUtils;

  function ElearningLessonParagraphUtils() {
    $this->ElearningLessonParagraphDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'elearning/lesson/paragraph/image/';
    $this->imageFileUrl = $gDataUrl . '/elearning/lesson/paragraph/image';

    $this->audioFileSize = 4096000;
    $this->audioFilePath = $gDataPath . 'elearning/lesson/paragraph/audio/';
    $this->audioFileUrl = $gDataUrl . '/elearning/lesson/paragraph/audio';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'elearning')) {
        mkdir($gDataPath . 'elearning');
      }
      if (!is_dir($gDataPath . 'elearning/lesson')) {
        mkdir($gDataPath . 'elearning/lesson');
      }
      if (!is_dir($gDataPath . 'elearning/lesson/paragraph')) {
        mkdir($gDataPath . 'elearning/lesson/paragraph');
      }
      mkdir($this->imageFilePath);
      chmod($this->imageFilePath, 0755);
    }

    if (!is_dir($this->audioFilePath)) {
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
          if (@file_exists($this->imageFilePath . $oneFile)) {
            @unlink($this->imageFilePath . $oneFile);
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
        if ($result = $this->dao->selectBodyLikeImage($image)) {
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
          if (@file_exists($this->audioFilePath . $oneFile)) {
            @unlink($this->audioFilePath . $oneFile);
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
  function getNextListOrder($elearningLessonId, $elearningLessonHeadingId) {
    $listOrder = 1;
    if ($elearningLessonParagraphs = $this->selectByLessonIdAndLessonHeadingId($elearningLessonId, $elearningLessonHeadingId)) {
      $total = count($elearningLessonParagraphs);
      if ($total > 0) {
        $elearningLessonParagraph = $elearningLessonParagraphs[$total - 1];
        $listOrder = $elearningLessonParagraph->getListOrder() + 1;
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
    if ($elearningLessonParagraph = $this->selectById($id)) {
      $listOrder = $elearningLessonParagraph->getListOrder();
      $elearningLessonId = $elearningLessonParagraph->getElearningLessonId();
      $elearningLessonHeadingId = $elearningLessonParagraph->getElearningLessonHeadingId();
      if ($elearningLessonParagraphs = $this->selectByListOrder($elearningLessonId, $elearningLessonHeadingId, $listOrder)) {
        if (($listOrder == 0) || (count($elearningLessonParagraphs)) > 1) {
          $this->resetListOrder($elearningLessonId, $elearningLessonHeadingId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($elearningLessonParagraph = $this->selectById($id)) {
      $listOrder = $elearningLessonParagraph->getListOrder();
      $elearningLessonId = $elearningLessonParagraph->getElearningLessonId();
      $elearningLessonHeadingId = $elearningLessonParagraph->getElearningLessonHeadingId();
      if ($elearningLessonParagraph = $this->selectByNextListOrder($elearningLessonId, $elearningLessonHeadingId, $listOrder)) {
        return($elearningLessonParagraph);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($elearningLessonParagraph = $this->selectById($id)) {
      $listOrder = $elearningLessonParagraph->getListOrder();
      $elearningLessonId = $elearningLessonParagraph->getElearningLessonId();
      $elearningLessonHeadingId = $elearningLessonParagraph->getElearningLessonHeadingId();
      if ($elearningLessonParagraph = $this->selectByPreviousListOrder($elearningLessonId, $elearningLessonHeadingId, $listOrder)) {
        return($elearningLessonParagraph);
      }
    }
  }

  // Add a paragraph to a lesson
  function add($elearningLessonId, $elearningLessonHeadingId = '') {
    $elearningLessonParagraph = new ElearningLessonParagraph();
    $elearningLessonParagraph->setElearningLessonId($elearningLessonId);
    $elearningLessonParagraph->setElearningLessonHeadingId($elearningLessonHeadingId);
    $this->insert($elearningLessonParagraph);
  }


  // Duplicate a lesson paragraph
  function duplicate($elearningLessonParagraphId, $elearningLessonId) {
    if ($elearningLessonParagraph = $this->selectById($elearningLessonParagraphId)) {
      $elearningLessonParagraph->setElearningLessonId($elearningLessonId);
      $this->insert($elearningLessonParagraph);
    }
  }

  // Get the first paragraph of a lesson
  // Otherwise get the first paragraph of any lesson
  function getFirstParagraph($elearningLessonId = '') {
    $elearningLessonParagraphId = '';

    if (!$elearningLessonId) {
      if ($elearningLessons = $this->elearningLessonUtils->selectAll()) {
        if (count($elearningLessons) > 0) {
          $elearningLesson = $elearningLessons[0];
          $elearningLessonId = $elearningLesson->getId();
        }
      }
    }

    if ($elearningLessonParagraphs = $this->selectByElearningLessonId($elearningLessonId)) {
      if (count($elearningLessonParagraphs) > 0) {
        $elearningLessonParagraph = $elearningLessonParagraphs[0];
        $elearningLessonParagraphId = $elearningLessonParagraph->getId();
      }
    }

    return($elearningLessonParagraphId);
  }

  // Get the previous paragraph
  function getPreviousParagraphId($elearningLessonParagraphId) {
    $previousParagraphId = '';

    if ($elearningLessonParagraph = $this->selectById($elearningLessonParagraphId)) {
      $elearningLessonId = $elearningLessonParagraph->getElearningLessonId();

      if ($elearningLessonParagraphs = $this->selectByElearningLessonId($elearningLessonId)) {
        foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
          $wElearningLessonParagraphId = $elearningLessonParagraph->getId();
          if ($wElearningLessonParagraphId == $elearningLessonParagraphId) {
            return($previousParagraphId);
          }
          $previousParagraphId = $wElearningLessonParagraphId;
        }
      }
    }
  }

  // Get the next paragraph
  function getNextParagraphId($elearningLessonParagraphId) {
    $nextParagraphId = '';

    if ($elearningLessonParagraph = $this->selectById($elearningLessonParagraphId)) {
      $elearningLessonId = $elearningLessonParagraph->getElearningLessonId();

      if ($elearningLessonParagraphs = $this->selectByElearningLessonId($elearningLessonId)) {
        foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
          $wElearningLessonParagraphId = $elearningLessonParagraph->getId();
          if ($nextParagraphId == $elearningLessonParagraphId) {
            return($wElearningLessonParagraphId);
          }
          $nextParagraphId = $wElearningLessonParagraphId;
        }
      }
    }
  }

  // Check if the content was created by the user
  function createdByUser($elearningLessonParagraphId, $userId) {
    $byUser = false;

    if ($elearningLessonParagraph = $this->selectById($elearningLessonParagraphId)) {
      $elearningLessonId = $elearningLessonParagraph->getElearningLessonId();
      $byUser = $this->elearningLessonUtils->createdByUser($elearningLessonId, $userId);
    }

    return($byUser);
  }

  // Delete a paragraph from a lesson
  function deleteParagraph($elearningLessonParagraphId) {
    $this->delete($elearningLessonParagraphId);
  }

  // Render the image of the lesson paragraph
  function renderImage($elearningLessonParagraphId, $emailFormat = false) {
    global $gDataPath;
    global $gDataUrl;
    global $gUtilsUrl;
    global $gIsPhoneClient;

    if (!$elearningLessonParagraph = $this->selectById($elearningLessonParagraphId)) {
      return;
    }

    $image = $elearningLessonParagraph->getImage();

    $imagePath = $this->imageFilePath;
    $imageUrl = $this->imageFileUrl;

    // Resize the image to the following width
    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("ELEARNING_PHONE_EXERCISE_IMAGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_IMAGE_WIDTH");
    }

    $str = '';

    if ($image && @file_exists($imagePath . $image)) {
      $str .= "<div class='elearning_lesson_paragraph_image'>";

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
            $url = $gUtilsUrl . "/printImage.php?filename=" . $filename
              . "&amp;width=" . $width . "&amp;height=";
          } else {
            $url = $imageUrl . '/' . $image;
          }
        }

        $str .= "<img class='elearning_lesson_paragraph_image_file' src='$url' title='' alt='' />";
      } else {
        $libFlash = new LibFlash();
        if ($libFlash->isFlashFile($imageFile)) {
          $str .= $libFlash->renderObject("$imageUrl/$image");
        }
      }
      $str .= "</div>";
    }

    return($str);
  }

  // Render the player
  function renderPlayer($audio) {
    global $gDataUrl;
    global $gDataPath;

    $str = '';

    if ($audio) {
      $str .= "<div class='elearning_lesson_paragraph_player'>";

      $autoStartAudioPlayer = $this->elearningExerciseUtils->autoStartAudioPlayer();

      $this->playerUtils->setAutostart($autoStartAudioPlayer);

      if (@is_file($gDataPath . "elearning/lesson/paragraph/audio/$audio")) {
        if ($this->elearningExerciseUtils->displayDownloadAudioFileIcon()) {
          $str .= ' ' . $this->renderDownload($audio);
        }
        $str = $this->playerUtils->renderPlayer("$gDataUrl/elearning/lesson/paragraph/audio/$audio");
      }

      $str .= "</div>";
    }

    return($str);
  }

  // Render the download link
  function renderDownload($audio) {
    global $gDataUrl;
    global $gDataPath;

    $str = '';

    if ($audio) {
      if (@is_file($gDataPath . "elearning/lesson/paragraph/audio/$audio")) {
        $str = $this->playerUtils->renderDownload($gDataPath . "elearning/lesson/paragraph/audio/$audio");
      }
    }

    return($str);
  }

}

?>
