<?

class ShopCategoryUtils extends ShopCategoryDB {

  var $mlText;
  var $websiteText;

  var $languageUtils;
  var $flashUtils;
  var $templateUtils;

  function __construct() {
    parent::__construct();
  }

  function init() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  // Get the next available list order
  function getNextListOrder($parentCategoryId) {
    $listOrder = 1;
    if ($objects = $this->selectByParentCategoryId($parentCategoryId)) {
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
    if ($shopCategory = $this->selectById($id)) {
      $listOrder = $shopCategory->getListOrder();
      $parentCategoryId = $shopCategory->getParentCategoryId();
      if ($shopCategories = $this->selectByListOrder($parentCategoryId, $listOrder)) {
        if (($listOrder == 0) || (count($shopCategories)) > 1) {
          $this->resetListOrder($parentCategoryId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($shopCategory = $this->selectById($id)) {
      $listOrder = $shopCategory->getListOrder();
      $parentCategoryId = $shopCategory->getParentCategoryId();
      if ($shopCategory = $this->selectByNextListOrder($parentCategoryId, $listOrder)) {
        return($shopCategory);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($shopCategory = $this->selectById($id)) {
      $listOrder = $shopCategory->getListOrder();
      $parentCategoryId = $shopCategory->getParentCategoryId();
      if ($shopCategory = $this->selectByPreviousListOrder($parentCategoryId, $listOrder)) {
        return($shopCategory);
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
    $parentId = $currentObject->getParentCategoryId();
    $currentListOrder = $currentObject->getListOrder();

    if ($objects = $this->selectByParentCategoryId($parentId)) {
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
    $currentObject->setParentCategoryId($targetObject->getParentCategoryId());
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
    $parentId = $currentObject->getParentCategoryId();
    $currentListOrder = $currentObject->getListOrder();

    if ($objects = $this->selectByParentCategoryId($parentId)) {
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
    $currentObject->setParentCategoryId($targetObject->getParentCategoryId());
    $this->update($currentObject);

    return(true);
  }

  // Check if a category contains some other categories
  function hasSubCategories($shopCategoryId) {
    $has = false;

    if ($shopCategories = $this->selectByParentCategoryId($shopCategoryId)) {
      if (count($shopCategories) > 0) {
        $has = true;
      }
    }

    return($has);
  }

  // Check if a category is a grand child of another one
  function isGrandChildOf($shopCategoryId, $parentId) {
    while ($shopCategoryId) {
      if ($shopCategory = $this->selectById($shopCategoryId)) {
        if ($shopCategory->getParentCategoryId() == $parentId) {
          return(true);
        } else {
          $shopCategoryId = $shopCategory->getParentCategoryId();
        }
      }
    }
    return(false);
  }

  // List the categories
  function getCategories($parentCategoryId = 0, $level = 0) {
    $listCategories = array();

    if ($shopCategories = $this->selectByParentCategoryId($parentCategoryId)) {
      foreach ($shopCategories as $shopCategory) {
        $shopCategoryId = $shopCategory->getId();
        array_push($listCategories, array($shopCategoryId, $level));
        $nextLevel = $level + 1;
        $listCategories = array_merge($listCategories, $this->getCategories($shopCategoryId, $nextLevel));
      }
    }

    return($listCategories);
  }

  // Get the list of category names
  function getCategoryNames() {
    $list = array();

    if ($categories = $this->getCategories()) {
      foreach ($categories as $category) {
        $shopCategoryId = $category[0];
        $level = $category[1];
        if ($shopCategory = $this->selectById($shopCategoryId)) {
          $name = $shopCategory->getName();
          $name = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $level) . " " . $name;
          $list[$shopCategoryId] = $name;
        }
      }
    }

    return($list);
  }

  // Get the list of categories
  function getAll() {
    $list = array();

    if ($categories = $this->getCategoryNames()) {
      foreach ($categories as $shopCategoryId => $name) {
        $list['SYSTEM_PAGE_SHOP_CATEGORY_LIST' . $shopCategoryId] = $this->mlText[0] . " " . $name;
      }
    }

    return($list);
  }

  // Render the navigation menu in Flash
  function renderFlashMenu($flashId) {

    // Update the name of the wddx file
    if ($flash = $this->flashUtils->selectById($flashId)) {
      // Refresh the wddx data file
      $wddxPacket = $this->createFlashWddxPacket();

      $file = $flash->getFile();
      if ($file && is_file($this->flashUtils->filePath . $file)) {
        $prefix = LibString::stripNonFilenameChar(LibFile::getFilePrefix($file));
        // Add the object id to avoid conflict between different navigation
        // elements using the same Flash animation .swf file
        $wddxFilename = $prefix . '.shopCategoryNavmenu.' . $flashId . FLASH_WDDX_SUFFIX;
        $flash->setWddx($wddxFilename);
        $this->flashUtils->update($flash);

        // Write the file
        $filename = $this->flashUtils->filePath . $wddxFilename;
        LibFile::writeString($filename, $wddxPacket);

        // Render the Flash animation
        // Because the rendering of the Flash animation makes use of the wddx filename, this rendering must
        // be done after the name of the wddx file has been set
        $str = $this->flashUtils->render($flashId);

        return($str);
      }
    }
  }

  // Create a wddx packet from the tree of web pages to be used by the Flash animation if any
  function createFlashWddxPacket() {
    $categoryArray = $this->getCategoryWddxArray();
    $navmenuLanguageArray = array(array('language' => '0', $categoryArray));

    $wddxPacketId = wddx_packet_start("shopCategoryNavmenu");
    wddx_add_vars($wddxPacketId, 'navmenuLanguageArray');
    $wddxPacket = wddx_packet_end($wddxPacketId);

    return($wddxPacket);
  }

  // Get the wddx array of sub categories for a category
  function getCategoryWddxArray($currentShopCategoryId = 0) {

    if (!$currentShopCategoryId) {
      $currentShopCategoryId = 0;
    }

    $categoryArray = array();

    if ($shopCategories = $this->selectByParentCategoryId($currentShopCategoryId)) {
      foreach ($shopCategories as $shopCategory) {
        $shopCategoryId = $shopCategory->getId();
        $name = $shopCategory->getName();
        $description = $shopCategory->getDescription();
        $url = $this->templateUtils->renderPageUrl($shopCategoryId);
        $listOrder = $shopCategory->getListOrder();

        $subitems = $this->getCategoryWddxArray($shopCategoryId);

        array_push($categoryArray, array('name' => $name, 'description' => $description, 'hide' => '', 'listOrder' => $listOrder, 'url' => $url, 'blankTarget' => '', 'templateModelId' => '', 'subitems' => $subitems));
      }
    }

    return($categoryArray);
  }

  // Render the navigation menu in javascript
  function renderMenu() {
    $str = "\n<div class='navmenu'>";

    $str .= "\n<div class='menuBar'>";

    $str .= "\n<a class='menuItem' title='' href='' onclick=\"return(buttonClick(event, 'menu_shop_root'));\" onmouseover=\"buttonMouseover(event, 'menu_shop_root');\">" . $this->websiteText[1] . "</a>";

    $str .= "\n</div>";

    $str .= $this->getMenu();

    $str .= "\n</div>";

    return($str);
  }

  // Render the navigation menu
  function getMenu($currentShopCategoryId = '') {
    $str = '';

    if (!$currentShopCategoryId) {
      $currentShopCategoryId = 0;

      $menuId = 'menu_shop_root';
    } else {
      $menuId = 'menu_shop_' . $currentShopCategoryId;
    }

    $str .= <<<HEREDOC

<div style='visibility: hidden;' id='$menuId' class='menu' onmouseover="menuMouseover(event)">
HEREDOC;

    if ($shopCategories = $this->selectByParentCategoryId($currentShopCategoryId)) {
      foreach ($shopCategories as $shopCategory) {
        $shopCategoryId = $shopCategory->getId();
        $name = $shopCategory->getName();
        $description = $shopCategory->getDescription();
        $url = $this->templateUtils->renderPageUrl($shopCategoryId);
        $listOrder = $shopCategory->getListOrder();

        $str .= "\n<div class='menuItem'>";
        if ($this->hasSubCategories($shopCategoryId)) {
          $str .= <<<HEREDOC

<a title='$description' href='' onclick="return false;" onmouseover="menuItemMouseover(event, 'menu_shop_$shopCategoryId');"><span class='no_style_menuItemText'>$name</span><span class='no_style_menuItemArrow'></span></a>
HEREDOC;
        } else {
          $str .= "\n<a href='$url' title='$description'>$name</a>";
        }
        $str .= "\n</div>";
      }
    }

    $str .= "\n</div>";

    foreach ($shopCategories as $shopCategory) {
      $shopCategoryId = $shopCategory->getId();
      if ($this->hasSubCategories($shopCategoryId)) {
        $str .= $this->getMenu($shopCategoryId);
      }
    }

    return($str);
  }

  // Render the accordion menu for the item categories
  function renderAccordionMenu() {
    $str = "\n<div class='navmenu'>";

    $str .= "\n<dl id='menuAccordionShopItem'>";

    $str .= $this->getAccordionMenu('', true);

    $str .= "\n</dl>";

    $str .= "\n</div>";

    // By default the menus are folded, meaning only the menus of the first level are not hidden
    $str .= <<<HEREDOC
<script type="text/javascript">
<!--
navmenuHideAllMenus('menuAccordionShopItem');
//-->
</script>
HEREDOC;

    return($str);
  }

  // Render the accordion menu
  function getAccordionMenu($parentCategoryId, $isTopMenu = false) {
    $str = '';

    $str .= "\n<ul>";

    $strNoDottedBorder = LibJavaScript::getNoDottedBorder();

    if (!$parentCategoryId) {
      $parentCategoryId = 0;
      $menuId = 'menu_shop_root';
    } else {
      $menuId = 'menu_shop_' . $parentCategoryId;
    }

    $i = 0;
    if ($shopCategories = $this->selectByParentCategoryId($parentCategoryId)) {
      foreach ($shopCategories as $shopCategory) {
        $shopCategoryId = $shopCategory->getId();
        $name = $shopCategory->getName();
        $description = $shopCategory->getDescription();

        if ($description) {
          $title = $description;
        } else {
          $title = $name;
        }

        if ($isTopMenu) {
          if ($i == 0) {
            $strClassID = 'menuBarItemFirst';
          } else if ($i == count($shopCategories) - 1) {
            $strClassID = 'menuBarItemLast';
          } else {
            $strClassID = 'menuBarItem';
          }
        } else {
          $strClassID = 'menuItem';
        }

        $str .= "\n<li class='$strClassID'>";

        if ($this->hasSubCategories($shopCategoryId)) {
          $str .= <<<HEREDOC
<dt onclick="javascript:navmenuDisplayHide('menu$shopCategoryId');">
<a title='$description' $strNoDottedBorder href='#' class='no_style_menuItemText'>$name</a>
</dt>
<dd style="display: block;" id="menu$shopCategoryId">
HEREDOC;
          $str .= $this->getAccordionMenu($shopCategoryId);
        } else {
          $url = $this->templateUtils->renderPageUrl('SYSTEM_PAGE_SHOP_CATEGORY_LIST' . $shopCategoryId);
          $str .= "\n<div class='menuItem'><a href='$url' title='$title'>$name</a></div>";
        }

        $str .= "\n</li>";

        $i++;
      }
    }

    $str .= "\n</ul>";

    return($str);
  }

}

?>
