<?

class TemplateElementLanguageUtils extends TemplateElementLanguageDB {

  var $mlText;

  var $languageUtils;
  var $templateUtils;
  var $templateElementUtils;
  var $newsFeedUtils;
  var $dynpageUtils;
  var $dynpageNavmenuUtils;
  var $linkCategoryUtils;
  var $photoUtils;

  function TemplateElementLanguageUtils() {
    $this->TemplateElementLanguageDB();
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Add a language for the element
  function add($templateElementId, $language, $objectId) {
    $templateElementLanguage = new TemplateElementLanguage();
    $templateElementLanguage->setTemplateElementId($templateElementId);
    $templateElementLanguage->setLanguage($language);
    $templateElementLanguage->setObjectId($objectId);
    $this->insert($templateElementLanguage);
    $templateElementLanguageId = $this->getLastInsertId();

    return($templateElementLanguageId);
  }

  // Duplicate the languages for the element
  function duplicateAllLanguages($templateElementId, $lastInsertTemplateElementId) {
    if ($templateElementLanguages = $this->selectByTemplateElementId($templateElementId)) {
      foreach ($templateElementLanguages as $templateElementLanguage) {
        $templateElementLanguageId = $templateElementLanguage->getId();
        $lastInsertObjectId = $this->duplicateLanguage($templateElementLanguageId);
        $templateElementLanguage->setObjectId($lastInsertObjectId);
        $templateElementLanguage->setTemplateElementId($lastInsertTemplateElementId);
        $this->insert($templateElementLanguage);
      }
    }
  }

  // Delete a language for the element
  function deleteAllLanguages($templateElementId) {
    if ($templateElementLanguages = $this->selectByTemplateElementId($templateElementId)) {
      foreach ($templateElementLanguages as $templateElementLanguage) {
        $templateElementLanguageId = $templateElementLanguage->getId();
        $this->deleteLanguage($templateElementLanguageId);
      }
    }
  }

  // Duplicate a language for a multi language element
  function duplicateLanguage($templateElementLanguageId) {
    $lastInsertObjectId = '';

    if ($templateElementLanguage = $this->selectById($templateElementLanguageId)) {
      $objectId = $templateElementLanguage->getObjectId();
      if ($objectId) {
        $templateElementId = $templateElementLanguage->getTemplateElementId();
        if ($templateElement = $this->templateElementUtils->selectById($templateElementId)) {
          $elementType = $templateElement->getElementType();
          if ($elementType == 'NEWS_FEED' || $elementType == 'NEWS_FEED_CYCLE') {
            $lastInsertObjectId = $this->newsFeedUtils->duplicate($objectId);
          } else if ($elementType == 'DYNPAGE_MENU' || $elementType == 'DYNPAGE_ACCORDION_MENU' || $elementType == 'DYNPAGE_TREE_MENU') {
            $lastInsertObjectId = $this->dynpageNavmenuUtils->duplicate($objectId);
          } else {
            $lastInsertObjectId = '';
          }
        }
      }
    }

    return($lastInsertObjectId);
  }

  // Delete a language for a mutlti language element
  function deleteLanguage($templateElementLanguageId) {
    if ($templateElementLanguage = $this->selectById($templateElementLanguageId)) {
      $objectId = $templateElementLanguage->getObjectId();
      if ($objectId) {
        $templateElementId = $templateElementLanguage->getTemplateElementId();
        if ($templateElement = $this->templateElementUtils->selectById($templateElementId)) {
          $elementType = $templateElement->getElementType();
          if ($elementType == 'NEWS_FEED' || $elementType == 'NEWS_FEED_CYCLE') {
            $this->newsFeedUtils->deleteNewsFeed($objectId);
          } else if ($elementType == 'DYNPAGE_MENU' || $elementType == 'DYNPAGE_ACCORDION_MENU' || $elementType == 'DYNPAGE_TREE_MENU') {
            $this->dynpageNavmenuUtils->delete($objectId);
          }
        }
      }
    }

    $this->delete($templateElementLanguageId);
  }

  // Create the element object if it is a multi instance one
  // Some elements have just one instance, others can have many
  function createElementContent($elementType) {
    $objectId = '';

    if ($elementType == 'NEWS_FEED' || $elementType == 'NEWS_FEED_CYCLE') {
      $objectId = $this->newsFeedUtils->add();
    } else if ($elementType == 'DYNPAGE_MENU' || $elementType == 'DYNPAGE_ACCORDION_MENU' || $elementType == 'DYNPAGE_TREE_MENU') {
      $objectId = $this->dynpageNavmenuUtils->add();
    }

    return($objectId);
  }

  // Get the url of the page to edit the content of an element that
  // can have several object instances
  function getEditContentUrl($elementType, $templateElementLanguageId, $objectId, $languageCode) {
    global $gNewsUrl;
    global $gLinkUrl;
    global $gPhotoUrl;
    global $gDynpageUrl;

    // Ask for a refresh of the cache
    $this->templateUtils->setRefreshCache();

    $url = '';

    if ($elementType == 'NEWS_FEED' || $elementType == 'NEWS_FEED_CYCLE') {
      $url = "$gNewsUrl/newsFeed/edit.php?newsFeedId=$objectId";
    } else if ($elementType == 'DYNPAGE_MENU' || $elementType == 'DYNPAGE_ACCORDION_MENU' || $elementType == 'DYNPAGE_TREE_MENU') {
      $url = "$gDynpageUrl/navmenu.php?dynpageNavmenuId=$objectId";
    } else if ($elementType == 'LINK_IMAGE_CYCLE') {
      $url = "$gLinkUrl/category/template_element.php?templateElementLanguageId=$templateElementLanguageId&linkCategoryId=$objectId";
    } else if ($elementType == 'PHOTO_IMAGE_CYCLE') {
      $url = "$gPhotoUrl/template_element.php?templateElementLanguageId=$templateElementLanguageId&photoAlbumId=$objectId";
    }

    $url .= "&languageCode=$languageCode";

    return($url);
  }

  // Get the available languages for the element
  function getAvailableLanguages($templateElementId, $excludeUsedOnes = false) {
    $this->loadLanguageTexts();

    $languageNames = $this->languageUtils->getActiveLanguageNames();
    $languageNames = array_merge(array('' => $this->mlText[0]), $languageNames);

    // Remove the already used languages
    if ($excludeUsedOnes) {
      if ($templateElementLanguages = $this->selectByTemplateElementId($templateElementId)) {
        foreach ($templateElementLanguages as $templateElementLanguage) {
          $language = $templateElementLanguage->getLanguage();
          unset($languageNames[$language]);
        }
      }
    }

    return($languageNames);
  }

  // Render the language for the element
  function render($templateElementId) {
    $str = '';

    $languageCode = $this->languageUtils->getCurrentLanguageCode();

    if ($templateElement = $this->templateElementUtils->selectById($templateElementId)) {
      $elementType = $templateElement->getElementType();
      $templateElementLanguage = $this->selectByLanguageAndTemplateElementId($languageCode, $templateElementId);
      if (!$templateElementLanguage) {
        $templateElementLanguage = $this->selectByNoLanguageAndTemplateElementId($templateElementId);
      }
      if ($templateElementLanguage) {
        $objectId = $templateElementLanguage->getObjectId();
        if ($elementType == 'NEWS_FEED') {
          $str = $this->newsFeedUtils->render($objectId);
        } else if ($elementType == 'DYNPAGE_MENU' || $elementType == 'DYNPAGE_ACCORDION_MENU' || $elementType == 'DYNPAGE_TREE_MENU') {
          $str = $this->dynpageNavmenuUtils->render($elementType, $objectId);
        } else if ($elementType == 'NEWS_FEED_CYCLE') {
          $str = $this->newsFeedUtils->renderImageCycleInTemplateElement($objectId);
        } else if ($elementType == 'LINK_IMAGE_CYCLE') {
          $str = $this->linkCategoryUtils->renderImageCycleInTemplateElement($objectId);
        } else if ($elementType == 'PHOTO_IMAGE_CYCLE') {
          $str = $this->photoUtils->renderImageCycleInTemplateElement($objectId);
        }
      }
    }

    return($str);
  }

}

?>
