<?

class NavlinkUtils extends NavlinkDB {

  var $mlText;

  var $currentNavlinkId;

  var $languageUtils;

  function NavlinkUtils() {
    $this->NavlinkDB();

    $this->init();
  }

  function init() {
    $this->currentNavlinkId = "navlinkCurrentNavlinkId";
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Add a navigation link
  function add() {
    $navlink = new Navlink();
    $this->insert($navlink);
    $navlinkId = $this->getLastInsertId();

    $languageCode = $this->languageUtils->getCurrentLanguageCode();

    // Create the item
    $navlinkItem = new NavlinkItem();
    $navlinkItem->setLanguage($languageCode);
    $navlinkItem->setNavlinkId($navlinkId);
    $this->navlinkItemUtils->insert($navlinkItem);

    return($navlinkId);
  }

  // Duplicate a navigation link
  function duplicate($navlinkId) {
    if ($navlink = $this->selectById($navlinkId)) {
      $duplicatedNavlink = new Navlink();
      $this->insert($duplicatedNavlink);
      $duplicatedNavlinkId = $this->getLastInsertId();

      // Duplicate the item
      if ($navlinkItems = $this->navlinkItemUtils->selectByNavlinkId($navlinkId)) {
        foreach ($navlinkItems as $navlinkItem) {
          $navlinkItemId = $navlinkItem->getId();
          $duplicatedNavlinkItem = new NavlinkItem();

          $duplicatedNavlinkItem->setText($navlinkItem->getText());
          $duplicatedNavlinkItem->setDescription($navlinkItem->getDescription());

          // Duplicate the image files
          $imagePath = $this->navlinkItemUtils->imagePath;
          $image = $navlinkItem->getImage();
          $imageOver = $navlinkItem->getImageOver();
          if ($image) {
            if ($image && @is_file($imagePath . $image)) {
              $randomNumber = LibUtils::generateUniqueId();
              $imagePrefix = LibFile::getFilePrefix($image);
              $imageSuffix = LibFile::getFileSuffix($image);
              $imageDuplicata = $imagePrefix . '_' . $randomNumber . '.' . $imageSuffix;
              @copy($imagePath . $image, $imagePath . $imageDuplicata);
              $duplicatedNavlinkItem->setImage($imageDuplicata);
            }
          }
          if ($imageOver) {
            if ($imageOver && @is_file($imagePath . $imageOver)) {
              $randomNumber = LibUtils::generateUniqueId();
              $imagePrefix = LibFile::getFilePrefix($imageOver);
              $imageSuffix = LibFile::getFileSuffix($imageOver);
              $imageDuplicata = $imagePrefix . '_' . $randomNumber .'.' . $imageSuffix;
              @copy($imagePath . $imageOver, $imagePath . $imageDuplicata);
              $duplicatedNavlinkItem->setImageOver($imageDuplicata);
            }
          }
          $duplicatedNavlinkItem->setUrl($navlinkItem->getUrl());
          $duplicatedNavlinkItem->setBlankTarget($navlinkItem->getBlankTarget());
          $duplicatedNavlinkItem->setLanguage($navlinkItem->getLanguage());
          $duplicatedNavlinkItem->setTemplateModelId($navlinkItem->getTemplateModelId());
          $duplicatedNavlinkItem->setNavlinkId($duplicatedNavlinkId);
          $this->navlinkItemUtils->insert($duplicatedNavlinkItem);
        }
      }

      return($duplicatedNavlinkId);
    }
  }

  // Get the item for a specific language
  function getLanguageItem($language, $navlinkId) {
    if ($navlinkItem = $this->navlinkItemUtils->selectByLanguageAndNavlinkId($language, $navlinkId)) {
      $navlinkItemId = $navlinkItem->getId();
      return($navlinkItemId);
    }
  }

  // Delete a navigation link
  function deleteNavlink($navlinkId) {
    if ($navlinkItems = $this->navlinkItemUtils->selectByNavlinkId($navlinkId)) {
      foreach ($navlinkItems as $navlinkItem) {
        $navlinkItemId = $navlinkItem->getId();
        $this->navlinkItemUtils->delete($navlinkItemId);
      }
    }

    $this->delete($navlinkId);
  }

  // Get the available languages for the navigation link
  function getAvailableLanguages($navlinkId, $excludeUsedOnes = false) {
    $this->loadLanguageTexts();

    $languageNames = $this->languageUtils->getActiveLanguageNames();
    $languageNames = array_merge(array('' => $this->mlText[0]), $languageNames);

    // Remove the already used languages
    if ($excludeUsedOnes) {
      if ($navlinkItems = $this->navlinkItemUtils->selectByNavlinkId($navlinkId)) {
        foreach ($navlinkItems as $navlinkItem) {
          $language = $navlinkItem->getLanguage();
          unset($languageNames[$language]);
        }
      }
    }

    return($languageNames);
  }

  // Render
  function render($navlinkId) {
    global $gTemplateUrl;
    global $gHomeUrl;

    if (!$navlink = $this->selectById($navlinkId)) {
      return;
    }

    $languageCode = $this->languageUtils->getCurrentLanguageCode();

    // Get the link language
    if (!$navlinkItem = $this->navlinkItemUtils->selectByLanguageAndNavlinkId($languageCode, $navlinkId)) {
      // If none is found then try to get a link for the default language
      if (!$navlinkItem = $this->navlinkItemUtils->selectByLanguageAndNavlinkId($this->languageUtils->getDefaultLanguageCode(), $navlinkId)) {
        // If none is found then get the link for no specific language
        if (!$navlinkItem = $this->navlinkItemUtils->selectByNoLanguageAndNavlinkId($navlinkId)) {
          return;
        }
      }
    }

    $navlinkItemId = $navlinkItem->getId();

    $str = $this->navlinkItemUtils->render($navlinkItemId);

    return($str);
  }

  // Render the tags
  // When creating an element in the template system, it is necessary to create its tags
  // A dummy rendering is used for this operation
  function renderTags() {
    $str = "\n<div class='navlink'>"
      . "</div>";

    return($str);
  }

}

?>
