<?php

class TemplatePageTagUtils extends TemplatePageTagDB {

  var $mlText;

  var $languageUtils;
  var $templatePropertySetUtils;
  var $templateModelUtils;
  var $templatePageUtils;
  var $clientUtils;
  var $guestbookUtils;
  var $peopleCategoryUtils;
  var $peopleUtils;
  var $linkCategoryUtils;
  var $photoAlbumUtils;
  var $photoUtils;
  var $documentUtils;
  var $formUtils;
  var $newsPublicationUtils;
  var $newsPaperUtils;
  var $newsStoryUtils;
  var $shopItemUtils;
  var $shopOrderUtils;
  var $dynpageUtils;
  var $elearningExerciseUtils;
  var $elearningLessonUtils;
  var $elearningTeacherUtils;
  var $elearningSubscriptionUtils;

  function TemplatePageTagUtils() {
    $this->TemplatePageTagDB();
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Clean up the tag ids for a page
  // These are the tags that exist in the database
  // If the source code changes, then the database may contain some tags
  // that no longer exist in the source code and that should be removed from the database
  function cleanupDatabaseTagIDs($templatePageId, $tagIDs) {
    if ($templatePageId) {
      if ($templatePageTags = $this->selectByTemplatePageId($templatePageId)) {
        foreach ($templatePageTags as $templatePageTag) {
          $dbTagID = $templatePageTag->getTagID();
          $templatePageTagId = $templatePageTag->getId();
          if (!in_array($dbTagID, $tagIDs)) {
            $this->deleteTemplatePageTag($templatePageTagId);
          }
        }
      }
    }

    return($tagIDs);
  }

  // Delete a tag and all its properties
  function deleteTemplatePageTag($templatePageTagId) {
    if ($templatePageTagId) {
      $this->delete($templatePageTagId);

      if ($templatePageTag = $this->selectById($templatePageTagId)) {
        $templatePropertySetId = $templatePageTag->getTemplatePropertySetId();
        if ($templatePropertySetId) {
          $this->templatePropertySetUtils->deleteTemplatePropertySet($templatePropertySetId);
        }
      }
    }
  }

  // Duplicate a tag
  function duplicate($templatePageTag, $templatePageId) {
    $templatePageTag->setTemplatePageId($templatePageId);
    $lastInsertTemplatePropertySetId = $this->templatePropertySetUtils->duplicate($templatePageTag->getTemplatePropertySetId());
    $templatePageTag->setTemplatePropertySetId($lastInsertTemplatePropertySetId);
    $this->insert($templatePageTag);
  }

  // Export a tag
  function exportXML($xmlNode, $templatePageTagId) {
    if ($templatePageTag = $this->selectById($templatePageTagId)) {
      $tagID = $templatePageTag->getTagID();

      $xmlChildNode = $xmlNode->addChild(TEMPLATE_PAGE_TAG);
      $attributes = array("tagID" => $tagID);
      if (is_array($attributes)) {
        foreach ($attributes as $aName => $aValue) {
          $xmlChildNode->addAttribute($aName, $aValue);
        }
      }

      // Export the property set
      $templatePropertySetId = $templatePageTag->getTemplatePropertySetId();
      $this->templatePropertySetUtils->exportXML($xmlChildNode, $templatePropertySetId);
    }
  }

  // Import a tag
  function importXML($xmlNode, $lastInsertTemplatePageId) {
    $tagID = $xmlNode->attributes()["tagID"];

    // Create the tag
    $templatePageTag = new TemplatePageTag();
    $templatePageTag->setTagID($tagID);
    $templatePageTag->setTemplatePageId($lastInsertTemplatePageId);
    $this->insert($templatePageTag);
    $lastInsertTemplatePageTagId = $this->getLastInsertId();

    $xmlChildNodes = $xmlNode->children();
    foreach ($xmlChildNodes as $xmlChildNode) {
      $name = $xmlChildNode->getName();
      if ($name == TEMPLATE_PROPERTY_SET) {
        // Create the property set
        $lastInsertTemplatePropertySetId = $this->templatePropertySetUtils->importXML($xmlChildNode);

        // Link the tag and the property set
        $templatePageTag->setId($lastInsertTemplatePageTagId);
        $templatePageTag->setTemplatePropertySetId($lastInsertTemplatePropertySetId);
        $this->update($templatePageTag);
      }
    }
  }

  function getTemplateModelId($templatePageTagId) {
    $templateModelId = '';

    if ($templatePageTag = $this->selectById($templatePageTagId)) {
      $templatePageId = $templatePageTag->getTemplatePageId();
      $templateModelId = $this->templatePageUtils->getTemplateModelId($templatePageId);
      }

    return($templateModelId);
    }

  // Render the tag properties for an html output
  function renderHtmlProperties($templatePageTagId, $absolutTagID = false) {
    $str = '';

    if ($templatePageTagId) {
      if ($templatePageTag = $this->selectById($templatePageTagId)) {
        $templatePropertySetId = $templatePageTag->getTemplatePropertySetId();
        $tagID = $templatePageTag->getTagID();

        $strProperties = $this->templatePropertySetUtils->renderHtmlProperties($templatePropertySetId);
        if ($strProperties) {
          $strTag = '.' . $this->renderTagID($templatePageTagId, $absolutTagID);
          $str .= $strTag . ' { ' . $strProperties .' }';

          // Apply the normal text properties
          $strTextProperties = $this->templatePropertySetUtils->renderHtmlTextProperties($templatePropertySetId);
          if (trim($strTextProperties)) {
            $str .= ' ' . $strTag . ' a { ' . $strTextProperties . ' }';
          }

          // Apply the link specific properties
          $str .= ' ' . $this->templatePropertySetUtils->renderHtmlLinkProperties($strTag, $templatePropertySetId);

          // Add the property set id to allow for the editing of the line of properties
          // by the property editor
          $str .= ' /* TPS_ID_' . $templatePropertySetId . ' */';
        }
      }
    }

    return($str);
  }

  // Render the tag id of the tag
  function renderTagID($templatePageTagId, $absolutTagID) {
    $str = '';

    if ($templatePageTag = $this->selectById($templatePageTagId)) {
      $tagID = $templatePageTag->getTagID();

      // Check if the model id must be rendered
      // It is not rendered when editing a page styles
      // But it is when rendering the model output
      if ($absolutTagID) {
        $modelTagID = $this->renderModelTagID($templatePageTagId);
        $str = $modelTagID . ' .' . $tagID;
      } else {
        $str = $tagID;
      }
    }

    return($str);
  }

  // Render the tag id of the model
  function renderModelTagID($templatePageTagId) {
    $str = '';

    if ($templatePageTagId) {
      if ($templatePageTag = $this->selectById($templatePageTagId)) {
        $templatePageId = $templatePageTag->getTemplatePageId();
        if ($templatePage = $this->templatePageUtils->selectById($templatePageId)) {
          $templateModelId = $templatePage->getTemplateModelId();
          $str = $this->templateModelUtils->renderTagID($templateModelId);
        }
      }
    }

    return($str);
  }

  // Render the content
  function renderSystemPageContent($systemPage) {
    global $gClientPath;
    global $gGuestbookPath;
    global $gPeoplePath;
    global $gLinkPath;
    global $gPhotoPath;
    global $gElearningPath;
    global $gNewsPath;
    global $gFormPath;
    global $gDocumentPath;
    global $gElearningPath;
    global $gShopPath;
    global $gUtilsPath;
    global $gDynpagePath;

    $str = '';

    if ($systemPage == 'SYSTEM_PAGE_CLIENT_LIST' || $systemPage == 'SYSTEM_PAGE_CLIENT_CYCLE') {
      $str = $this->clientUtils->renderStylingElements();
    } else if ($systemPage == 'SYSTEM_PAGE_GUESTBOOK_LIST') {
      $str = $this->guestbookUtils->renderStylingElements();
    } else if ($systemPage == 'SYSTEM_PAGE_PEOPLE_LIST') {
      $str = $this->peopleCategoryUtils->renderStylingElements();
    } else if ($systemPage == 'SYSTEM_PAGE_PEOPLE_ITEM') {
      $str = $this->peopleUtils->renderStylingElements();
    } else if ($systemPage == 'SYSTEM_PAGE_LINK_LIST' || $systemPage == 'SYSTEM_PAGE_LINK_CYCLE') {
      $str = $this->linkCategoryUtils->renderStylingElements();
    } else if ($systemPage == 'SYSTEM_PAGE_PHOTO_ALBUM_LIST') {
      $str = $this->photoAlbumUtils->renderStylingElementsForAlbums();
    } else if ($systemPage == 'SYSTEM_PAGE_PHOTO_LIST' || $systemPage == 'SYSTEM_PAGE_PHOTO_CYCLE') {
      $str = $this->photoAlbumUtils->renderStylingElementsForPHotos();
    } else if ($systemPage == 'SYSTEM_PAGE_PHOTO_ITEM') {
      $str = $this->photoUtils->renderStylingElementsForOnePhoto();
    } else if ($systemPage == 'SYSTEM_PAGE_PHOTO_SEARCH') {
      $str = $this->renderSystemPageStylingElements();
    } else if ($systemPage == 'SYSTEM_PAGE_DOCUMENT_LIST') {
      $str = $this->documentUtils->renderStylingElements();
    } else if ($systemPage == 'SYSTEM_PAGE_FORM') {
      $str = $this->formUtils->renderStylingElements();
    } else if ($systemPage == 'SYSTEM_PAGE_NEWSPUBLICATION_LIST') {
      $str = $this->newsPublicationUtils->renderStylingElements();
    } else if ($systemPage == 'SYSTEM_PAGE_NEWSPAPER_LIST') {
      $str = $this->newsPaperUtils->renderStylingElementsForList();
    } else if ($systemPage == 'SYSTEM_PAGE_NEWSPAPER') {
      $str = $this->newsPaperUtils->renderStylingElements();
    } else if ($systemPage == 'SYSTEM_PAGE_NEWSSTORY') {
      $str = $this->newsStoryUtils->renderStylingElements();
    } else if ($systemPage == 'SYSTEM_PAGE_SHOP_CATEGORY_LIST') {
      $str = $this->shopItemUtils->renderStylingElementsForList();
    } else if ($systemPage == 'SYSTEM_PAGE_SHOP_ITEM') {
      $str = $this->shopItemUtils->renderStylingElementsForItem();
    } else if ($systemPage == 'SYSTEM_PAGE_SHOP_ORDER_LIST') {
      $str = $this->shopOrderUtils->renderStylingElementsForList();
    } else if ($systemPage == 'SYSTEM_PAGE_DYNPAGE') {
      $str = $this->dynpageUtils->renderStylingElements();
    } else if ($systemPage == 'SYSTEM_PAGE_ELEARNING_SUBSCRIPTIONS') {
      $str = $this->elearningExerciseUtils->renderStylingElementsForCourse();
    } else if ($systemPage == 'SYSTEM_PAGE_ELEARNING_PARTICIPANTS') {
      $str = $this->elearningSubscriptionUtils->renderStylingElementsForList();
    } else if ($systemPage == 'SYSTEM_PAGE_ELEARNING_LIST_TEACHERS') {
      $str = $this->elearningTeacherUtils->renderStylingElementsForList();
    } else if ($systemPage == 'SYSTEM_PAGE_ELEARNING_EXERCISE') {
      $str .= $this->elearningExerciseUtils->renderStylingElementsForExercise();
      $str .= $this->elearningExerciseUtils->renderStylingElementsForContactPage();
    } else if ($systemPage == 'SYSTEM_PAGE_ELEARNING_LESSON') {
      $str = $this->elearningLessonUtils->renderStylingElementsForLesson();
    } else if ($systemPage == 'SYSTEM_PAGE_ELEARNING_RESULT') {
      $str = $this->elearningExerciseUtils->renderStylingElementsForResults();
    } else if ($systemPage == 'SYSTEM_PAGE') {
      $str = $this->renderSystemPageStylingElements();
    }

    return($str);
  }

  // Render a dummy system page with the styling elements
  function renderSystemPageStylingElements() {
    $this->loadLanguageTexts();

    $str = "\n<div class='system'>" . $this->mlText[0]
      . "<div class='system_title'>" . $this->mlText[1] . "</div>"
      . "<div class='system_comment'>" . $this->mlText[2] . "</div>"
      . "<div class='system_warning'>" . $this->mlText[3] . "</div>"
      . "<div class='system_form'>"
      . "<div class='system_label'>" . $this->mlText[4]
      . "<span class='system_tooltip'>" . $this->mlText[5] . "</span>"
      . "</div>"
      . "<div class='system_field'>"
      . "<input class='system_input' type='text' name='word' value='An input field' size='30' maxlength='50'>"
      . "</div>"
      . "<div class='system_okay_button'>" . $this->mlText[6] . "</div>"
      . "<div class='system_cancel_button'>" . $this->mlText[7] . "</div>"
      . "</div>"
      . "<div class='system_icons'>" . $this->mlText[9] . "</div>"
      . "<div class='system_email_content'>" . $this->mlText[8] . "</div>"
      . "</div>";

    return($str);
  }

}

?>
