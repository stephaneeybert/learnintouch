<?

class NavbarUtils extends NavbarDB {

  var $mlText;

  var $currentNavbarId;

  var $languageUtils;
  var $navbarLanguageUtils;
  var $navbarItemUtils;

  function NavbarUtils() {
    $this->NavbarDB();

    $this->init();
  }

  function init() {
    $this->currentNavbarId = "navbarCurrentNavbarId";
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Add a navigation bar
  function add() {
    $navbar = new Navbar();
    $this->insert($navbar);
    $navbarId = $this->getLastInsertId();

    $languageCode = $this->languageUtils->getCurrentLanguageCode();

    // Add a language to the bar if none
    if (!$navbarLanguages = $this->navbarLanguageUtils->selectByNavbarId($navbarId)) {
      $navbarLanguage = new NavbarLanguage();
      $navbarLanguage->setLanguage($languageCode);
      $navbarLanguage->setNavbarId($navbarId);
      $this->navbarLanguageUtils->insert($navbarLanguage);
    }

    return($navbarId);
  }

  // Duplicate a navigation bar
  function duplicate($navbarId) {
    if ($navbar = $this->selectById($navbarId)) {
      $duplicatedNavbar = new Navbar();
      $this->insert($duplicatedNavbar);
      $duplicatedNavbarId = $this->getLastInsertId();

      // Duplicate the languages
      if ($navbarLanguages = $this->navbarLanguageUtils->selectByNavbarId($navbarId)) {
        foreach ($navbarLanguages as $navbarLanguage) {
          $duplicatedNavbarLanguage = new NavbarLanguage();
          $duplicatedNavbarLanguage->setLanguage($navbarLanguage->getLanguage());
          $duplicatedNavbarLanguage->setNavbarId($duplicatedNavbarId);
          $this->navbarLanguageUtils->insert($duplicatedNavbarLanguage);
          $duplicatedNavbarLanguageId = $this->navbarLanguageUtils->getLastInsertId();

          // Duplicate the items of a language
          $navbarLanguageId = $navbarLanguage->getId();
          if ($navbarItems = $this->navbarItemUtils->selectByNavbarLanguageId($navbarLanguageId)) {
            foreach ($navbarItems as $navbarItem) {
              $duplicatedNavbarItem = new NavbarItem();
              $duplicatedNavbarItem->setName($navbarItem->getName());

              // Duplicate the image files
              $imagePath = $this->navbarItemUtils->imagePath;
              $image = $navbarItem->getImage();
              $imageOver = $navbarItem->getImageOver();
              if ($image) {
                if ($image && @is_file($imagePath . $image)) {
                  $randomNumber = LibUtils::generateUniqueId();
                  $imagePrefix = LibFile::getFilePrefix($image);
                  $imageSuffix = LibFile::getFileSuffix($image);
                  $imageDuplicata = $imagePrefix . '_' . $randomNumber . '.' . $imageSuffix;
                  @copy($imagePath . $image, $imagePath . $imageDuplicata);
                  $duplicatedNavbarItem->setImage($imageDuplicata);
                }
              }
              if ($imageOver) {
                if ($imageOver && @is_file($imagePath . $imageOver)) {
                  $randomNumber = LibUtils::generateUniqueId();
                  $imagePrefix = LibFile::getFilePrefix($imageOver);
                  $imageSuffix = LibFile::getFileSuffix($imageOver);
                  $imageDuplicata = $imagePrefix . '_' . $randomNumber . '.' . $imageSuffix;
                  @copy($imagePath . $imageOver, $imagePath . $imageDuplicata);
                  $duplicatedNavbarItem->setImageOver($imageDuplicata);
                }
              }

              $duplicatedNavbarItem->setUrl($navbarItem->getUrl());
              $duplicatedNavbarItem->setBlankTarget($navbarItem->getBlankTarget());
              $duplicatedNavbarItem->setDescription($navbarItem->getDescription());
              $duplicatedNavbarItem->setHide($navbarItem->getHide());
              $duplicatedNavbarItem->setTemplateModelId($navbarItem->getTemplateModelId());
              $duplicatedNavbarItem->setListOrder($navbarItem->getListOrder());
              $duplicatedNavbarItem->setNavbarLanguageId($duplicatedNavbarLanguageId);

              $this->navbarItemUtils->insert($duplicatedNavbarItem);
            }
          }
        }
      }

      return($duplicatedNavbarId);
    }
  }

  // Delete a navigation bar
  function deleteNavbar($navbarId) {
    // Delete all the languages of the bar
    if ($navbarLanguages = $this->navbarLanguageUtils->selectByNavbarId($navbarId)) {
      foreach ($navbarLanguages as $navbarLanguage) {
        $navbarLanguageId = $navbarLanguage->getId();
        $this->navbarLanguageUtils->deleteLanguage($navbarLanguageId);
      }
    }

    $this->delete($navbarId);
  }

  // Get the navigation bar for the current language
  function getNavbar($navbarId) {
    if (!$navbar = $this->selectByLanguage($languageCode)) {
      // If none is found then try to get one for all languages
      if (!$navbar = $this->selectByLanguage('')) {
        // If none is found then try to get the default language one
        $navbar = $this->selectByLanguage($this->languageUtils->getDefaultLanguageCode());
      }
    }

    return($navbar);
  }

  // Count the available languages for the navigation bar
  function countAvailableLanguages($navbarId) {
    $languageNames = $this->getAvailableLanguages($navbarId);

    return(count($languageNames));
  }

  // Get the available languages for the navigation bar
  function getAvailableLanguages($navbarId) {
    $this->loadLanguageTexts();

    $languageNames = $this->languageUtils->getActiveLanguageNames();
    $languageNames = array_merge(array('' => $this->mlText[0]), $languageNames);

    // Remove the already used languages
    if ($navbarLanguages = $this->navbarLanguageUtils->selectByNavbarId($navbarId)) {
      foreach ($navbarLanguages as $navbarLanguage) {
        $language = $navbarLanguage->getLanguage();
        unset($languageNames[$language]);
      }
    }

    return($languageNames);
  }

  // Render the bar horizontally
  function renderHorizontal($navbarId) {
    $str = $this->render($navbarId, true);

    return($str);
  }

  // Render the bar vertically
  function renderVertical($navbarId) {
    $str = $this->render($navbarId, false);

    return($str);
  }

  // Render
  function render($navbarId, $horizontal) {
    global $gTemplateUrl;

    if (!$navbar = $this->selectById($navbarId)) {
      return;
    }

    // Check if the navigation bar is not to be displayed
    $hide = $navbar->getHide();
    if ($hide) {
      return;
    }

    $languageCode = $this->languageUtils->getCurrentLanguageCode();

    // Get the navbar language
    if (!$navbarLanguage = $this->navbarLanguageUtils->selectByNavbarIdAndLanguage($navbarId, $languageCode)) {
      // If none is found then try to get a navbar for the default language
      if (!$navbarLanguage = $this->navbarLanguageUtils->selectByNavbarIdAndLanguage($navbarId, $this->languageUtils->getDefaultLanguageCode())) {
        // If none is found then get the link for no specific language
        if (!$navbarLanguage = $this->navbarLanguageUtils->selectByNavbarIdAndNoLanguage($navbarId)) {
          return;
        }
      }
    }

    $navbarLanguageId = $navbarLanguage->getId();

    if (!$navbarItems = $this->navbarItemUtils->selectByNavbarLanguageId($navbarLanguageId)) {
      return;
    }

    $str = '';

    $str .= "\n<div class='navbar'>";

    if ($navbarItems = $this->navbarItemUtils->selectByNavbarLanguageId($navbarLanguageId)) {
      for ($i = 0; $i < count($navbarItems); $i++) {
        $navbarItem = $navbarItems[$i];
        $navbarItemId = $navbarItem->getId();
        $hide = $navbarItem->getHide();

        if ($hide) {
          continue;
        }

        if ($i == 0) {
          $strClassID = 'navbar_item_first';
        } else if ($i == count($navbarItems) - 1) {
          $strClassID = 'navbar_item_last';
        } else {
          $strClassID = 'navbar_item';
        }

        if (!$horizontal) {
          $str .= "<div class='" . $strClassID . "'>";
        } else {
          // Do not have a new line \n here as it inserts a blank space
          // And a blank space is unwelcomed for the images based navigation bars
          // And have an inline-block so as the have an item border (if any) wrapping
          // around the item image (if any)
          $str .= "<span style='display:inline-block;' class='" . $strClassID . "'>";
        }

        $str .= $this->navbarItemUtils->render($navbarItemId, $horizontal);

        if (!$horizontal) {
          $str .= "</div>";
        } else {
          // Do not have a new line \n here as it inserts a blank space
          // And a blank space is unwelcomed for the images based navigation bars
          $str .= "</span>";
        }
      }

    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the tags
  // When creating an element in the template system, it is necessary to create its tags
  // A dummy rendering is used for this operation
  function renderTags() {
    $str = "\n<div class='navbar'>"
      . "<div class='navbar_item_first'></div>"
      . "<div class='navbar_item'>"
      . "<div class='navbar_item_image'></div>"
      . "</div>"
      . "<div class='navbar_item_last'></div>"
      . "\n</div>";

    return($str);
  }

}

?>
