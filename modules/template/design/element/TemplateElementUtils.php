<?php

class TemplateElementUtils extends TemplateElementDB {

  var $mlText;

  // Some element types belong to a module and are available 
  // only if these modules are granted
  // while others are available whatsoever
  var $elementModules;

  var $properties;

  var $languageUtils;
  var $templateUtils;
  var $templateTagUtils;
  var $containerUtils;
  var $navmenuUtils;
  var $navbarUtils;
  var $navlinkUtils;
  var $dynpageUtils;
  var $lexiconEntryUtils;
  var $elearningLessonUtils;
  var $elearningExerciseUtils;
  var $flashUtils;
  var $rssFeedUtils;
  var $newsFeedUtils;
  var $adminModuleUtils;
  var $templateContainerUtils;
  var $templateElementLanguageUtils;
  var $templatePropertySetUtils;

  function TemplateElementUtils() {
    $this->TemplateElementDB();

    $this->init();
  }

  function init() {
    $this->elementModules = array(
      'USER_MINI_LOGIN' => 'MODULE_USER',
      'MAIL_REGISTRATION' => 'MODULE_MAIL',
      'SMS_NUMBER_REGISTRATION' => 'MODULE_SMS',
      'ELEARNING_SEARCH_LESSON' => 'MODULE_ELEARNING',
      'ELEARNING_SEARCH_EXERCISE' => 'MODULE_ELEARNING',
      'NEWS_FEED' => 'MODULE_NEWS',
      'NEWS_FEED_CYCLE' => 'MODULE_NEWS',
      'CLIENT_IMAGE_CYCLE' => 'MODULE_CLIENT',
      'LINK_IMAGE_CYCLE' => 'MODULE_LINK',
      'PHOTO_IMAGE_CYCLE' => 'MODULE_PHOTO',
    );
  }

  function getPropertyTypes() {
    $propertyTypes = array(
      'TEXT_ALIGNMENT',
      'VERTICAL_ALIGNMENT',
      'FLOAT',
      'CLEAR',
      'WIDTH',
      'HEIGHT',
      'MARGIN',
      'MARGIN_POSITION',
      'PADDING',
      'PADDING_POSITION',
      'BORDER_STYLE',
      'BORDER_SIZE',
      'BORDER_COLOR',
      'BORDER_POSITION',
      'ROUND_CORNER',
      'BACKGROUND_COLOR',
      'BACKGROUND_IMAGE',
      'BACKGROUND_REPEAT',
      'BACKGROUND_ATTACHMENT',
      'BACKGROUND_POSITION',
      'FONT_TYPE',
      'FONT_SIZE',
      'FONT_WEIGHT',
      'FONT_COLOR',
      'FONT_STYLE',
      'TEXT_DECORATION',
      'TEXT_TRANSFORM',
      'TEXT_INDENT',
      'LINE_HEIGHT',
      'WORD_SPACING',
      'LETTER_SPACING',
      'WHITE_SPACE',
      'DIRECTION',
      'IMAGE_BACKGROUND_COLOR',
      'LINK_COLOR',
      'LINK_TEXT_DECORATION',
      'LINK_HOVER_COLOR',
      'LINK_HOVER_TEXT_DECORATION',
      'LINK_HOVER_BACKGROUND_COLOR',
      'LINK_USED_COLOR',
    );

    return($propertyTypes);
  }

  function getElementTypes() {
    $this->loadLanguageTexts();

    $elementTypes = array(
      'PAGE' => array($this->mlText[11], $this->mlText[111]),
      'PAGE_NAME' => array($this->mlText[30], $this->mlText[31]),
      'NAVLINK' => array($this->mlText[13], $this->mlText[113]),
      'NAVBAR_HORIZONTAL' => array($this->mlText[9], $this->mlText[109]),
      'NAVBAR_VERTICAL' => array($this->mlText[10], $this->mlText[109]),
      'NAVMENU_HORIZONTAL' => array($this->mlText[22], $this->mlText[122]),
      'NAVMENU_ACCORDION' => array($this->mlText[33], $this->mlText[34]),
      'DYNPAGE_MENU' => array($this->mlText[120], $this->mlText[121]),
      'DYNPAGE_ACCORDION_MENU' => array($this->mlText[36], $this->mlText[37]),
      'DYNPAGE_TREE_MENU' => array($this->mlText[2], $this->mlText[102]),
      'DYNPAGE_BREADCRUMBS' => array($this->mlText[12], $this->mlText[112]),
      'SHOP_CATEGORY_MENU' => array($this->mlText[25], $this->mlText[26]),
      'SHOP_CATEGORY_ACCORDION_MENU' => array($this->mlText[39], $this->mlText[40]),
      'FLASH' => array($this->mlText[14], $this->mlText[114]),
      'CONTAINER' => array($this->mlText[8], $this->mlText[108]),
      'CLOCK_DATE' => array($this->mlText[6], $this->mlText[106]),
      'CLOCK_TIME' => array($this->mlText[7], $this->mlText[107]),
      'LANGUAGE' => array($this->mlText[1], $this->mlText[101]),
      'USER_MINI_LOGIN' => array($this->mlText[3], $this->mlText[103]),
      'SEARCH' => array($this->mlText[23], $this->mlText[24]),
      'SOCIAL_BUTTONS' => array($this->mlText[43], $this->mlText[44]),
      'MAIL_REGISTRATION' => array($this->mlText[4], $this->mlText[104]),
      'SMS_NUMBER_REGISTRATION' => array($this->mlText[32], $this->mlText[35]),
      'ELEARNING_SEARCH_LESSON' => array($this->mlText[45], $this->mlText[46]),
      'ELEARNING_SEARCH_EXERCISE' => array($this->mlText[47], $this->mlText[48]),
      'LEXICON_SEARCH' => array($this->mlText[0], $this->mlText[38]),
      'LAST_UPDATE' => array($this->mlText[123], $this->mlText[124]),
      'NEWS_FEED' => array($this->mlText[5], $this->mlText[105]),
      'NEWS_FEED_CYCLE' => array($this->mlText[41], $this->mlText[42]),
      'RSS_FEED' => array($this->mlText[125], $this->mlText[126]),
      'CLIENT_IMAGE_CYCLE' => array($this->mlText[17], $this->mlText[27]),
      'LINK_IMAGE_CYCLE' => array($this->mlText[20], $this->mlText[28]),
      'PHOTO_IMAGE_CYCLE' => array($this->mlText[21], $this->mlText[29]),
      'WEBSITE_ADDRESS' => array($this->mlText[15], $this->mlText[115]),
      'WEBSITE_TELEPHONE' => array($this->mlText[18], $this->mlText[118]),
      'WEBSITE_FAX' => array($this->mlText[19], $this->mlText[119]),
      'WEBSITE_COPYRIGHT' => array($this->mlText[16], $this->mlText[116]),
    );

    return($elementTypes);
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Add an element in a model container
  function addElement($templateContainerId, $elementType) {
    if ($elementType && $templateContainerId) {
      // Ask for a refresh of the cache
      $this->templateUtils->setRefreshCache();

      // Create the element
      $objectId = $this->createElementContent($elementType);

      // Get the next list order
      $listOrder = $this->getNextListOrder($templateContainerId);

      $templateElement = new TemplateElement();
      $templateElement->setElementType($elementType);
      $templateElement->setObjectId($objectId);
      $templateElement->setTemplateContainerId($templateContainerId);
      $templateElement->setListOrder($listOrder);
      $this->insert($templateElement);
      $templateElementId = $this->getLastInsertId();

      // Create the element tags
      $content = $this->renderContent($templateElementId, $elementType, $objectId);
      $tagIDs = $this->getTagIDs($templateElementId, $content);
      $this->createTags($templateElementId, $tagIDs);
    }
  }

  // Duplicate an element
  function duplicate($templateElement, $templateContainerId) {
    $objectId = $templateElement->getObjectId();
    $elementType = $templateElement->getElementType();

    if ($elementType == 'NEWS_FEED' || $elementType == 'NEWS_FEED_CYCLE' || $elementType == 'LINK_IMAGE_CYCLE' || $elementType == 'PHOTO_IMAGE_CYCLE' || $elementType == 'DYNPAGE_MENU' || $elementType == 'DYNPAGE_ACCORDION_MENU' || $elementType == 'DYNPAGE_TREE_MENU') {
      $lastInsertObjectId = $objectId;
    } else {
      $lastInsertObjectId = $this->duplicateElementContent($objectId, $elementType);
    }
    $listOrder = $this->getNextListOrder($templateContainerId);
    $templateElement->setListOrder($listOrder);
    $templateElement->setObjectId($lastInsertObjectId);
    $templateElement->setTemplateContainerId($templateContainerId);
    $this->insert($templateElement);
    $lastInsertTemplateElementId = $this->getLastInsertId();

    // The multi languages element types are duplicated after the new element has been created
    $this->duplicateAllLanguages($templateElement->getId(), $lastInsertTemplateElementId, $elementType);

    // Duplicate the tags
    $teplateTags = $this->templateTagUtils->selectByTemplateElementId($templateElement->getId());
    foreach ($teplateTags as $teplateTag) {
      $this->templateTagUtils->duplicate($teplateTag, $lastInsertTemplateElementId);
    }
  }

  // Export an element
  function exportXML(& $xmlNode, $templateElementId) {
    if ($templateElement = $this->selectById($templateElementId)) {
      $elementType = $templateElement->getElementType();
      $listOrder = $templateElement->getListOrder();
      $objectId = $templateElement->getObjectId();

      // The element content is not exported
      // Only its object id is, as it serves as a boolean to check if an object
      // needs to be created at import time
      $attributes = array("elementType" => $elementType, "listOrder" => $listOrder, "objectId" => $objectId);
      $xmlChildNode =& $xmlNode->addChild(TEMPLATE_ELEMENT, '', $attributes);

      // Export the tags
      $teplateTags = $this->templateTagUtils->selectByTemplateElementId($templateElementId);
      foreach ($teplateTags as $teplateTag) {
        $teplateTagId = $teplateTag->getId();
        $this->templateTagUtils->exportXML($xmlChildNode, $teplateTagId);
      }
    }
  }

  // Import an element
  function importXML($xmlNode, $lastInsertTemplateContainerId) {
    $elementType =& $xmlNode->attributes["elementType"];
    $listOrder =& $xmlNode->attributes["listOrder"];
    $objectId =& $xmlNode->attributes["objectId"];

    // Create the element
    $templateElement = new TemplateElement();
    $templateElement->setElementType($elementType);
    $templateElement->setListOrder($listOrder);
    $templateElement->setTemplateContainerId($lastInsertTemplateContainerId);

    // Create the element content object if any
    if ($objectId) {
      $objectId = $this->createElementContent($elementType);
      $templateElement->setObjectId($objectId);
    }
    $this->insert($templateElement);
    $lastInsertTemplateElementId = $this->getLastInsertId();

    // Create the element tag
    $xmlChildNodes =& $xmlNode->children;
    foreach ($xmlChildNodes as $xmlChildNode) {
      $name =& $xmlChildNode->name;
      if ($name == TEMPLATE_TAG) {
        $this->templateTagUtils->importXML($xmlChildNode, $lastInsertTemplateElementId);
      }
    }
  }

  // Create the element object if it is a multi instance one
  // Some element types have just one instance, others can have many
  function createElementContent($elementType) {
    $objectId = '';

    if ($elementType == 'CONTAINER') {
      $objectId = $this->containerUtils->add();
    } else if ($elementType == 'NAVMENU_HORIZONTAL' || $elementType == 'NAVMENU_ACCORDION') {
      $objectId = $this->navmenuUtils->add();
    } else if ($elementType == 'NAVBAR_HORIZONTAL' || $elementType == 'NAVBAR_VERTICAL') {
      $objectId = $this->navbarUtils->add();
    } else if ($elementType == 'NAVLINK') {
      $objectId = $this->navlinkUtils->add();
    } else if ($elementType == 'RSS_FEED') {
      $objectId = $this->rssFeedUtils->add();
    } else if ($elementType == 'FLASH') {
      $objectId = $this->flashUtils->add();
    } else if ($elementType == 'NEWS_FEED' || $elementType == 'NEWS_FEED_CYCLE' || $elementType == 'LINK_IMAGE_CYCLE' || $elementType == 'PHOTO_IMAGE_CYCLE' || $elementType == 'DYNPAGE_MENU' || $elementType == 'DYNPAGE_ACCORDION_MENU' || $elementType == 'DYNPAGE_TREE_MENU') {
      // Have it editable
      $objectId = true;
    } else if ($elementType == 'WEBSITE_ADDRESS') {
      // Have it editable
      $objectId = true;
    } else if ($elementType == 'WEBSITE_TELEPHONE') {
      // Have it editable
      $objectId = true;
    } else if ($elementType == 'WEBSITE_FAX') {
      // Have it editable
      $objectId = true;
    } else if ($elementType == 'WEBSITE_COPYRIGHT') {
      // Have it editable
      $objectId = true;
    }

    return($objectId);
  }

  // Duplicate the element object if it is a multi instance one
  function duplicateAllLanguages($templateElementId, $lastInsertTemplateElementId, $elementType) {
    if ($elementType == 'NEWS_FEED' || $elementType == 'NEWS_FEED_CYCLE' || $elementType == 'LINK_IMAGE_CYCLE' || $elementType == 'PHOTO_IMAGE_CYCLE' || $elementType == 'DYNPAGE_MENU' || $elementType == 'DYNPAGE_ACCORDION_MENU' || $elementType == 'DYNPAGE_TREE_MENU') {
      $this->templateElementLanguageUtils->duplicateAllLanguages($templateElementId, $lastInsertTemplateElementId);
    }
  }

  // Duplicate the element object if it is a multi instance one
  function duplicateElementContent($objectId, $elementType) {
    $lastInsertObjectId = '';

    if ($elementType == 'CONTAINER') {
      $lastInsertObjectId = $this->containerUtils->duplicate($objectId);
    } else if (($elementType == 'NAVMENU_HORIZONTAL') || ($elementType == 'NAVMENU_ACCORDION')) {
      $lastInsertObjectId = $this->navmenuUtils->duplicate($objectId);
    } else if ($elementType == 'NAVBAR_HORIZONTAL' || $elementType == 'NAVBAR_VERTICAL') {
      $lastInsertObjectId = $this->navbarUtils->duplicate($objectId);
    } else if ($elementType == 'RSS_FEED') {
      $lastInsertObjectId = $this->rssFeedUtils->duplicate($objectId);
    } else if ($elementType == 'NAVLINK') {
      $lastInsertObjectId = $this->navlinkUtils->duplicate($objectId);
    } else if ($elementType == 'FLASH') {
      $lastInsertObjectId = $this->flashUtils->duplicate($objectId);
    } else if ($elementType == 'WEBSITE_ADDRESS') {
      // Have it editable
      $lastInsertObjectId = true;
    } else if ($elementType == 'WEBSITE_TELEPHONE') {
      // Have it editable
      $lastInsertObjectId = true;
    } else if ($elementType == 'WEBSITE_FAX') {
      // Have it editable
      $lastInsertObjectId = true;
    } else if ($elementType == 'WEBSITE_COPYRIGHT') {
      // Have it editable
      $lastInsertObjectId = true;
    }

    return($lastInsertObjectId);
  }

  function deleteElementContent($elementType, $objectId) {
    // Ask for a refresh of the cache
    $this->templateUtils->setRefreshCache();

    if ($elementType == 'CONTAINER') {
      $this->containerUtils->deleteContainer($objectId);
    } else if ($elementType == 'NAVMENU_HORIZONTAL' || $elementType == 'NAVMENU_ACCORDION') {
      $this->navmenuUtils->deleteNavmenu($objectId);
    } else if ($elementType == 'NAVBAR_HORIZONTAL' || $elementType == 'NAVBAR_VERTICAL') {
      $this->navbarUtils->deleteNavbar($objectId);
    } else if ($elementType == 'RSS_FEED') {
      $this->rssFeedUtils->deleteRssFeed($objectId);
    } else if ($elementType == 'NAVLINK') {
      $this->navlinkUtils->deleteNavlink($objectId);
    } else if ($elementType == 'FLASH') {
      $this->flashUtils->delete($objectId);
    }
  }

  function deleteElementLanguages($templateElementId, $elementType) {
    // Ask for a refresh of the cache
    $this->templateUtils->setRefreshCache();

    if ($elementType == 'NEWS_FEED' || $elementType == 'NEWS_FEED_CYCLE' || $elementType == 'LINK_IMAGE_CYCLE' || $elementType == 'PHOTO_IMAGE_CYCLE' || $elementType == 'DYNPAGE_MENU' || $elementType == 'DYNPAGE_ACCORDION_MENU' || $elementType == 'DYNPAGE_TREE_MENU') {
      $this->templateElementLanguageUtils->deleteAllLanguages($templateElementId);
    }
  }

  // Get the url of the page to edit the content of an element that
  // can have several object instances
  function getEditContentUrl($templateElementId, $elementType, $objectId) {
    global $gTemplateDesignUrl;
    global $gNavbarUrl;
    global $gNavlinkUrl;
    global $gNavmenuUrl;
    global $gContainerUrl;
    global $gFlashUrl;
    global $gNewsUrl;
    global $gProfileUrl;
    global $gPhotoUrl;
    global $gLinkUrl;
    global $gRssUrl;

    // Ask for a refresh of the cache
    $this->templateUtils->setRefreshCache();

    $url = '';

    if ($elementType == 'CONTAINER') {
      $url = "$gContainerUrl/edit_content.php?containerId=$objectId";
    } else if ($elementType == 'NAVMENU_HORIZONTAL' || $elementType == 'NAVMENU_ACCORDION') {
      $url = "$gNavmenuUrl/admin.php?navmenuId=$objectId";
    } else if ($elementType == 'NAVBAR_HORIZONTAL' || $elementType == 'NAVBAR_VERTICAL') {
      $url = "$gNavbarUrl/admin.php?navbarId=$objectId";
    } else if ($elementType == 'NEWS_FEED' || $elementType == 'NEWS_FEED_CYCLE' || $elementType == 'LINK_IMAGE_CYCLE' || $elementType == 'PHOTO_IMAGE_CYCLE' || $elementType == 'DYNPAGE_MENU' || $elementType == 'DYNPAGE_ACCORDION_MENU' || $elementType == 'DYNPAGE_TREE_MENU') {
      $url = "$gTemplateDesignUrl/element/admin.php?templateElementId=$templateElementId";
    } else if ($elementType == 'RSS_FEED') {
      $url = "$gRssUrl/admin.php?rssFeedId=$objectId";
    } else if ($elementType == 'NAVLINK') {
      $url = "$gNavlinkUrl/admin.php?navlinkId=$objectId";
    } else if ($elementType == 'FLASH') {
      $url = "$gFlashUrl/edit.php?flashId=$objectId";
    } else if ($elementType == 'WEBSITE_ADDRESS') {
    } else if ($elementType == 'WEBSITE_TELEPHONE') {
    } else if ($elementType == 'WEBSITE_FAX') {
    } else if ($elementType == 'WEBSITE_COPYRIGHT') {
    }

    return($url);
  }

  // Get the element description
  function getDescription($element) {
    $elementTypes = $this->getElementTypes();

    if (array_key_exists($element, $elementTypes)) {
      return($elementTypes[$element][0]);
    }
  }

  // Get the element help text
  function getHelp($element) {
    $elementTypes = $this->getElementTypes();

    if (array_key_exists($element, $elementTypes)) {
      return($elementTypes[$element][1]);
    }
  }

  // Get the list of element types
  function getGrantedElements() {
    $grantedElementTypes = array();

    $elementTypes = $this->getElementTypes();

    // Get the granted modules for the current website
    $moduleNames = $this->adminModuleUtils->getLoggedAdminModules();

    $orderedElementTypes = array(' ' => '');
    foreach ($elementTypes as $elementKey => $element) {
      array_push($orderedElementTypes, $elementKey);
    }

    foreach ($orderedElementTypes as $elementType) {
      if (array_key_exists($elementType, $this->elementModules)) {
        $elementModule = $this->elementModules[$elementType];
        if (in_array($elementModule, $moduleNames)) {
          $grantedElementTypes[$elementType] = $this->getDescription($elementType);
        }
      } else {
        $grantedElementTypes[$elementType] = $this->getDescription($elementType);
      }
    }

    return($grantedElementTypes);
  }

  // Get the next available list order
  function getNextListOrder($templateContainerId) {
    $listOrder = 1;

    if ($templateElements = $this->selectByTemplateContainerId($templateContainerId)) {
      $total = count($templateElements);
      if ($total > 0) {
        $templateElement = $templateElements[$total - 1];
        $listOrder = $templateElement->getListOrder() + 1;
      }
    }

    return($listOrder);
  }

  // Get the next object
  function selectNext($id) {
    if ($templateElement = $this->selectById($id)) {
      $listOrder = $templateElement->getListOrder();
      $templateContainerId = $templateElement->getTemplateContainerId();
      if ($templateElement = $this->selectByNextListOrder($templateContainerId, $listOrder)) {
        return($templateElement);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($templateElement = $this->selectById($id)) {
      $listOrder = $templateElement->getListOrder();
      $templateContainerId = $templateElement->getTemplateContainerId();
      if ($templateElement = $this->selectByPreviousListOrder($templateContainerId, $listOrder)) {
        return($templateElement);
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
    if ($templateElement = $this->selectById($id)) {
      $listOrder = $templateElement->getListOrder();
      $templateContainerId = $templateElement->getTemplateContainerId();
      if ($templateElements = $this->selectByListOrder($templateContainerId, $listOrder)) {
        if (($listOrder == 0) || (count($templateElements)) > 1) {
          $this->resetListOrder($templateContainerId);
        }
      }
    }
  }

  function getTemplateModelId($templateElementId) {
    $templateModelId = '';

    if ($templateElement = $this->selectById($templateElementId)) {
      $templateContainerId = $templateElement->getTemplateContainerId();
      $templateModelId = $this->templateContainerUtils->getTemplateModelId($templateContainerId);
    }

    return($templateModelId);
  }

  // Hide or show an element
  function showHideElement($templateElementId) {
    if ($templateElementId) {
      if ($templateElement = $this->selectById($templateElementId)) {
        $hide = $templateElement->getHide();
        if ($hide) {
          $templateElement->setHide(false);
        } else {
          $templateElement->setHide(true);
        }
        $this->update($templateElement);
      }
    }
  }

  // Delete an element and all its tags
  function deleteElement($templateElementId) {
    if ($templateElementId) {
      // Delete all the element tags
      if ($templateTags = $this->templateTagUtils->selectByTemplateElementId($templateElementId)) {
        foreach ($templateTags as $templateTag) {
          $templateTagId = $templateTag->getId();
          $this->templateTagUtils->deleteTemplateTag($templateTagId);
        }
      }

      // Delete the element content if any
      if ($templateElement = $this->selectById($templateElementId)) {
        $elementType = $templateElement->getElementType();
        $objectId = $templateElement->getObjectId();

        if ($elementType == 'NEWS_FEED' || $elementType == 'NEWS_FEED_CYCLE' || $elementType == 'LINK_IMAGE_CYCLE' || $elementType == 'PHOTO_IMAGE_CYCLE' || $elementType == 'DYNPAGE_MENU' || $elementType == 'DYNPAGE_ACCORDION_MENU' || $elementType == 'DYNPAGE_TREE_MENU') {
          $this->deleteElementLanguages($templateElementId, $elementType);
        } else {
          $this->deleteElementContent($elementType, $objectId);
        }
      }

      // Delete the element
      $this->delete($templateElementId);
    }
  }

  // Create the tags for an element
  function createTags($templateElementId, $tagIDs) {
    // Create a template tag and a template property set for each property id
    if (is_array($tagIDs)) {    
      foreach ($tagIDs as $tagID) {
        if (!$templateTag = $this->templateTagUtils->selectByTemplateElementIdAndTagID($templateElementId, $tagID)) {
          // Create the property set
          $templatePropertySetId = $this->templatePropertySetUtils->createPropertySet();

          $templateTag = new TemplateTag();
          $templateTag->setTemplateElementId($templateElementId);
          $templateTag->setTagID($tagID);
          $templateTag->setTemplatePropertySetId($templatePropertySetId);
          $this->templateTagUtils->insert($templateTag);
          $templateTagId = $this->templateTagUtils->getLastInsertId();
        }
      }

      // Delete the tag ids not used any longer
      // This should be rarely used but is necessary if modifying
      // the html source code in the rendering functions and
      // removing tag ids
      if ($templateTags = $this->templateTagUtils->selectByTemplateElementId($templateElementId)) {
        foreach ($templateTags as $templateTag) {
          $templateTagId = $templateTag->getId();
          $tagID = $templateTag->getTagID();
          if (!in_array($tagID, $tagIDs)) {
            $this->templateTagUtils->delete($templateTagId);
          }
        }
      }
    }
  }

  // Parse the content of the element output
  // to extract the div tag class ids
  function parseContent($content) {
    // Pattern matching
    // one class
    // followed by zero or more spaces
    // followed by one =
    // followed by zero or more spaces
    // followed by one ' or "
    // followed by anything except \S (white space) and ' and " (use parenthese to store this pattern)
    // followed by one ' or "
    // ignoring the case
    $matches = array();
    if (preg_match_all("/class *?= *?['|\"]([\S][^>][^']*?)['|\"]/i", $content, $matches)) {
      $matches = $matches[0];
    }

    $tagIDs = array();

    if (count($matches) > 0) {
      for ($i = 0; $i < count($matches); $i++) {
        $strId = $matches[$i];

        if (!is_array($strId)) {
          // Some tags are not to be styled
          if (strstr($strId, 'no_style')) {
            continue;
          }

          $strId = str_replace("class=", '', $strId);
          $strId = str_replace("'", '', $strId);
          $strId = str_replace('"', '', $strId);

          // There may be more than one class name 
          // like for the round corner additional class name
          $strId = LibString::wordSubtract($strId, 1);

          // Avoid tags with blank spaces
          if (!strstr($strId, ' ')) {
            array_push($tagIDs, $strId);
          }
        }
      }
    }

    // Remove the double values
    $tagIDs = array_unique($tagIDs);

    return($tagIDs);
  }

  // Get the tag ids for an element
  function getTagIDs($templateElementId, $content) {
    if ($templateElementId) {
      if ($templateElement = $this->selectById($templateElementId)) {
        // Update the non cached content
        $content = $this->templateUtils->updateContent($content);

        // Extract the list of tags from the content
        if ($content) {
          $tagIDs = $this->parseContent($content);

          return($tagIDs);
        }
      }
    }
  }

  // Get the tag ids of the element
  function NOT_USED_getTagIDs($templateElementId) {
    $tagIDs = array();

    if ($templateElementId) {
      if ($templateTags = $this->templateTagUtils->selectByTemplateElementId($templateElementId)) {
        foreach ($templateTags as $templateTag) {
          $tagID = $templateTag->getTagID();
          array_push($tagIDs, $tagID);
        }
      }
    }

    return($tagIDs);
  }

  // Insert a call to a javascript function to mark
  // and edit to properties of a specified tag of the content
  function insertJsEditor($content, $tagID, $url) {
    // Pattern matching
    // one class
    // followed by zero or more spaces
    // followed by one =
    // followed by zero or more spaces
    // followed by one ' or "
    // followed by the class id and ' or "
    // ignoring the case
    if (preg_match_all("/class *?= *?['|\"](" . $tagID. ")['|\"]/i", $content, $matches)) {
      $matches = $matches[0];
      if (count($matches) > 0) {
        $strClass = $matches[0];

        $strId = str_replace("class=", "id=", $strClass);

        $jsEditor = "onMouseOver=\"javascript:underline(event);\" onMouseOut=\"javascript:reset(this);\" onclick=\"window.open('$url', '_blank'); event.cancelBubble = true; return(false);\"";

        $content = str_replace($strClass, "$jsEditor $strId $strClass", $content);
      }
    }

    return($content);
  }

  // Prepare the content for the style editor
  function prepareContent($content) {
    // Prevent the page to redirect when clicking on a link to edit its style
    $content = $this->removeLinkTags($content);

    // Prevent the page to redirect when clicking on an <input > tag to edit its style
    $content = $this->inputToImgTags($content);

    // Prevent the underlining of the table td tags in the style editor
    $content = $this->hideTDTags($content);

    return($content);
  }

  // Remove the links in the content
  // This prevents the page to redirect when clicking on a link to edit its style
  function removeLinkTags($content) {
    // Pattern matching
    // one <a
    // followed by anything or nothing except >
    // followed by >
    // followed by anything or nothing (note the use of the ungreedy modifier ?)
    // followed by </a>
    // ignoring the case
    if (preg_match_all("/(<a[^>]*>)(.*?)(<\/a>)/i", $content, $allMatches)) {
      $matches = $allMatches[1];
      if (isset($allMatches[3])) {
        $matches = array_merge($matches, $allMatches[3]);
      }
      if (count($matches) > 0) {
        foreach ($matches as $match) {
          $content = str_replace($match, '', $content);
        }
      }
    }

    return($content);
  }

  // Transform the input tags like <input > into <img > tags in the content
  // This prevents the page to redirect when clicking on an input tag to edit its style
  function inputToImgTags($content) {

    $content = str_replace("<input type='image'", "<img", $content);

    return($content);
  }

  // Render the javascript callback functions used in the editor
  function insertJsEditorCallback() {
    $str = <<<HEREDOC
<script type='text/javascript'>
function onRemoteUnderline(element) {
  element.style.border = '1px dashed #CC0000';
  }

function onReset(element) {
  element.style.border = '';
  }

var formerElement;
function underline(e) {
  theObj = (e.srcElement)?e.srcElement:e.target;
  if (formerElement) {
    formerElement.style.border = '';
    }
  theObj.style.border = '1px dashed #CC0000';
  formerElement = theObj;
  e.cancelBubble = true;
  status = theObj.id;
  }

</script>
HEREDOC;

    return($str);
  }

  // Render the header (DHTML javascript code) of an element if any
  // Most of the element types do not have any
  function renderHeader($elementType, $objectId = '') {
    $str = '';

    if ($elementType == 'NAVMENU_HORIZONTAL' || $elementType == 'DYNPAGE_MENU' || $elementType == 'SHOP_CATEGORY_MENU') {
      $str = $this->navmenuUtils->renderHeader();
    } else if ($elementType == 'NAVMENU_ACCORDION' || $elementType == 'DYNPAGE_ACCORDION_MENU' || $elementType == 'SHOP_CATEGORY_ACCORDION_MENU') {
      $str = $this->navmenuUtils->renderAccordionMenuHeader();
    } else if ($elementType == 'DYNPAGE_TREE_MENU') {
      $str = $this->dynpageUtils->renderDirectoryTreeHeader();
    } else if ($elementType == 'NEWS_FEED' || $elementType == 'NEWS_FEED_CYCLE') {
      $str = $this->newsFeedUtils->renderHeader();
    } else if ($elementType == 'LEXICON_SEARCH') {
      $str = $this->lexiconEntryUtils->renderHeader();
    }

    return($str);
  }

  // Render the content of an element
  // Some element types can have several object instances
  function renderContent($templateElementId, $elementType, $objectId = '') {
    $str = '';

    if ($elementType == 'PAGE') {
      $str .= "TEMPLATE_CONTENT_PAGE";
    } else if ($elementType == 'LANGUAGE') {
      $str .= "TEMPLATE_CONTENT_LANGUAGE_BAR";
    } else if ($elementType == 'SHOP_CATEGORY_MENU') {
      $str .= "TEMPLATE_CONTENT_SHOP_CATEGORY_MENU";
    } else if ($elementType == 'SHOP_CATEGORY_ACCORDION_MENU') {
      $str .= "TEMPLATE_CONTENT_SHOP_CATEGORY_ACCORDION_MENU";
    } else if ($elementType == 'DYNPAGE_BREADCRUMBS') {
      $str .= "TEMPLATE_CONTENT_DYNPAGE_BREADCRUMBS";
    } else if ($elementType == 'SEARCH') {
      $str .= "TEMPLATE_CONTENT_SEARCH";
    } else if ($elementType == 'SOCIAL_BUTTONS') {
      $str .= "TEMPLATE_SOCIAL_BUTTONS";
    } else if ($elementType == 'USER_MINI_LOGIN') {
      $str .= "TEMPLATE_CONTENT_USER_MINI_LOGIN";
    } else if ($elementType == 'MAIL_REGISTRATION') {
      $str .= "TEMPLATE_CONTENT_MAIL_REGISTRATION";
    } else if ($elementType == 'SMS_NUMBER_REGISTRATION') {
      $str .= "TEMPLATE_CONTENT_SMS_REGISTRATION";
    } else if ($elementType == 'LAST_UPDATE') {
      $str .= "TEMPLATE_CONTENT_LAST_UPDATE";
    } else if ($elementType == 'RSS_FEED') {
      $str .= "TEMPLATE_CONTENT_RSS_FEED_$objectId";
    } else if ($elementType == 'CLOCK_DATE') {
      $str .= "TEMPLATE_CONTENT_CLOCK_DATE";
    } else if ($elementType == 'CLOCK_TIME') {
      $str .= "TEMPLATE_CONTENT_CLOCK_TIME";
    } else if ($elementType == 'WEBSITE_ADDRESS') {
      $str .= "TEMPLATE_CONTENT_WEBSITE_ADDRESS";
    } else if ($elementType == 'WEBSITE_TELEPHONE') {
      $str .= "TEMPLATE_CONTENT_WEBSITE_TELEPHONE";
    } else if ($elementType == 'WEBSITE_FAX') {
      $str .= "TEMPLATE_CONTENT_WEBSITE_FAX";
    } else if ($elementType == 'WEBSITE_COPYRIGHT') {
      $str .= "TEMPLATE_CONTENT_WEBSITE_COPYRIGHT";
    } else if ($elementType == 'PAGE_NAME') {
      $str .= "TEMPLATE_CONTENT_CURRENT_PAGE_NAME";
    } else if ($elementType == 'CLIENT_IMAGE_CYCLE') {
      $str .= "TEMPLATE_CONTENT_CLIENT_IMAGE_CYCLE";
    } else if ($elementType == 'ELEARNING_SEARCH_LESSON') {
      $str .= $this->elearningLessonUtils->renderSearch();
    } else if ($elementType == 'ELEARNING_SEARCH_EXERCISE') {
      $str .= $this->elearningExerciseUtils->renderSearch();
    } else if ($elementType == 'LEXICON_SEARCH') {
      $str .= $this->lexiconEntryUtils->renderLexiconSearch();
    } else if ($elementType == 'NEWS_FEED' || $elementType == 'NEWS_FEED_CYCLE' || $elementType == 'LINK_IMAGE_CYCLE' || $elementType == 'PHOTO_IMAGE_CYCLE' || $elementType == 'DYNPAGE_MENU' || $elementType == 'DYNPAGE_ACCORDION_MENU' || $elementType == 'DYNPAGE_TREE_MENU') {
      $str .= "TEMPLATE_CONTENT_LANGUAGE_$templateElementId";
    } else if ($elementType == 'CONTAINER') {
      $str .= $this->containerUtils->render($objectId);
      if (!$str) {
        $str .= $this->containerUtils->renderTags();
      }
    } else if ($elementType == 'NAVMENU_HORIZONTAL') {
      $str .= $this->navmenuUtils->render($objectId);
      if (!$str) {
        $str .= $this->navmenuUtils->renderTags();
      }
    } else if ($elementType == 'NAVMENU_ACCORDION') {
      $str .= $this->navmenuUtils->renderAccordionMenu($objectId);
      if (!$str) {
        $str .= $this->navmenuUtils->renderTags();
      }
    } else if ($elementType == 'NAVBAR_HORIZONTAL') {
      $str .= $this->navbarUtils->renderHorizontal($objectId);
      if (!$str) {
        $str .= $this->navbarUtils->renderTags();
      }
    } else if ($elementType == 'NAVBAR_VERTICAL') {
      $str .= $this->navbarUtils->renderVertical($objectId);
      if (!$str) {
        $str .= $this->navbarUtils->renderTags();
      }
    } else if ($elementType == 'NAVLINK') {
      $str .= $this->navlinkUtils->render($objectId);
      if (!$str) {
        $str .= $this->navlinkUtils->renderTags();
      }
    } else if ($elementType == 'FLASH') {
      $str .= $this->flashUtils->render($objectId);
      if (!$str) {
        $str .= $this->flashUtils->renderTags();
      }
    }

    $containerRoundCornerClass = $this->renderRoundCornerClass($templateElementId);
    $str = "<div class='template_element $containerRoundCornerClass'>" . $str . "</div>";

    return($str);
  }

  // Render the tag id
  // The tag id must be unique for each model/container/element
  function renderTagID($templateElementId) {
    $str = '';

    if ($templateElementId) {
      if ($templateElement = $this->selectById($templateElementId)) {
        $templateContainerId = $templateElement->getTemplateContainerId();
        if ($templateContainer = $this->templateContainerUtils->selectById($templateContainerId)) {
          $templateModelId = $templateContainer->getTemplateModelId();

          // A tag id must start with a letter
          $str = "ID_M_"
            . $templateModelId
            . "_C_"
            . $templateContainerId
            . "_E_"
            . $templateElementId;
        }
      }
    }

    return($str);
  }

  // Render the class id for some round corners if any
  function renderRoundCornerClass($templateElementId) {
    $value = '';

    if ($templateElementId) {
      if ($templateTags = $this->templateTagUtils->selectByTemplateElementId($templateElementId)) {
        foreach ($templateTags as $templateTag) {
          $templateTagId = $templateTag->getId();
          $templatePropertySetId = $templateTag->getTemplatePropertySetId();
          $properties = $this->templatePropertySetUtils->getProperties($templatePropertySetId);
          if (isset($properties["ROUND_CORNER"])) {
            $value = $properties["ROUND_CORNER"];
            return($value);
          }
        }
      }
    }
  }

  // Render the element properties for an html output
  function renderHtmlProperties($templateElementId, $absolutTagID = false) {
    $str = '';

    if ($templateElementId) {
      if ($templateTags = $this->templateTagUtils->selectByTemplateElementId($templateElementId)) {
        foreach ($templateTags as $templateTag) {
          $templateTagId = $templateTag->getId();

          $strProperties = $this->templateTagUtils->renderHtmlProperties($templateTagId, $absolutTagID);
          if ($strProperties) {
            $str .= "\n" . $strProperties;
          }
        }
      }
    }

    return($str);
  }

  // Prevent the underlining of the table td tags in the style editor
  // This stops the table td tags from bubbling up the javascript event
  function hideTDTags($content) {
    // Pattern matching
    // one <td
    // followed by zero or more blank spaces
    // followed by >
    // ignoring the case
    if (preg_match_all("/(<td)( *?>)/i", $content, $allMatches)) {
      $openings = $allMatches[1];
      $closings = $allMatches[2];
      if (count($openings) > 0) {
        for ($i = 0; $i < count($openings); $i++) {
          $opening = $openings[$i];
          $closing = $closings[$i];
          $content = str_replace("$opening$closing", "$opening onMouseOver='event.cancelBubble = true;'$closing", $content);
  }
  }
  }

  return($content);
  }

  }

?>
