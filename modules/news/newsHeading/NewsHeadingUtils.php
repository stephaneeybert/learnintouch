<?

class NewsHeadingUtils extends NewsHeadingDB {

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $preferenceUtils;
  var $newsHeadingUtils;
  var $newsStoryUtils;

  function NewsHeadingUtils() {
    $this->NewsHeadingDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'news/newsHeading/image/';
    $this->imageFileUrl = $gDataUrl . '/news/newsHeading/image';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'news')) {
        mkdir($gDataPath . 'news');
      }
      if (!is_dir($gDataPath . 'news/newsHeading')) {
        mkdir($gDataPath . 'news/newsHeading');
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
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Get the next available list order
  function getNextListOrder($newsPublicationId) {
    $listOrder = 1;
    $newsHeadings = $this->newsHeadingUtils->selectByNewsPublicationId($newsPublicationId);
    $total = count($newsHeadings);
    if ($total > 0) {
      $newsHeading = $newsHeadings[$total - 1];
      $listOrder = $newsHeading->getListOrder() + 1;
    }

    return($listOrder);
  }

  // Get the next object
  function selectNext($id) {
    if ($newsHeading = $this->selectById($id)) {
      $listOrder = $newsHeading->getListOrder();
      $newsPublicationId = $newsHeading->getNewsPublicationId();
      if ($newsHeading = $this->selectByNextListOrder($listOrder, $newsPublicationId)) {
        return($newsHeading);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($newsHeading = $this->selectById($id)) {
      $listOrder = $newsHeading->getListOrder();
      $newsPublicationId = $newsHeading->getNewsPublicationId();
      if ($newsHeading = $this->selectByPreviousListOrder($listOrder, $newsPublicationId)) {
        return($newsHeading);
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
    if ($newsHeading = $this->selectById($id)) {
      $listOrder = $newsHeading->getListOrder();
      $newsPublicationId = $newsHeading->getNewsPublicationId();
      if ($newsHeadings = $this->selectByListOrder($listOrder, $newsPublicationId)) {
        if (($listOrder == 0) || (count($newsHeadings)) > 1) {
          $this->resetListOrder($newsPublicationId);
        }
      }
    }
  }

  // Detach a news heading from its news publication
  function detachFromNewsPublication($newsHeadingId) {
    if ($newsHeading = $this->selectById($newsHeadingId)) {
      $newsHeading->setNewsPublicationId('');
      $this->update($newsHeading);
    }
  }

  // Get the url for an image
  function getImageUrl($newsHeadingId) {
    if (!$newsHeading = $this->selectById($newsHeadingId)) {
      return;
    }

    $imageUrl = '';

    $image = $newsHeading->getImage();

    $imageFilePath = $this->imageFilePath;
    $imageFileUrl = $this->imageFileUrl;

    if ($image && file_exists($imageFilePath . $image)) {
      $imageUrl = "$imageFileUrl/$image";
    }

    return($imageUrl);
  }

  // Get the width of an image
  function getImageWidth() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("NEWS_HEADING_PHONE_IMAGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("NEWS_HEADING_IMAGE_WIDTH");
    }

    return($width);
  }

  // Render the images of a news story
  function renderImage($newsHeadingId) {
    global $gNewsUrl;
    global $gJSNoStatus;
    global $gImagesUserUrl;

    $str = '';

    if (!$newsHeading = $this->selectById($newsHeadingId)) {
      return;
    }

    $imageUrl = $this->getImageUrl($newsHeadingId);

    if ($imageUrl) {
      $str .= "<div class='newspaper_heading_image' style='float:left;'>"
        . "<img class='newspaper_heading_image_file' src='$imageUrl' title='' alt='' />"
        . "</div>";
    }

    return($str);
  }

}

?>
