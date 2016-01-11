<?

class LinkUtils extends LinkDB {

  var $mlText;

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $currentCategoryId;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;

  function LinkUtils() {
    $this->LinkDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'link/image/';
    $this->imageFileUrl = $gDataUrl . '/link/image';

    $this->currentCategoryId = "linkCurrentCategoryId";
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'link')) {
        mkdir($gDataPath . 'link');
      }
      mkdir($this->imageFilePath);
      chmod($this->imageFilePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "LINK_DEFAULT_WIDTH" =>
      array($this->mlText[0], $this->mlText[1], PREFERENCE_TYPE_TEXT, 100),
        "LINK_PHONE_DEFAULT_WIDTH" =>
        array($this->mlText[4], $this->mlText[5], PREFERENCE_TYPE_TEXT, 100),
          "LINK_CYCLE_WIDTH_TEMPLATE" =>
          array($this->mlText[8], $this->mlText[9], PREFERENCE_TYPE_TEXT, 100),
            "LINK_CYCLE_WIDTH_PAGE" =>
            array($this->mlText[10], $this->mlText[11], PREFERENCE_TYPE_TEXT, 100),
              "LINK_CYCLE_TIMEOUT" =>
              array($this->mlText[12], $this->mlText[13], PREFERENCE_TYPE_RANGE, array(1, 60, 10)),
                "LINK_DISPLAY_ALL" =>
                array($this->mlText[2], $this->mlText[3], PREFERENCE_TYPE_BOOLEAN, ''),
                  "LINK_HIDE_SELECTOR" =>
                  array($this->mlText[6], $this->mlText[7], PREFERENCE_TYPE_BOOLEAN, ''),
                  );
  }

  // Get the width of the image
  function getImageWidth() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("LINK_PHONE_DEFAULT_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("LINK_DEFAULT_WIDTH");
    }

    return($width);
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

  // Delete a link
  function deleteLink($linkId) {
    $this->delete($linkId);
  }

  // Get the next available list order
  function getNextListOrder($categoryId) {
    $listOrder = 1;
    if ($objects = $this->selectByCategoryId($categoryId)) {
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
    if ($link = $this->selectById($id)) {
      $listOrder = $link->getListOrder();
      $categoryId = $link->getCategoryId();
      if ($links = $this->selectByListOrder($categoryId, $listOrder)) {
        if (($listOrder == 0) || (count($links)) > 1) {
          $this->resetListOrder($categoryId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($link = $this->selectById($id)) {
      $listOrder = $link->getListOrder();
      $categoryId = $link->getCategoryId();
      if ($link = $this->selectByNextListOrder($categoryId, $listOrder)) {
        return($link);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($link = $this->selectById($id)) {
      $listOrder = $link->getListOrder();
      $categoryId = $link->getCategoryId();
      if ($link = $this->selectByPreviousListOrder($categoryId, $listOrder)) {
        return($link);
      }
    }
  }

}

?>
