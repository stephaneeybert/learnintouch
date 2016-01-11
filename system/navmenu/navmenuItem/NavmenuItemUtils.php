<?

class NavmenuItemUtils extends NavmenuItemDB {

  var $imagePath;
  var $imageUrl;
  var $imageSize;

  function NavmenuItemUtils() {
    $this->NavmenuItemDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageSize = 50000;
    $this->imagePath = $gDataPath . 'navmenu/image/';
    $this->imageUrl = $gDataUrl . '/navmenu/image';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imagePath)) {
      if (!is_dir($gDataPath . 'navmenu')) {
        mkdir($gDataPath . 'navmenu');
      }
      mkdir($this->imagePath);
      chmod($this->imagePath, 0755);
    }
  }

  // Add a separator item
  function addSeparator($parentNavmenuItemId) {
    $navmenuItem = new NavmenuItem();
    $navmenuItem->setName('NAVMENU_SEPARATOR');

    // Get the next list order
    $listOrder = $this->getNextListOrder($parentNavmenuItemId);
    $navmenuItem->setListOrder($listOrder);

    $navmenuItem->setParentNavmenuItemId($parentNavmenuItemId);
    $this->insert($navmenuItem);
  }

  // Get the next available list order
  function getNextListOrder($parentNavmenuItemId) {
    $listOrder = 1;

    if ($navmenuItems = $this->selectByParentNavmenuItemId($parentNavmenuItemId)) {
      $total = count($navmenuItems);
      if ($total > 0) {
        $navmenuItem = $navmenuItems[$total - 1];
        $listOrder = $navmenuItem->getListOrder() + 1;
      }
    }

    return($listOrder);
  }

  // Get the next object
  function selectNext($id) {
    if ($navmenuItem = $this->selectById($id)) {
      $listOrder = $navmenuItem->getListOrder();
      $parentNavmenuItemId = $navmenuItem->getParentNavmenuItemId();
      if ($navmenuItem = $this->selectByNextListOrder($parentNavmenuItemId, $listOrder)) {
        return($navmenuItem);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($navmenuItem = $this->selectById($id)) {
      $listOrder = $navmenuItem->getListOrder();
      $parentNavmenuItemId = $navmenuItem->getParentNavmenuItemId();
      if ($navmenuItem = $this->selectByPreviousListOrder($parentNavmenuItemId, $listOrder)) {
        return($navmenuItem);
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
    if ($navmenuItem = $this->selectById($id)) {
      $listOrder = $navmenuItem->getListOrder();
      $parentNavmenuItemId = $navmenuItem->getParentNavmenuItemId();
      if ($navmenuItems = $this->selectByListOrder($parentNavmenuItemId, $listOrder)) {
        if (($listOrder == 0) || (count($navmenuItems)) > 1) {
          $this->resetListOrder($parentNavmenuItemId);
        }
      }
    }
  }

  // List the items of a language
  function listItems($parentNavmenuItemId, $level = 0) {
    $listItems = array();

    if ($navmenuItems = $this->selectByParentNavmenuItemId($parentNavmenuItemId)) {
      foreach ($navmenuItems as $navmenuItem) {
        $navmenuItemId = $navmenuItem->getId();
        array_push($listItems, array($navmenuItemId, $level));
        $nextLevel = $level + 1;
        $listItems = array_merge($listItems, $this->listItems($navmenuItemId, $nextLevel));
      }
    }

    return($listItems);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imagePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        // Check if the image is not present in the database table
        if (!$this->imageIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (file_exists($this->imagePath . $oneFile)) {
            unlink($this->imagePath . $oneFile);
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
        if ($result = $this->dao->selectByImageOver($image)) {
          if ($result->getRowCount() < 1) {
            $isUsed = false;
          }
        }
      }
    }

    return($isUsed);
  }

}

?>
