<?

class NavbarItemUtils extends NavbarItemDB {

  var $imagePath;
  var $imageUrl;
  var $imageSize;

  var $templateUtils;

  function NavbarItemUtils() {
    $this->NavbarItemDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageSize = 200000;
    $this->imagePath = $gDataPath . 'navbar/image/';
    $this->imageUrl = $gDataUrl . '/navbar/image';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imagePath)) {
      if (!is_dir($gDataPath . 'navbar')) {
        mkdir($gDataPath . 'navbar');
      }
      mkdir($this->imagePath);
      chmod($this->imagePath, 0755);
    }
  }

  // Get the next available list order
  function getNextListOrder($navbarLanguageId) {
    $listOrder = 1;

    if ($navbarItems = $this->selectByNavbarLanguageId($navbarLanguageId)) {
      $total = count($navbarItems);
      if ($total > 0) {
        $navbarItem = $navbarItems[$total - 1];
        $listOrder = $navbarItem->getListOrder() + 1;
      }
    }

    return($listOrder);
  }

  // Get the next object
  function selectNext($id) {
    if ($navbarItem = $this->selectById($id)) {
      $listOrder = $navbarItem->getListOrder();
      $navbarLanguageId = $navbarItem->getNavbarLanguageId();
      if ($navbarItem = $this->selectByNextListOrder($navbarLanguageId, $listOrder)) {
        return($navbarItem);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($navbarItem = $this->selectById($id)) {
      $listOrder = $navbarItem->getListOrder();
      $navbarLanguageId = $navbarItem->getNavbarLanguageId();
      if ($navbarItem = $this->selectByPreviousListOrder($navbarLanguageId, $listOrder)) {
        return($navbarItem);
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
    if ($navbarItem = $this->selectById($id)) {
      $listOrder = $navbarItem->getListOrder();
      $navbarLanguageId = $navbarItem->getNavbarLanguageId();
      if ($navbarItems = $this->selectByListOrder($navbarLanguageId, $listOrder)) {
        if (($listOrder == 0) || (count($navbarItems)) > 1) {
          $this->resetListOrder($navbarLanguageId);
        }
      }
    }
  }

  // Delete an item
  function deleteItem($id) {
    $this->delete($id);
  }

  // Render
  function render($navbarItemId, $horizontal) {
    global $gJSNoStatus;
    global $gHomeUrl;
    global $gNavbarUrl;

    if (!$navbarItem = $this->selectById($navbarItemId)) {
      return;
    }

    $navbarItemId = $navbarItem->getId();
    $name = $navbarItem->getName();
    $description = $navbarItem->getDescription();
    $image = $navbarItem->getImage();
    $imageOver = $navbarItem->getImageOver();
    $url = $navbarItem->getUrl();
    $blankTarget = $navbarItem->getBlankTarget();
    $templateModelId = $navbarItem->getTemplateModelId();

    if ($description) {
      $title = $description;
    } else {
      $title = $name;
    }

    $imagePath = $this->imagePath;
    $imageUrl = $this->imageUrl;

    if ($image && is_file($imagePath . $image)) {
      if ($imageOver && is_file($imagePath . $imageOver)) {
        $strOnMouseOver = "onmouseover=\"src='$imageUrl/$imageOver'\" onmouseout=\"src='$imageUrl/$image'\"";
      } else {
        $strOnMouseOver = '';
      }

      // The image is vertically centered with the item text
      // But only if the bar is horizontal and if there is an item text
      if ($horizontal && $name) {
        $strAlign = "style='vertical-align:middle;'";
      } else {
        $strAlign = '';
      }

      $anchor = "<img class='navbar_item_image' $strAlign src='$imageUrl/$image' $strOnMouseOver title='$title' alt='' />";
    } else {
      $anchor = '';
    }

    $anchor .= $name;

    // Link to an internal web page or to a url or to the home page of the web site
    $strUrl = '';
    if ($url) {
      $strUrl = $this->templateUtils->renderPageUrl($url, $templateModelId);
    }

    if ($blankTarget) {
      $strTarget = "onclick=\"window.open(this.href, '_blank'); return(false);\"";
    } else {
      $strTarget = '';
    }

    $strNoDottedBorder = LibJavaScript::getNoDottedBorder();

    if ($strUrl) {
      $str = "<a href='$strUrl' $strNoDottedBorder $gJSNoStatus $strTarget title='$title'>$anchor</a>";
    } else {
      $str = $anchor;
    }

    return($str);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imagePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        // Check if the image is not present in the database table
        if (!$this->imageIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (@file_exists($this->imagePath . $oneFile)) {
            @unlink($this->imagePath . $oneFile);
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
