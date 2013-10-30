<?

class ElearningLessonHeadingUtils extends ElearningLessonHeadingDB {

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $preferenceUtils;
  var $elearningLessonParagraphUtils;

  function ElearningLessonHeadingUtils() {
    $this->ElearningLessonHeadingDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'elearning/heading/image/';
    $this->imageFileUrl = $gDataUrl . '/elearning/heading/image';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'elearning')) {
        mkdir($gDataPath . 'elearning');
      }
      if (!is_dir($gDataPath . 'elearning/heading')) {
        mkdir($gDataPath . 'elearning/heading');
      }
      mkdir($this->imageFilePath);
      chmod($this->imageFilePath, 0755);
    }
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imageFilePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->imageIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (@is_file($this->imageFilePath . $oneFile)) {
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
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Get the next available list order
  function getNextListOrder($elearningLessonModelId) {
    $listOrder = 1;
    $elearningLessonHeadings = $this->selectByElearningLessonModelId($elearningLessonModelId);
    $total = count($elearningLessonHeadings);
    if ($total > 0) {
      $elearningLessonHeading = $elearningLessonHeadings[$total - 1];
      $listOrder = $elearningLessonHeading->getListOrder() + 1;
    }

    return($listOrder);
  }

  // Get the next object
  function selectNext($id) {
    if ($elearningLessonHeading = $this->selectById($id)) {
      $listOrder = $elearningLessonHeading->getListOrder();
      $elearningLessonModelId = $elearningLessonHeading->getElearningLessonModelId();
      if ($elearningLessonHeading = $this->selectByNextListOrder($listOrder, $elearningLessonModelId)) {
        return($elearningLessonHeading);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($elearningLessonHeading = $this->selectById($id)) {
      $listOrder = $elearningLessonHeading->getListOrder();
      $elearningLessonModelId = $elearningLessonHeading->getElearningLessonModelId();
      if ($elearningLessonHeading = $this->selectByPreviousListOrder($listOrder, $elearningLessonModelId)) {
        return($elearningLessonHeading);
      }
    }
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
    if ($elearningLessonHeading = $this->selectById($id)) {
      $listOrder = $elearningLessonHeading->getListOrder();
      $elearningLessonModelId = $elearningLessonHeading->getElearningLessonModelId();
      if ($elearningLessonHeadings = $this->selectByListOrder($listOrder, $elearningLessonModelId)) {
        if (($listOrder == 0) || (count($elearningLessonHeadings)) > 1) {
          $this->resetListOrder($elearningLessonModelId);
        }
      }
    }
  }

  // Add a lesson heading
  function add($elearningLessonModelId, $content = '') {
    $elearningLessonHeading = new ElearningLessonHeading();
    $elearningLessonHeading->setContent($content);
    $elearningLessonHeading->setElearningLessonModelId($elearningLessonModelId);
    $nextListOrder = $this->getNextListOrder($elearningLessonModelId);
    $elearningLessonHeading->setListOrder($nextListOrder);
    $this->insert($elearningLessonHeading);
    $elearningLessonHeadingId = $this->getLastInsertId();
    return($elearningLessonHeadingId);
  }

  // Delete a lesson heading
  function deleteHeading($elearningLessonHeadingId) {
    if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonHeadingId($elearningLessonHeadingId)) {
      foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
        $elearningLessonParagraph->setElearningLessonHeadingId('');
        $this->elearningLessonParagraphUtils->update($elearningLessonParagraph);
      }
    }

    $this->delete($elearningLessonHeadingId);
  }

  // Get the url for an image
  function getImageUrl($elearningLessonHeadingId, $width) {
    global $gUtilsUrl;

    if (!$elearningLessonHeading = $this->selectById($elearningLessonHeadingId)) {
      return;
    }

    $imageUrl = '';

    $image = $elearningLessonHeading->getImage();

    $imageFilePath = $this->imageFilePath;
    $imageFileUrl = $this->imageFileUrl;

    if ($image && @file_exists($imageFilePath . $image)) {
      if (!LibImage::isGif($image)) {
        $filename = $imageFilePath . $image;

        $filename = urlencode($filename);

        $imageUrl = $gUtilsUrl . "/printImage.php?filename=" . $filename . "&amp;width=$width&amp;height=";
      } else {
        $imageUrl = "$imageFileUrl/$image";
      }
    }

    return($imageUrl);
  }

  // Render the images of a elearningLesson story
  function renderImage($elearningLessonHeadingId) {
    global $gUtilsUrl;
    global $gElearningLessonUrl;
    global $gJSNoStatus;
    global $gImagesUserUrl;
    global $gIsPhoneClient;

    if (!$elearningLessonHeading = $this->selectById($elearningLessonHeadingId)) {
      return;
    }

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("NEWS_HEADING_PHONE_IMAGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("NEWS_HEADING_IMAGE_WIDTH");
    }

    $imageUrl = $this->getImageUrl($elearningLessonHeadingId, $width);

    if ($imageUrl) {
      $strImg = "<img class='elearning_lesson_heading_image_file' src='$imageUrl' title='' alt='' />";
    } else {
      $strImg = "&nbsp;";
    }

    $str = "<div class='elearning_lesson_heading_image'>$strImg</div>";

    return($str);
  }

}

?>
