<?

class NewsStoryImageUtils extends NewsStoryImageDB {

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $newsPaperUtils;
  var $newsStoryUtils;

  function NewsStoryImageUtils() {
    parent::__construct();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'news/newsStory/image/';
    $this->imageFileUrl = $gDataUrl . '/news/newsStory/image';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'news')) {
        mkdir($gDataPath . 'news');
      }
      if (!is_dir($gDataPath . 'news/newsStory')) {
        mkdir($gDataPath . 'news/newsStory');
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
          if (is_file($this->imageFilePath . $oneFile)) {
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
        if ($result = $this->dao->selectExcerptLikeImage($image)) {
          if ($result->getRowCount() < 1) {
            if ($result = $this->dao->selectParagraphLikeImage($image)) {
              if ($result->getRowCount() < 1) {
                $isUsed = false;
              }
            }
          }
        }
      }
    }

    return($isUsed);
  }

  // Get the next available list order
  function getNextListOrder($newsStoryId) {
    $listOrder = 1;
    if ($objects = $this->selectByNewsStoryId($newsStoryId)) {
      $total = count($objects);
      if ($total > 0) {
        $object = $objects[$total - 1];
        $listOrder = $object->getListOrder() + 1;
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
    if ($newsStoryImage = $this->selectById($id)) {
      $listOrder = $newsStoryImage->getListOrder();
      $newsStoryId = $newsStoryImage->getNewsStoryId();
      if ($newsStoryImages = $this->selectByListOrder($newsStoryId, $listOrder)) {
        if (($listOrder == 0) || (count($newsStoryImages)) > 1) {
          $this->resetListOrder($newsStoryId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($newsStoryImage = $this->selectById($id)) {
      $listOrder = $newsStoryImage->getListOrder();
      $newsStoryId = $newsStoryImage->getNewsStoryId();
      if ($newsStoryImage = $this->selectByNextListOrder($newsStoryId, $listOrder)) {
        return($newsStoryImage);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($newsStoryImage = $this->selectById($id)) {
      $listOrder = $newsStoryImage->getListOrder();
      $newsStoryId = $newsStoryImage->getNewsStoryId();
      if ($newsStoryImage = $this->selectByPreviousListOrder($newsStoryId, $listOrder)) {
        return($newsStoryImage);
      }
    }
  }

  // Get the first image of the news story
  // Otherwise get the first image of the first news story of the first published newspaper
  function getFirstImage($newsStoryId = '') {
    $newsStoryImageId = '';

    if (!$newsStoryId) {
      if ($newsPapers = $this->newsPaperUtils->selectPublished()) {
        if (count($newsPapers) > 0) {
          $newsPaper = $newsPapers[0];
          $newsPaperId = $newsPaper->getId();

          if ($newsStories = $this->newsStoryUtils->selectByNewsPaper($newsPaperId)) {
            if (count($newsStories) > 0) {
              $newsStory = $newsStories[0];
              $newsStoryId = $newsStory->getId();
            }
          }
        }
      }
    }

    if ($newsStoryImages = $this->selectByNewsStoryId($newsStoryId)) {
      if (count($newsStoryImages) > 0) {
        $newsStoryImage = $newsStoryImages[0];
        $newsStoryImageId = $newsStoryImage->getId();
      }
    }

    return($newsStoryImageId);
  }

  // Get the url for an image
  function getImageUrl($newsStoryImageId, $width) {
    global $gUtilsUrl;

    if (!$newsStoryImage = $this->selectById($newsStoryImageId)) {
      return;
    }

    $imageUrl = '';

    $image = $newsStoryImage->getImage();

    $imageFilePath = $this->imageFilePath;
    $imageFileUrl = $this->imageFileUrl;

    if ($image && file_exists($imageFilePath . $image)) {
      if (!LibImage::isGif($image)) {
        $filename = $imageFilePath . $image;

        $imageLengthIsHeight = $this->newsStoryUtils->imageLengthIsHeight();
        if ($imageLengthIsHeight) {
          $width = LibImage::getWidthFromHeight($filename, $width);
        }

        $filename = urlencode($filename);

        $imageUrl = $gUtilsUrl . "/printImage.php?filename=" . $filename . "&amp;width=$width&amp;height=";
      } else {
        $imageUrl = "$imageFileUrl/$image";
      }
    }

    return($imageUrl);
  }

}

?>
