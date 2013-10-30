<?

class ShopItemImageUtils extends ShopItemImageDB {

  var $websiteText;

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $languageUtils;
  var $preferenceUtils;
  var $shopItemUtils;

  function ShopItemImageUtils() {
    $this->ShopItemImageDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'shop/image/';
    $this->imageFileUrl = $gDataUrl . '/shop/image';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'shop')) {
        mkdir($gDataPath . 'shop');
      }
      mkdir($this->imageFilePath);
      chmod($this->imageFilePath, 0755);
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
  function getNextListOrder($shopItemId) {
    $listOrder = 1;
    if ($objects = $this->selectByShopItemId($shopItemId)) {
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
    if ($shopItemImage = $this->selectById($id)) {
      $listOrder = $shopItemImage->getListOrder();
      $shopItemId = $shopItemImage->getShopItemId();
      if ($shopItemImages = $this->selectByListOrder($shopItemId, $listOrder)) {
        if (($listOrder == 0) || (count($shopItemImages)) > 1) {
          $this->resetListOrder($shopItemId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($shopItemImage = $this->selectById($id)) {
      $listOrder = $shopItemImage->getListOrder();
      $shopItemImageId = $shopItemImage->getShopItemId();
      if ($shopItemImage = $this->selectByNextListOrder($shopItemImageId, $listOrder)) {
        return($shopItemImage);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($shopItemImage = $this->selectById($id)) {
      $listOrder = $shopItemImage->getListOrder();
      $shopItemImageId = $shopItemImage->getShopItemId();
      if ($shopItemImage = $this->selectByPreviousListOrder($shopItemImageId, $listOrder)) {
        return($shopItemImage);
      }
    }
  }

}

?>
