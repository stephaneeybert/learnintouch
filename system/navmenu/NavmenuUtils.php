<?php

class NavmenuUtils extends NavmenuDB {

  var $mlText;

  var $currentNavmenuId;

  var $languageUtils;
  var $navmenuItemUtils;
  var $navmenuLanguageUtils;
  var $templateUtils;

  function NavmenuUtils() {
    $this->NavmenuDB();

    $this->init();
  }

  function init() {
    $this->currentNavmenuId = "navmenuCurrentNavmenuId";
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Add a navigation menu
  function add() {
    $navmenu = new Navmenu();
    $this->insert($navmenu);
    $navmenuId = $this->getLastInsertId();

    $languageCode = $this->languageUtils->getCurrentLanguageCode();

    // Add a language to the menu if none
    if (!$navmenuLanguages = $this->navmenuLanguageUtils->selectByNavmenuId($navmenuId)) {
      // Create a root empty item for each language
      $navmenuItem = new NavmenuItem();
      $listOrder = $this->navmenuItemUtils->getNextListOrder('');
      $navmenuItem->setListOrder($listOrder);
      $this->navmenuItemUtils->insert($navmenuItem);
      $navmenuItemId = $this->navmenuItemUtils->getLastInsertId();

      $navmenuLanguage = new NavmenuLanguage();
      $navmenuLanguage->setLanguage($languageCode);
      $navmenuLanguage->setNavmenuId($navmenuId);
      $navmenuLanguage->setNavmenuItemId($navmenuItemId);
      $this->navmenuLanguageUtils->insert($navmenuLanguage);
    }

    return($navmenuId);
  }

  // Duplicate a navigation menu
  function duplicate($navmenuId) {
    if ($navmenu = $this->selectById($navmenuId)) {
      $duplicatedNavmenu = new Navmenu();
      $this->insert($duplicatedNavmenu);
      $duplicatedNavmenuId = $this->getLastInsertId();

      // Duplicate the languages
      if ($navmenuLanguages = $this->navmenuLanguageUtils->selectByNavmenuId($navmenuId)) {
        foreach ($navmenuLanguages as $navmenuLanguage) {
          // Create a root empty item for each language
          $navmenuItem = new NavmenuItem();
          $listOrder = $this->navmenuItemUtils->getNextListOrder('');
          $navmenuItem->setListOrder($listOrder);
          $this->navmenuItemUtils->insert($navmenuItem);
          $duplicatedRootNavmenuItemId = $this->navmenuItemUtils->getLastInsertId();

          $duplicatedNavmenuLanguage = new NavmenuLanguage();
          $duplicatedNavmenuLanguage->setLanguage($navmenuLanguage->getLanguage());
          $duplicatedNavmenuLanguage->setNavmenuId($duplicatedNavmenuId);
          $duplicatedNavmenuLanguage->setNavmenuItemId($duplicatedRootNavmenuItemId);
          $this->navmenuLanguageUtils->insert($duplicatedNavmenuLanguage);

          // Duplicate the items of a language
          $rootNavmenuItemId = $navmenuLanguage->getNavmenuItemId();
          $this->duplicateItems($rootNavmenuItemId, $duplicatedRootNavmenuItemId);
        }
      }

      return($duplicatedNavmenuId);
    }
  }

  // Duplicate the items of a menu language
  function duplicateItems($parentNavmenuItemId, $duplicatedParentNavmenuItemId) {
    if ($navmenuItems = $this->navmenuItemUtils->selectByParentNavmenuItemId($parentNavmenuItemId)) {
      foreach ($navmenuItems as $navmenuItem) {
        $navmenuItemId = $navmenuItem->getId();
        $duplicatedNavmenuItem = new NavmenuItem();

        $duplicatedNavmenuItem->setName($navmenuItem->getName());

        // Duplicate the image files
        $imagePath = $this->navmenuItemUtils->imagePath;
        $image = $navmenuItem->getImage();
        $imageOver = $navmenuItem->getImageOver();
        if ($image) {
          if ($image && is_file($imagePath . $image)) {
            $randomNumber = LibUtils::generateUniqueId();
            $imagePrefix = LibFile::getFilePrefix($image);
            $imageSuffix = LibFile::getFileSuffix($image);
            $imageDuplicata = $imagePrefix . '_' . $randomNumber . '.' . $imageSuffix;
            copy($imagePath . $image, $imagePath . $imageDuplicata);
            $duplicatedNavmenuItem->setImage($imageDuplicata);
          }
        }
        if ($imageOver) {
          if ($imageOver && is_file($imagePath . $imageOver)) {
            $randomNumber = LibUtils::generateUniqueId();
            $imagePrefix = LibFile::getFilePrefix($imageOver);
            $imageSuffix = LibFile::getFileSuffix($imageOver);
            $imageDuplicata = $imagePrefix . '_' . $randomNumber . '.' . $imageSuffix;
            copy($imagePath . $imageOver, $imagePath . $imageDuplicata);
            $duplicatedNavmenuItem->setImageOver($imageDuplicata);
          }
        }

        $duplicatedNavmenuItem->setUrl($navmenuItem->getUrl());
        $duplicatedNavmenuItem->setBlankTarget($navmenuItem->getBlankTarget());
        $duplicatedNavmenuItem->setDescription($navmenuItem->getDescription());
        $duplicatedNavmenuItem->setHide($navmenuItem->getHide());
        $duplicatedNavmenuItem->setTemplateModelId($navmenuItem->getTemplateModelId());
        $duplicatedNavmenuItem->setListOrder($navmenuItem->getListOrder());
        $duplicatedNavmenuItem->setParentNavmenuItemId($duplicatedParentNavmenuItemId);

        $this->navmenuItemUtils->insert($duplicatedNavmenuItem);
        $duplicatedNavmenuItemId = $this->navmenuItemUtils->getLastInsertId();

        $this->duplicateItems($navmenuItemId, $duplicatedNavmenuItemId);
      }
    }
  }

  // Delete the navigation menu
  function deleteNavmenu($id) {
    $this->dataSource->selectDatabase();

    // Delete all the languages of the menu
    if ($navmenuLanguages = $this->navmenuLanguageUtils->selectByNavmenuId($id)) {
      foreach ($navmenuLanguages as $navmenuLanguage) {
        $navmenuLanguageId = $navmenuLanguage->getId();
        $this->navmenuLanguageUtils->delete($navmenuLanguageId);
      }
    }

    $this->dao->delete($id);
  }

  // Get the navigation menu for the current language
  function getNavmenu($navmenuId) {
    if (!$navmenu = $this->selectByLanguage($languageCode)) {
      // If none is found then try to get one for all languages
      if (!$navmenu = $this->selectByLanguage('')) {
        // If none is found then try to get the default language one
        $navmenu = $this->selectByLanguage($this->languageUtils->getDefaultLanguageCode());
      }
    }

    return($navmenu);
  }

  // Count the available languages for the navigation menu
  function countAvailableLanguages($navmenuId) {
    $languageNames = $this->getAvailableLanguages($navmenuId);

    return(count($languageNames));
  }

  // Get the available languages for the navigation menu
  function getAvailableLanguages($navmenuId) {
    $this->loadLanguageTexts();

    $languageNames = $this->languageUtils->getActiveLanguageNames();
    $languageNames = array_merge(array('' => $this->mlText[0]), $languageNames);

    if ($navmenuLanguages = $this->navmenuLanguageUtils->selectByNavmenuId($navmenuId)) {
      foreach ($navmenuLanguages as $navmenuLanguage) {
        $language = $navmenuLanguage->getLanguage();
        unset($languageNames[$language]);
      }
    }

    return($languageNames);
  }

  // Render the header (DHTML javascript code) for the menu
  function renderHeader() {
    global $gJsUrl;

    $str = "\n<script type='text/javascript' src='$gJsUrl/menu/menu.js'></script>";
    $str .= "\n<script type='text/javascript' src='$gJsUrl/cookies.js'></script>";
    $str .= "\n<link href='$gJsUrl/menu/menu.css' rel='stylesheet' type='text/css' />";

    return($str);
  }

  // Render the navigation menu
  function render($navmenuId) {
    global $gTemplateUrl;

    if (!$navmenu = $this->selectById($navmenuId)) {
      return;
    }

    // Check if the menu is not to be displayed
    $hide = $navmenu->getHide();
    if ($hide) {
      return;
    }

    $languageCode = $this->languageUtils->getCurrentLanguageCode();

    // Get the navmenu language
    if (!$navmenuLanguage = $this->navmenuLanguageUtils->selectByNavmenuIdAndLanguage($navmenuId, $languageCode)) {
      // If none is found then try to get one for the default language
      if (!$navmenuLanguage = $this->navmenuLanguageUtils->selectByNavmenuIdAndLanguage($navmenuId, $this->languageUtils->getDefaultLanguageCode())) {
        // If none is found then get the link for no specific language
        if (!$navmenuLanguage = $this->navmenuLanguageUtils->selectByNavmenuIdAndNoLanguage($navmenuId)) {
          return;
        }
      }
    }

    // Get the root empty item
    $rootNavmenuItemId = $navmenuLanguage->getNavmenuItemId();

    if (!$navmenuItems = $this->navmenuItemUtils->selectByParentNavmenuItemId($rootNavmenuItemId)) {
      return;
    }

    $str = '';

    $str .= "\n<div class='navmenu'>";

    $str .= "\n<div class='menuBar'>";

    if ($navmenuItems = $this->navmenuItemUtils->selectByParentNavmenuItemId($rootNavmenuItemId)) {
      for ($i = 0; $i < count($navmenuItems); $i++) {
        $navmenuItem = $navmenuItems[$i];
        $navmenuItemId = $navmenuItem->getId();
        $name = $navmenuItem->getName();
        $description = $navmenuItem->getDescription();
        $hide = $navmenuItem->getHide();
        $templateModelId = $navmenuItem->getTemplateModelId();
        $image = $navmenuItem->getImage();
        $imageOver = $navmenuItem->getImageOver();
        $blankTarget = $navmenuItem->getBlankTarget();
        $url = $navmenuItem->getUrl();

        if ($hide) {
          continue;
        }

        $imagePath = $this->navmenuItemUtils->imagePath;
        $imageUrl = $this->navmenuItemUtils->imageUrl;

        if ($image && is_file($imagePath . $image)) {
          if ($imageOver && is_file($imagePath . $imageOver)) {
            $strOnMouseOver = "onmouseover=\"src='$imageUrl/$imageOver'\" onmouseout=\"src='$imageUrl/$image'\"";
          } else {
            $strOnMouseOver = '';
          }

          $anchor = "<img class='menuBarItemImg' src='$imageUrl/$image' $strOnMouseOver title='$description' alt='' />";
          if ($name) {
            $anchor .= ' ' . $name;
          }
        } else {
          $anchor = $name;
        }

        $strUrl = $this->templateUtils->renderPageUrl($url, $templateModelId);

        if ($blankTarget) {
          $strTarget = "onclick=\"window.open(this.href, '_blank'); return(false);\"";
        } else {
          $strTarget = '';
        }

        $strNoDottedBorder = LibJavaScript::getNoDottedBorder();

        if ($i == 0) {
          $strClassID = 'menuBarItemFirst';
        } else if ($i == count($navmenuItems) - 1) {
          $strClassID = 'menuBarItemLast';
        } else {
          $strClassID = 'menuBarItem';
        }

        $str .= <<<HEREDOC
<a style='display:inline-block;' class='$strClassID' title='$description' $strNoDottedBorder $strTarget href='$strUrl' onclick="return buttonClick(event, 'menu$navmenuItemId');" onmouseover="buttonMouseover(event, 'menu$navmenuItemId');">$anchor</a>
HEREDOC;
      }
    }

    $str .= "\n</div>";

    if ($navmenuItems = $this->navmenuItemUtils->selectByParentNavmenuItemId($rootNavmenuItemId)) {
      foreach ($navmenuItems as $navmenuItem) {
        $navmenuItemId = $navmenuItem->getId();
        $subNavmenuItems = $this->navmenuItemUtils->selectByParentNavmenuItemId($navmenuItemId);
        if (count($subNavmenuItems) > 0) {
          $str .= $this->getMenu($navmenuItemId);
        }
      }
    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the navigation menu
  function getMenu($parentNavmenuItemId) {
    $str = '';

    $str .= <<<HEREDOC
<div style='visibility:hidden;' id='menu$parentNavmenuItemId' class='menu' onmouseover="menuMouseover(event)">
HEREDOC;

    if ($navmenuItems = $this->navmenuItemUtils->selectByParentNavmenuItemId($parentNavmenuItemId)) {
      foreach ($navmenuItems as $navmenuItem) {
        $navmenuItemId = $navmenuItem->getId();
        $name = $navmenuItem->getName();
        $description = $navmenuItem->getDescription();
        $hide = $navmenuItem->getHide();
        $url = $navmenuItem->getUrl();
        $templateModelId = $navmenuItem->getTemplateModelId();
        $blankTarget = $navmenuItem->getBlankTarget();
        $image = $navmenuItem->getImage();
        $imageOver = $navmenuItem->getImageOver();

        if ($hide) {
          continue;
        }

        $imagePath = $this->navmenuItemUtils->imagePath;
        $imageUrl = $this->navmenuItemUtils->imageUrl;

        if ($image && is_file($imagePath . $image)) {
          if ($imageOver && is_file($imagePath . $imageOver)) {
            $strOnMouseOver = "onmouseover=\"src='$imageUrl/$imageOver'\" onmouseout=\"src='$imageUrl/$image'\"";
          } else {
            $strOnMouseOver = '';
          }

          $anchor = "<img class='menuItemImg' src='$imageUrl/$image' $strOnMouseOver title='$description' alt='' /> $name";
        } else {
          $anchor = $name;
        }

        if ($blankTarget) {
          $strTarget = "onclick=\"window.open(this.href, '_blank'); return(false);\"";
        } else {
          $strTarget = '';
        }

        $strNoDottedBorder = LibJavaScript::getNoDottedBorder();

        $subNavmenuItems = $this->navmenuItemUtils->selectByParentNavmenuItemId($navmenuItemId);

        if ($name == 'NAVMENU_SEPARATOR') {
          $str .= "\n<div class='menuItemSep'></div>";
        } else if (count($subNavmenuItems) > 0) {
          if ($url) {
            $strUrl = $this->templateUtils->renderPageUrl($url, $templateModelId);
            $strHRef = "href='$strUrl'";
          } else {
            $strHRef = "href='#' onclick='return(false);'";
          }
          $str .= <<<HEREDOC
<div><a class='menuItem' title='$description' $strNoDottedBorder $strHRef onmouseover="menuItemMouseover(event, 'menu$navmenuItemId');"><span class='no_style_menuItemText'>$anchor</span></a></div>
HEREDOC;
        } else if ($url) {
          $strUrl = $this->templateUtils->renderPageUrl($url, $templateModelId);
          $str .= "\n<a class='menuItem' href='$strUrl' $strNoDottedBorder $strTarget title='$description'>$anchor</a>";
        } else {
          $str .= "\n<div class='menuItemHdr'>$anchor</div>";
        }
      }

      $str .= "\n</div>";

      foreach ($navmenuItems as $navmenuItem) {
        $navmenuItemId = $navmenuItem->getId();
        $subNavmenuItems = $this->navmenuItemUtils->selectByParentNavmenuItemId($navmenuItemId);
        if (count($subNavmenuItems) > 0) {
          $str .= $this->getMenu($navmenuItemId);
        }
      }

    }

    return($str);
  }

  // Get the wddx array of items for the sub navigation menu of an item
  function getNavmenuWddxArray($parentNavmenuItemId) {
    $navmenuItemArray = array();
    if ($navmenuItems = $this->navmenuItemUtils->selectByParentNavmenuItemId($parentNavmenuItemId)) {
      foreach ($navmenuItems as $navmenuItem) {
        $navmenuItemId = $navmenuItem->getId();
        $name = $navmenuItem->getName();
        $description = $navmenuItem->getDescription();
        $hide = $navmenuItem->getHide();
        $listOrder = $navmenuItem->getListOrder();
        $url = $navmenuItem->getUrl();
        $blankTarget = $navmenuItem->getBlankTarget();
        $templateModelId = $navmenuItem->getTemplateModelId();

        $strUrl = $this->templateUtils->renderPageUrl($url, $templateModelId);

        $subNavmenuItemArray = $this->getNavmenuWddxArray($navmenuItemId);

        array_push($navmenuItemArray, array('name' => $name, 'description' => $description, 'hide' => $hide, 'listOrder' => $listOrder, 'url' => $strUrl, 'blankTarget' => $blankTarget, 'templateModelId' => $templateModelId, 'subitems' => $subNavmenuItemArray));
      }
    }

    return($navmenuItemArray);
  }

  // Render the header (DHTML javascript code) for the accordion menu
  function renderAccordionMenuHeader() {
    global $gJsUrl;

    $str = "\n<script type='text/javascript' src='$gJsUrl/inlineMenu/menu.js'></script>";
    $str .= "\n<script type='text/javascript' src='$gJsUrl/cookies.js'></script>";
    $str .= "\n<link href='$gJsUrl/inlineMenu/menu.css' rel='stylesheet' type='text/css' />";

    return($str);
  }

  // Render the in-line menu
  function renderAccordionMenu($navmenuId) {
    global $gTemplateUrl;

    if (!$navmenu = $this->selectById($navmenuId)) {
      return;
    }

    // Check if the menu is not to be displayed
    $hide = $navmenu->getHide();
    if ($hide) {
      return;
    }

    $languageCode = $this->languageUtils->getCurrentLanguageCode();

    // Get the navmenu language
    if (!$navmenuLanguage = $this->navmenuLanguageUtils->selectByNavmenuIdAndLanguage($navmenuId, $languageCode)) {
      // If none is found then try to get one for the default language
      if (!$navmenuLanguage = $this->navmenuLanguageUtils->selectByNavmenuIdAndLanguage($navmenuId, $this->languageUtils->getDefaultLanguageCode())) {
        // If none is found then get the link for the no specific language
        if (!$navmenuLanguage = $this->navmenuLanguageUtils->selectByNavmenuIdAndLanguage($navmenuId, '')) {
          return;
        }
      }
    }

    // Get the root empty item
    $rootNavmenuItemId = $navmenuLanguage->getNavmenuItemId();

    if (!$navmenuItems = $this->navmenuItemUtils->selectByParentNavmenuItemId($rootNavmenuItemId)) {
      return;
    }

    $str = '';

    $str .= "\n<div class='navmenu'>";

    $str .= "\n<dl id='menu$rootNavmenuItemId'>";

    $str .= $this->getAccordionMenu($rootNavmenuItemId, true);

    $str .= "\n</dl>";

    $str .= "\n</div>";

    // By default the menus are folded, meaning only the menus of the first level are not hidden
    $str .= <<<HEREDOC
<script type="text/javascript">
<!--
navmenuHideAllMenus('menu$rootNavmenuItemId');
//-->
</script>
HEREDOC;

    return($str);
  }

  // Render the in-line menu
  function getAccordionMenu($parentNavmenuItemId, $isTopMenu = false) {
    $str = '';


    $str .= "\n<ul>";

    $strNoDottedBorder = LibJavaScript::getNoDottedBorder();

    if ($navmenuItems = $this->navmenuItemUtils->selectByParentNavmenuItemId($parentNavmenuItemId)) {
      for ($i = 0; $i < count($navmenuItems); $i++) {
        $navmenuItem = $navmenuItems[$i];
        $navmenuItemId = $navmenuItem->getId();
        $name = $navmenuItem->getName();
        $description = $navmenuItem->getDescription();
        $hide = $navmenuItem->getHide();
        $url = $navmenuItem->getUrl();
        $templateModelId = $navmenuItem->getTemplateModelId();
        $blankTarget = $navmenuItem->getBlankTarget();
        $image = $navmenuItem->getImage();
        $imageOver = $navmenuItem->getImageOver();

        if ($hide) {
          continue;
        }

        $imagePath = $this->navmenuItemUtils->imagePath;
        $imageUrl = $this->navmenuItemUtils->imageUrl;

        if ($image && is_file($imagePath . $image)) {
          if ($imageOver && is_file($imagePath . $imageOver)) {
            $strOnMouseOver = "onmouseover=\"src='$imageUrl/$imageOver'\" onmouseout=\"src='$imageUrl/$image'\"";
          } else {
            $strOnMouseOver = '';
          }

          $anchor = "<img class='menuItemImg' src='$imageUrl/$image' $strOnMouseOver title='$description' alt='' /> $name";
        } else {
          $anchor = $name;
        }

        if ($blankTarget) {
          $strTarget = "onclick=\"window.open(this.href, '_blank'); return(false);\"";
        } else {
          $strTarget = '';
        }

        $subNavmenuItems = $this->navmenuItemUtils->selectByParentNavmenuItemId($navmenuItemId);

        if ($isTopMenu) {
          if ($i == 0) {
            $strClassID = 'menuBarItemFirst';
          } else if ($i == count($navmenuItems) - 1) {
            $strClassID = 'menuBarItemLast';
          } else {
            $strClassID = 'menuBarItem';
          }
        } else {
          $strClassID = 'menuItem';
        }

        $str .= "\n<li>";

        if ($name == 'NAVMENU_SEPARATOR') {
          $str .= "\n<div class='menuItemSep'></div>";
        } else if (count($subNavmenuItems) > 0) {
          $str .= <<<HEREDOC
<dt onclick="javascript:navmenuDisplayHide('menu$navmenuItemId');">
<div class='$strClassID'><a title='$description' $strNoDottedBorder href='#' class='no_style_menuItemText'>$anchor</a></div>
</dt>
<dd style="display: block;" id="menu$navmenuItemId">
HEREDOC;
          $str .= $this->getAccordionMenu($navmenuItemId);
          $str .= "\n</dd>";
        } else if ($url) {
          $strUrl = $this->templateUtils->renderPageUrl($url, $templateModelId);
          $str .= "\n<div class='$strClassID'><a href='$strUrl' $strNoDottedBorder $strTarget title='$description'>$anchor</a></div>";
        } else {
          $str .= "\n<div class='$strClassID'><div class='menuItemHdr'>$anchor</div></div>";
        }

        $str .= "\n</li>";
      }
    }

    $str .= "\n</ul>";

    return($str);
  }

  // Render the tags
  // When creating an element in the template system, it is necessary to create its tags
  // A dummy rendering is used for this operation
  function renderTags() {
    $str = "<div class='navmenu'></div>";
    $str .= "<div class='menuBar'></div>";
    $str .= "<div class='menuBarItem'></div>";
    $str .= "<div class='menuBarItemImg'></div>";
    $str .= "<div class='menu'></div>";
    $str .= "<div class='menuItem'></div>";
    $str .= "<div class='menuItemImg'></div>";
    $str .= "<div class='menuItemHdr'></div>";
    $str .= "<div class='menuItemSep'></div>";

    return($str);
  }

/*
  // Render the styling elements for the editing of the css style properties
  function renderStylingElements() {
    global $gImagesUserUrl;
    global $gStylingImage;

    $linkCategoryList = array('0' => 'A category');
    $strSelect = LibHtml::getSelectList("linkCategoryId", $linkCategoryList);

    $str = 
        "<div class='navmenu'>"
        . "<span class='menuBar'>"
          . "<span class='menuBarItem'>"
            . "<span class='menuBarItemImg'>"
            . "<img class='link_list_image_file' src='$gStylingImage' title='The image of the link' alt='' />"
            . "</span>"
          . "</span>"
          . "<span class='menuBarItem'>"
            . "<span class='menuBarItemImg'>"
            . "</span>"
          . "</span>"
        . "</div>"
        . "<div class='menu'>"
        . "</div>"
      . "</div>";
    $str = "<div class='navmenu'></div>";
    $str .= "<div class='menuBar'></div>";
    $str .= "<div class='menuBarItem'></div>";
    $str .= "<div class='menuBarItemImg'></div>";
    $str .= "<div class='menu'></div>";
    $str .= "<div class='menuItem'></div>";
    $str .= "<div class='menuItemImg'></div>";
    $str .= "<div class='menuItemHdr'></div>";
    $str .= "<div class='menuItemSep'></div>";

    return($str);
    }
 */
}

?>
