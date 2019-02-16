<?php

class TemplatePageUtils extends TemplatePageDB {

  var $mlText;

  // The subset of system pages that contain tag ids
  var $styledPages;

  var $languageUtils;
  var $templateUtils;
  var $templateElementUtils;
  var $templatePageTagUtils;
  var $templatePropertySetUtils;

  function TemplatePageUtils() {
    $this->TemplatePageDB();

    $this->init();
  }

  function init() {
    $this->styledPages = array(
      'SYSTEM_PAGE_DYNPAGE',
      'SYSTEM_PAGE_ELEARNING_EXERCISE',
      'SYSTEM_PAGE_ELEARNING_LESSON',
      'SYSTEM_PAGE_ELEARNING_RESULT',
      'SYSTEM_PAGE_ELEARNING_PARTICIPANTS',
      'SYSTEM_PAGE_ELEARNING_LIST_TEACHERS',
      'SYSTEM_PAGE_ELEARNING_SUBSCRIPTIONS',
      'SYSTEM_PAGE_NEWSSTORY',
      'SYSTEM_PAGE_NEWSPAPER',
      'SYSTEM_PAGE_NEWSPAPER_LIST',
      'SYSTEM_PAGE_NEWSPUBLICATION_LIST',
      'SYSTEM_PAGE_FORM',
      'SYSTEM_PAGE_SHOP_CATEGORY_LIST',
      'SYSTEM_PAGE_SHOP_ITEM',
      'SYSTEM_PAGE_SHOP_ORDER_LIST',
      'SYSTEM_PAGE_CLIENT_LIST',
      'SYSTEM_PAGE_CLIENT_CYCLE',
      'SYSTEM_PAGE_GUESTBOOK_LIST',
      'SYSTEM_PAGE_PEOPLE_LIST',
      'SYSTEM_PAGE_PEOPLE_ITEM',
      'SYSTEM_PAGE_LINK_LIST',
      'SYSTEM_PAGE_LINK_CYCLE',
      'SYSTEM_PAGE_PHOTO_ITEM',
      'SYSTEM_PAGE_PHOTO_SEARCH',
      'SYSTEM_PAGE_PHOTO_LIST',
      'SYSTEM_PAGE_PHOTO_CYCLE',
      'SYSTEM_PAGE_PHOTO_ALBUM_LIST',
      'SYSTEM_PAGE_DOCUMENT_LIST',
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
      'LINK_COLOR',
      'LINK_TEXT_DECORATION',
      'LINK_HOVER_COLOR',
      'LINK_HOVER_TEXT_DECORATION',
      'LINK_USED_COLOR',
    );

    return($propertyTypes);
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Add a page in a model
  function add($templateModelId, $systemPage) {
    if ($systemPage && $templateModelId) {
      if (!$templatePage = $this->selectByTemplateModelIdAndSystemPage($templateModelId, $systemPage)) {
        $templatePage = new TemplatePage();
        $templatePage->setSystemPage($systemPage);
        $templatePage->setTemplateModelId($templateModelId);
        $this->insert($templatePage);
        $templatePageId = $this->getLastInsertId();

        // Create the page tags
        $content = $this->templatePageTagUtils->renderSystemPageContent($systemPage);
        $tagIDs = $this->getTagIDs($templatePageId, $content);
        $this->createTags($templatePageId, $tagIDs);

        return($templatePageId);
      }
    }
  }

  // Export
  function exportXML($xmlNode, $templatePageId) {
    if ($templatePage = $this->selectById($templatePageId)) {
      $systemPage = $templatePage->getSystemPage();

      // The element content is not exported
      // Only its object id is, as it serves as a boolean to check if an object
      // needs to be created at import time
      $xmlChildNode = $xmlNode->addChild(TEMPLATE_PAGE);
      $attributes = array("systemPage" => $systemPage);
      if (is_array($attributes)) {
        foreach ($attributes as $aName => $aValue) {
          $xmlChildNode->addAttribute($aName, $aValue);
        }
      }

      // Export the tags
      $teplatePageTags = $this->templatePageTagUtils->selectByTemplatePageId($templatePageId);
      foreach ($teplatePageTags as $teplatePageTag) {
        $teplatePageTagId = $teplatePageTag->getId();
        $this->templatePageTagUtils->exportXML($xmlChildNode, $teplatePageTagId);
      }
    }
  }

  // Import
  function importXML($xmlNode, $lastInsertTemplateModelId) {
    $systemPage = $xmlNode->attributes()["systemPage"];

    // Create the element
    $templatePage = new TemplatePage();
    $templatePage->setSystemPage($systemPage);
    $templatePage->setTemplateModelId($lastInsertTemplateModelId);

    $this->insert($templatePage);
    $lastInsertTemplatePageId = $this->getLastInsertId();

    // Create the element tag
    $xmlChildNodes = $xmlNode->children();
    foreach ($xmlChildNodes as $xmlChildNode) {
      $name = $xmlChildNode->getName();
      if ($name == TEMPLATE_PAGE_TAG) {
        $this->templatePageTagUtils->importXML($xmlChildNode, $lastInsertTemplatePageId);
      }
    }
  }

  // Duplicate a system page properties
  function duplicate($templatePage, $templateModelId) {
    $templatePage->setTemplateModelId($templateModelId);
    $this->insert($templatePage);
    $lastInsertTemplatePageId = $this->getLastInsertId();

    // Duplicate the tags
    $teplatePageTags = $this->templatePageTagUtils->selectByTemplatePageId($templatePage->getId());
    foreach ($teplatePageTags as $teplatePageTag) {
      $this->templatePageTagUtils->duplicate($teplatePageTag, $lastInsertTemplatePageId);
    }
  }

  // Get the description
  function getDescription($element) {
    if (array_key_exists($element, $this->elements)) {
      return($this->elements[$element][0]);
    }
  }

  // Get the element help text
  function getHelp($element) {
    if (array_key_exists($element, $this->elements)) {
      return($this->elements[$element][1]);
    }
  }

  // Get the list of pages
  function getPageList() {
    $this->loadLanguageTexts();

    $systemPages = $this->templateUtils->getSystemPages(true);

    $list = array();
    foreach ($this->styledPages as $key) {
      if (isset($systemPages[$key])) {
        $list[$key] = $systemPages[$key];
      }
    }

    // Add an entry for all the other system pages
    $list['SYSTEM_PAGE'] = $this->mlText[0];

    return($list);
  }

  // Delete a page from a model
  function deleteTemplatePage($templatePageId) {
    if ($templatePageId) {
      // Delete all the element tags
      if ($templatePageTags = $this->templatePageTagUtils->selectByTemplatePageId($templatePageId)) {
        foreach ($templatePageTags as $templatePageTag) {
          $templatePageTagId = $templatePageTag->getId();
          $this->templatePageTagUtils->deleteTemplatePageTag($templatePageTagId);
        }
      }

      // Delete the element content if any
      if ($templatePage = $this->selectById($templatePageId)) {
        $systemPage = $templatePage->getSystemPage();
      }

      // Delete the element
      $this->delete($templatePageId);
    }
  }

  // Create the tags for a page
  function createTags($templatePageId, $tagIDs) {
    // Create a template tag and a template property set for each tag id
    if (is_array($tagIDs)) {
      foreach ($tagIDs as $tagID) {
        if (!$templatePageTag = $this->templatePageTagUtils->selectByTemplatePageIdAndTagID($templatePageId, $tagID)) {
          // Create the property set
          $templatePropertySetId = $this->templatePropertySetUtils->createPropertySet();

          $templatePageTag = new TemplatePageTag();
          $templatePageTag->setTemplatePageId($templatePageId);
          $templatePageTag->setTagID($tagID);
          $templatePageTag->setTemplatePropertySetId($templatePropertySetId);
          $this->templatePageTagUtils->insert($templatePageTag);
          $templatePageTagId = $this->templatePageTagUtils->getLastInsertId();
        }
      }
    }
  }

  // Delete the tags of a page
  function deleteTags($templatePageId) {
    if ($templatePageId) {
      if ($templatePageTags = $this->templatePageTagUtils->selectByTemplatePageId($templatePageId)) {
        foreach ($templatePageTags as $templatePageTag) {
          $templatePageTagId = $templatePageTag->getId();
          $this->templatePageTagUtils->deleteTemplatePageTag($templatePageTagId);
        }
      }
    }
  }

  // Get the tag ids for a page
  function getTagIDs($templatePageId, $content) {
    if ($templatePageId) {
      if ($templatePage = $this->selectById($templatePageId)) {
        // Update the non cached content
        $content = $this->templateUtils->updateContent($content);

        // Extract the list of tags from the content
        if ($content) {
          $tagIDs = $this->templateElementUtils->parseContent($content);

          return($tagIDs);
        }
      }
    }
  }

  // Render the tag id
  // The tag id must be unique for each model/page
  function NOT_USED_renderTagID($templatePageId) {
    $str = '';

    if ($templatePageId) {
      if ($templatePage = $this->selectById($templatePageId)) {
        $templateModelId = $templatePage->getTemplateModelId();

        // A tag id must start with a letter
        $str = "ID_M_"
          . $templateModelId
          . "_PG_"
          . $templatePageId;
      }
    }

    return($str);
  }

  // Render the element properties for an html output
  function renderHtmlProperties($templatePageId, $absolutTagID = false) {
    $str = '';

    if ($templatePageId) {
      if ($templatePageTags = $this->templatePageTagUtils->selectByTemplatePageId($templatePageId)) {
        foreach ($templatePageTags as $templatePageTag) {
          $templatePageTagId = $templatePageTag->getId();
          $strProperties = $this->templatePageTagUtils->renderHtmlProperties($templatePageTagId, $absolutTagID);
          if ($strProperties) {
            $str .= "\n" . $strProperties;
          }
        }
      }
    }

    return($str);
  }

  // Render the properties for all the system pages
  function renderAllHtmlProperties($templateModelId) {
    $str = '';

    $systemPages = $this->getPageList();
    foreach ($systemPages as $systemPage => $description) {
      if ($templatePage = $this->selectByTemplateModelIdAndSystemPage($templateModelId, $systemPage)) {
        $templatePageId = $templatePage->getId();
        $str .= $this->renderHtmlProperties($templatePageId, true);
      }
    }

    return($str);
  }

}

?>
