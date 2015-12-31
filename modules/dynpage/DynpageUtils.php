<?

class DynpageUtils extends DynpageDB {

  var $mlText;
  var $websiteText;

  var $imageSize;
  var $imagePath;
  var $imageUrl;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $flashUtils;
  var $popupUtils;
  var $templateModelUtils;
  var $templateUtils;

  function DynpageUtils() {
    $this->DynpageDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageSize = 200000;
    $this->imagePath = $gDataPath . 'dynpage/image/';
    $this->imageUrl = $gDataUrl . '/dynpage/image';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imagePath)) {
      if (!is_dir($gDataPath . 'dynpage')) {
        mkdir($gDataPath . 'dynpage');
      }
      mkdir($this->imagePath);
      chmod($this->imagePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $templateModels = $this->templateModelUtils->getAllModels();

    $this->preferences = array(
      "DYNPAGE_SECURED_ACCESS" =>
      array($this->mlText[9], $this->mlText[10], PREFERENCE_TYPE_BOOLEAN, ''),
        "DYNPAGE_TEMPLATE_MODEL" =>
        array($this->mlText[17], $this->mlText[18], PREFERENCE_TYPE_SELECT, $templateModels),
          "DYNPAGE_TEMPLATE_MODEL_ON_PHONE" =>
          array($this->mlText[19], $this->mlText[20], PREFERENCE_TYPE_SELECT, $templateModels),
            "DYNPAGE_NAME_AS_TITLE" =>
            array($this->mlText[7], $this->mlText[8], PREFERENCE_TYPE_BOOLEAN, ''),
              "DYNPAGE_HTML_EDITOR" =>
              array($this->mlText[13], $this->mlText[14], PREFERENCE_TYPE_SELECT,
                array(
                  'HTML_EDITOR_INNOVA' => $this->mlText[15],
                  'HTML_EDITOR_CKEDITOR' => $this->mlText[16],
                )),
              "DYNPAGE_WEBSITE_IN_CONSTRUCTION" =>
              array($this->mlText[1], $this->mlText[2], PREFERENCE_TYPE_BOOLEAN, ''),
                "DYNPAGE_WEBSITE_IN_CONSTRUCTION_MESSAGE" =>
                array($this->mlText[3], $this->mlText[4], PREFERENCE_TYPE_MLTEXT, $this->mlText[5]),
                  "DYNPAGE_IMAGE_WIDTH" =>
                  array($this->mlText[21], $this->mlText[22], PREFERENCE_TYPE_TEXT, 300),
                    "DYNPAGE_PHONE_IMAGE_WIDTH" =>
                    array($this->mlText[23], $this->mlText[24], PREFERENCE_TYPE_TEXT, 140),
                    );

    $this->preferenceUtils->init($this->preferences);
  }

  // Get the width of the image
  function getImageWidth() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("DYNPAGE_PHONE_IMAGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("DYNPAGE_IMAGE_WIDTH");
    }

    return($width);
  }

  // Add a page
  function addPage($name, $description, $parentId, $content = '') {
    if ($parentId == DYNPAGE_ROOT_ID) {
      $parentId = 0;
    }

    $dynpage = new Dynpage();
    $dynpage->setName($name);
    $dynpage->setDescription($description);
    $dynpage->setParentId($parentId);
    $dynpage->setContent($content);
    $dynpage->setListOrder($this->getNextListOrder($parentId));
    $this->insert($dynpage);
    $dynpageId = $this->getLastInsertId();

    return($dynpageId);
  }

  // Duplicate a web page
  function duplicate($dynpageId, $name, $description, $parentId = '') {
    if ($dynpage = $this->selectById($dynpageId)) {
      $content = $dynpage->getContent();
      $hide = $dynpage->getHide();
      $secured = $dynpage->getSecured();
      $adminId = $dynpage->getAdminId();

      if (!$name) {
        $name = $dynpage->getName();
      }

      if ($name == $dynpage->getName() && $parentId == $dynpage->getParentId()) {
        $randomNumber = LibUtils::generateUniqueId();
        $name .= DYNPAGE_DUPLICATA . '_' . $randomNumber;
      }

      if (!$parentId) {
        $parentId = $dynpage->getParentId();
      }

      $duplicatedDynpage = new Dynpage();
      $duplicatedDynpage->setName($name);
      $duplicatedDynpage->setDescription($description);
      $duplicatedDynpage->setContent($content);
      $duplicatedDynpage->setParentId($parentId);
      $duplicatedDynpage->setHide($hide);
      $duplicatedDynpage->setSecured($secured);
      $duplicatedDynpage->setAdminId($adminId);
      $duplicatedDynpage->setListOrder($this->getNextListOrder($parentId));
      $this->insert($duplicatedDynpage);
      $duplicatedDynpageId = $this->getLastInsertId();

      if ($dynpages = $this->selectChildren($dynpageId)) {
        foreach ($dynpages as $childDynpage) {
          $childDynpageId = $childDynpage->getId();
          $duplicatedChildDynpageId = $this->duplicate($childDynpageId, $childDynpage->getName(), $childDynpage->getDescription(), $duplicatedDynpageId);
        }
      }

      return($duplicatedDynpageId);
    }
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imagePath);
    while ($imageFile = readdir($handle)) {
      if ($imageFile != "." && $imageFile != ".." && !strstr($imageFile, '*')) {
        if (!$this->imageIsUsed($imageFile)) {
          $imageFile = str_replace(" ", "\\ ", $imageFile);
          if (@file_exists($this->imagePath . $imageFile)) {
            @unlink($this->imagePath . $imageFile);
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

  // Get the template model, if any, in which to render the web pages
  function getTemplateModel() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $templateModelId = $this->preferenceUtils->getValue("DYNPAGE_TEMPLATE_MODEL_ON_PHONE");
    } else {
      $templateModelId = $this->preferenceUtils->getValue("DYNPAGE_TEMPLATE_MODEL");
    }

    return($templateModelId);
  }

  // Check if the selected html editor is the InnovaStudio
  function useHtmlEditorInnova() {
    $result = false;

    $htmlEditor = $this->preferenceUtils->getValue("DYNPAGE_HTML_EDITOR");

    if ($htmlEditor == 'HTML_EDITOR_INNOVA') {
      $result = true;
    }

    return($result);
  }

  // Check if the selected html editor is the CKEditor
  function useHtmlEditorCKEditor() {
    $result = false;

    $htmlEditor = $this->preferenceUtils->getValue("DYNPAGE_HTML_EDITOR");

    if ($htmlEditor == 'HTML_EDITOR_CKEDITOR') {
      $result = true;
    }

    return($result);
  }

  // Get the next available list order
  function getNextListOrder($parentId) {
    $listOrder = 1;

    if ($dynpages = $this->selectByParentId($parentId)) {
      $total = count($dynpages);
      if ($total > 0) {
        $dynpage = $dynpages[$total - 1];
        $listOrder = $dynpage->getListOrder() + 1;
      }
    }

    return($listOrder);
  }

  // Get the next object
  function selectNext($id) {
    if ($dynpage = $this->selectById($id)) {
      $listOrder = $dynpage->getListOrder();
      $parentId = $dynpage->getParentId();
      if ($dynpage = $this->selectByNextListOrder($parentId, $listOrder)) {
        return($dynpage);
      }
    }
  }

  // Get the next object
  function selectPrevious($id) {
    if ($dynpage = $this->selectById($id)) {
      $listOrder = $dynpage->getListOrder();
      $parentId = $dynpage->getParentId();
      if ($dynpage = $this->selectByPreviousListOrder($parentId, $listOrder)) {
        return($dynpage);
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
    $parentId = $currentObject->getParentId();
    $currentListOrder = $currentObject->getListOrder();

    if ($objects = $this->selectByParentId($parentId)) {
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
    $currentObject->setParentId($targetObject->getParentId());
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
    $parentId = $currentObject->getParentId();
    $currentListOrder = $currentObject->getListOrder();

    if ($objects = $this->selectByParentId($parentId)) {
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
    $currentObject->setParentId($targetObject->getParentId());
    $this->update($currentObject);

    return(true);
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
    if ($dynpage = $this->selectById($id)) {
      $listOrder = $dynpage->getListOrder();
      $parentId = $dynpage->getParentId();
      if ($dynpages = $this->selectByListOrder($parentId, $listOrder)) {
        if (($listOrder == 0) || (count($dynpages)) > 1) {
          $this->resetListOrder($parentId);
        }
      }
    }
  }

  // Render
  function render($dynpage) {
    $str = '';

    $str .= "\n<div class='dynpage'>";

    $content = $dynpage->getContent();

    $str .= $content;

    $str .= "\n</div>";

    return($str);
  }

  // Render the tags
  // When creating an element in the template system, it is necessary to create its tags
  // A dummy rendering is used for this operation
  function renderTagsBreadCrumbs() {
    $str = "\n<div class='dynpage_breadcrumbs'></div>";

    return($str);
  }

  // Get the list of all the pages
  function getPageNames() {
    $listPages = $this->getFolderPageNames();

    // Reverse the array and preserve the keys
    $listPages = array_reverse($listPages, true);

    return($listPages);
  }

  // Get the first page of the tree
  function getFirstPage() {
    if ($dynpages = $this->selectByParentId('')) {
      if (count($dynpages) > 0) {
        foreach ($dynpages as $dynpage) {
          $dynpageId = $dynpage->getId();
          return($dynpageId);
        }
      }
    }
  }

  // Get the list of pages for a page
  function getFolderPageNames($parentId = DYNPAGE_ROOT_ID) {
    $this->loadLanguageTexts();

    $listPages = array();

    if ($dynpages = $this->selectChildren($parentId)) {
      foreach ($dynpages as $dynpage) {
        $dynpageId = $dynpage->getId();
        $name = $dynpage->getName();

        $listPages[$dynpageId] = $this->websiteText[0] . " " . $this->getFolderPath($parentId) . ' / ' . $name;

        $dirListPages = $this->getFolderPageNames($dynpageId);

        $listPages = LibUtils::arrayMerge($dirListPages, $listPages);
      }
    }

    // Reverse the array and preserve the keys
    $listPages = array_reverse($listPages, true);

    return($listPages);
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
        $wddxFilename = $prefix . '.dynpageNavmenu.' . $flashId . FLASH_WDDX_SUFFIX;
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
    $pagesArray = $this->getPageWddxContent();
    $navmenuLanguageArray = array(array('language' => '0', $pagesArray));

    $wddxPacketId = wddx_packet_start("dynpageNavmenu");
    wddx_add_vars($wddxPacketId, 'navmenuLanguageArray');
    $wddxPacket = wddx_packet_end($wddxPacketId);

    return($wddxPacket);
  }

  // Get the wddx array of web pages for a directory
  function getPageWddxContent($currentParentId = '') {
    if (!$currentParentId) {
      $currentParentId = 0;
    }

    $pagesArray = array();

    $dynpages = $this->selectChildren($currentParentId);
    foreach ($dynpages as $dynpage) {
      $dynpageId = $dynpage->getId();
      $name = $dynpage->getName();
      $description = $dynpage->getDescription();
      $hide = $dynpage->getHide();
      $listOrder = $dynpage->getListOrder();
      $parentId = $dynpage->getParentId();
      $url = $this->templateUtils->renderPageUrl($dynpageId);

      if ($this->hasChild($dynpageId)) {
        $subDirectoryArray = $this->getPageWddxContent($parentId);
        array_push($pagesArray, array('name' => $name, 'description' => $description, 'hide' => $hide, 'listOrder' => $listOrder, 'url' => $url, 'blankTarget' => '', 'templateModelId' => '', 'subitems' => $subDirectoryArray));
      } else {
        array_push($pagesArray, array('name' => $name, 'description' => $description, 'hide' => $hide, 'listOrder' => $listOrder, 'url' => $url, 'blankTarget' => '', 'templateModelId' => '', 'subitems' => ''));
      }
    }

    return($pagesArray);
  }

  // Render the navigation menu
  function renderMenu($currentParentId = DYNPAGE_ROOT_ID) {
    $this->loadLanguageTexts();

    if ($dynpage = $this->selectById($currentParentId)) {
      $name = $dynpage->getName();
    } else {
      $name = $this->websiteText[6];
    }

    $str = "\n<div class='navmenu'>";

    $str .= "\n<div class='menuBar'>";

    $str .= "\n<a class='menuItem' title='' href='' onclick=\"return(buttonClick(event, 'menu_dynpage_root'));\" onmouseover=\"buttonMouseover(event, 'menu_dynpage_root');\">" . $name . "</a>";

    $str .= "\n</div>";

    if ($currentParentId == DYNPAGE_ROOT_ID) {
      $currentParentId = 0;
    }

    $str .= $this->getMenu($currentParentId, true);

    $str .= "\n</div>";

    return($str);
  }

  // Render the navigation menu
  function getMenu($currentParentId, $isTopMenu = false) {
    $str = '';

    if ($isTopMenu) {
      $menuId = 'menu_dynpage_root';
    } else {
      $menuId = 'menu_dynpage_' . $currentParentId;
    }

    $str .= <<<HEREDOC
<div style='visibility: hidden;' id='$menuId' class='menu' onmouseover="menuMouseover(event)">
HEREDOC;

    if ($dynpages = $this->selectChildren($currentParentId)) {
      foreach ($dynpages as $dynpage) {
        $dynpageId = $dynpage->getId();
        $name = $dynpage->getName();
        $description = $dynpage->getDescription();
        $hide = $dynpage->getHide();

        if ($hide) {
          continue;
        }

        if ($description) {
          $title = $description;
        } else {
          $title = $name;
        }

        $url = $this->templateUtils->renderPageUrl($dynpageId);

        if ($this->hasChild($dynpageId)) {
          $str .= <<<HEREDOC
<div><a title='' href='$url' onmouseover="menuItemMouseover(event, 'menu_dynpage_$dynpageId');"><span class='no_style_menuItemText'>$name</span><span class='no_style_menuItemArrow'></span></a></div>
HEREDOC;
        } else {
          $str .= "<div class='menuItem'><a href='$url' title='$title'>$name</a></div>";
        }
      }

      $str .= "\n</div>";

      foreach ($dynpages as $dynpage) {
        $dynpageId = $dynpage->getId();
        if ($this->hasChild($dynpageId)) {
          $str .= $this->getMenu($dynpageId);
        }
      }
    }

    return($str);
  }

  // Render the accordion navigation menu
  function renderAccordionMenu($currentParentId = DYNPAGE_ROOT_ID) {
    $str = "\n<div class='navmenu'>";

    $str .= "\n<dl id='menuAccordionDynpage'>";

    if ($currentParentId == DYNPAGE_ROOT_ID) {
      $currentParentId = 0;
    }

    $str .= $this->getAccordionMenu($currentParentId, true);

    $str .= "\n</dl>";

    $str .= "\n</div>";

    // By default the menus are folded, meaning only the menus of the first level are not hidden
    $str .= <<<HEREDOC
<script type="text/javascript">
<!--
navmenuHideAllMenus('menuAccordionDynpage');
//-->
</script>
HEREDOC;

    return($str);
  }

  // Render the accordion menu
  function getAccordionMenu($currentParentId, $isTopMenu = false) {
    $str = '';

    $str .= "\n<ul>";

    $strNoDottedBorder = LibJavaScript::getNoDottedBorder();

    if ($isTopMenu) {
      $menuId = 'menu_dynpage_root';
    } else {
      $menuId = 'menu_dynpage_' . $currentParentId;
    }

    $i = 0;
    if ($dynpages = $this->selectChildren($currentParentId)) {
      foreach ($dynpages as $dynpage) {
        $dynpageId = $dynpage->getId();
        $name = $dynpage->getName();
        $description = $dynpage->getDescription();
        $hide = $dynpage->getHide();

        if ($hide) {
          continue;
        }

        if ($description) {
          $title = $description;
        } else {
          $title = $name;
        }

        if ($isTopMenu) {
          if ($i == 0) {
            $strClassID = 'menuBarItemFirst';
          } else if ($i == count($dynpages) - 1) {
            $strClassID = 'menuBarItemLast';
          } else {
            $strClassID = 'menuBarItem';
          }
        } else {
          $strClassID = 'menuItem';
        }

        $str .= "\n<li>";

        if ($this->hasChild($dynpageId)) {
          $url = $this->templateUtils->renderPageUrl($dynpageId);
          $str .= <<<HEREDOC
<dt onclick="javascript:navmenuDisplayHide('menu$dynpageId');">
<div class='$strClassID'><a title='$description' $strNoDottedBorder href='$url' class='no_style_menuItemText'>$name</a></div>
</dt>
<dd style="display: block;" id="menu$dynpageId">
HEREDOC;
          $str .= $this->getAccordionMenu($dynpageId);
        } else {
          $url = $this->templateUtils->renderPageUrl($dynpageId);
          $str .= "\n<div class='$strClassID'><a href='$url' title='$title'>$name</a></div>";
        }

        $str .= "\n</li>";

        $i++;
      }
    }

    $str .= "\n</ul>";

    return($str);
  }

  // Render the header (DHTML javascript code) for the link tree
  function renderDirectoryTreeHeader($withImages = false) {
    global $gJsUrl;

    $str = <<<HEREDOC

<script type='text/javascript'>
<!--
var gJsTreeUrl = '$gJsUrl/tree/';
//-->
</script>
HEREDOC;

    $str .= "\n<script type='text/javascript' src='$gJsUrl/tree/tree.js'></script>";

    if ($withImages) {
      $str .= "\n<script type='text/javascript' src='$gJsUrl/tree/tree_tpl.js'></script>";
    } else {
      $str .= "\n<script type='text/javascript' src='$gJsUrl/tree/tree_tpl_bare.js'></script>";
    }

    return($str);
  }

  // Render the navigation links tree
  function renderDirectoryTree($currentParentId = DYNPAGE_ROOT_ID) {
    global $gHomeUrl;

    if ($currentParentId == DYNPAGE_ROOT_ID) {
      $currentParentId = 0;
    }

    $str = "\n<div class='dynpage_link_tree'>";

    $str .= <<<HEREDOC
<script type='text/javascript'>
<!--
var TREE_ITEMS = [
HEREDOC;

    $str .= "\n['', '',";
    $str .= $this->getDirectoryTree($currentParentId);
    $str .= "\n],";

    $str .= <<<HEREDOC
];
//-->
</script>

<script type='text/javascript'>
<!--
new tree(TREE_ITEMS, tree_tpl);
//-->
</script>
HEREDOC;

    $str .= "\n</div>";

    return($str);
  }

  // Get the navigation link tree
  function getDirectoryTree($currentParentId) {
    $str = '';

    $dynpages = $this->selectChildren($currentParentId);
    foreach ($dynpages as $dynpage) {
      $dynpageId = $dynpage->getId();
      $name = $dynpage->getName();
      $hide = $dynpage->getHide();

      $url = $this->templateUtils->renderPageUrl($dynpageId);

      if (!$hide) {
        $str .= "['$name', '$url',";
        $str .= $this->getDirectoryTree($dynpageId);
        $str .= "],";
      }
    }

    return($str);
  }

  // Render the bread crumbs navigation links
  function renderBreadCrumbs() {
    global $gJSNoStatus;

    // Get the user current page
    $currentUserPageId = LibSession::getSessionValue(DYNPAGE_SESSION_USER_PAGE);

    if ($dynpage = $this->selectById($currentUserPageId)) {
      $parentId = $dynpage->getParentId();
    } else {
      $parentId = '';
    }

    $crumbs = $this->getBreadCrumbs($parentId);

    $str = "\n<div class='dynpage_breadcrumbs'>";
    $str .= "/";

    foreach ($crumbs as $crumb) {
      list($name, $description, $url) = $crumb;

      $str .= " <a href='$url' $gJSNoStatus title='$description'>$name</a> /";
    }

    $str .= "</div>";

    return($str);
  }

  // Construct the pages bread crumbs
  function getBreadCrumbs($dynpageId) {
    $crumbs = array();

    if ($dynpage = $this->selectById($dynpageId)) {
      $name = $dynpage->getName();
      $description = $dynpage->getDescription();
      $parentId = $dynpage->getParentId();

      $url = $this->templateUtils->renderPageUrl($dynpageId);

      $crumb = array($name, $description, $url);

      $crumbs = $this->getBreadCrumbs($parentId);

      $crumbs[count($crumbs)] = $crumb;
    }

    return($crumbs);
  }

  // Check if a page is secured
  function isSecured($dynpageId) {
    if ($dynpage = $this->selectById($dynpageId)) {
      if ($dynpage->getSecured()) {
        return(true);
      } else {
        $parentId = $dynpage->getParentId();
        if ($parentId) {
          return($this->isSecured($parentId));
        }
      }
    }
  }

  // Check if a page is a grand child of another page
  function isGrandChildOf($dynpageId, $parentId) {
    while ($dynpageId) {
      if ($dynpage = $this->selectById($dynpageId)) {
        if ($dynpage->getParentId() == $parentId) {
          return(true);
        } else {
          $dynpageId = $dynpage->getParentId();
        }
      }
    }
    return(false);
  }

  // Check if a page has at least one child page
  function hasChild($dynpageId) {
    if (count($this->selectChildren($dynpageId)) > 0) {
      return(true);
    } else {
      return(false);
    }
  }

  // Select the children of a page
  function selectChildren($dynpageId) {
    $listPages = array();

    if ($dynpageId == DYNPAGE_ROOT_ID) {
      $dynpageId = 0;
    }

    $dynpages = $this->selectByParentId($dynpageId);
    foreach ($dynpages as $dynpage) {
      $dynpageId = $dynpage->getId();
      $garbage = $dynpage->getGarbage();
      if (!$garbage) {
        array_push($listPages, $dynpage);
      }
    }

    return($listPages);
  }

  // Get the full path to the page
  function getFolderPath($dynpageId) {
    $separator = ' / ';

    $path = '';

    if ($dynpageId) {
      if ($dynpage = $this->selectById($dynpageId)) {
        $name = $dynpage->getName();
        $parentId = $dynpage->getParentId();
        if ($dynpageId != $parentId) {
          $path = $this->getFolderPath($parentId) . $separator . $name;
        }
      }
    }

    return($path);
  }

  // Delete a page in the garbage
  function putInGarbage($dynpageId) {
    if ($dynpage = $this->selectById($dynpageId)) {
      $garbage = $dynpage->getGarbage();

      if (!$garbage) {
        // Store the page in the garbage
        $dynpage->setGarbage(true);

        // Free the name of the page when it is put into the garbage
        $randomNumber = LibUtils::generateUniqueId();
        $name = $dynpage->getName() . DYNPAGE_GARBAGE . '_' . $randomNumber;
        $dynpage->setName($name);

        // Reset the page parent as root
        $dynpage->setParentId('');

        // Reset the list order
        $dynpage->setListOrder(0);

        $this->update($dynpage);

        if ($dynpages = $this->selectChildren($dynpageId)) {
          foreach ($dynpages as $dynpage) {
            $dynpageId = $dynpage->getId();
            $this->putInGarbage($dynpageId);
          }
        }
      }
    }
  }

  // Restore a page from the garbage
  function restoreFromGarbage($dynpageId) {
    if ($dynpage = $this->selectById($dynpageId)) {
      // Reset the directory
      $dynpage->setParentId(0);

      // Set a new list order
      $dynpage->setListOrder($this->getNextListOrder($dynpage->getParentId()));

      // Remove the page from the garbage
      $dynpage->setGarbage(false);

      $this->update($dynpage);
    }
  }

  function getSecureWarning() {
    global $gCommonImagesUrl;
    global $gImageWarning;

    $this->loadLanguageTexts();

    $str = '';

    if (!$this->preferenceUtils->getValue("DYNPAGE_SECURED_ACCESS")) {
      $str = $this->popupUtils->getTipPopup("<img border='0' src='$gCommonImagesUrl/$gImageWarning' title=''>"
        . $this->mlText[12], $this->mlText[11], 300, 400);
    }

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElements() {
    $str = "\n<div class='dynpage'>A web page"
      . "<div class='lexicon_entry'>A lexicon entry anchor</div>"
      . "<div class='lexicon_entry_name'>The name for a lexicon entry</div>"
      . "<div class='lexicon_entry_explanation'>The explanation for a lexicon entry</div>"
      . "<div class='lexicon_entry_list_item'>A lexicon entry in the list under the text</div>"
      . "<div class='lexicon_entry_list_item_name'>The name for a lexicon entry in the list under the text</div>"
      . "<div class='lexicon_entry_list_item_explanation'>The explanation for a lexicon entry in the list under the text</div>"
      . "</div>";

    return($str);
  }

}

?>
