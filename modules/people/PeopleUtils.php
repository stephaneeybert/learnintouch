<?

class PeopleUtils extends PeopleDB {

  var $mlText;
  var $websiteText;

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $currentCategoryId;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $fileUploadUtils;

  function PeopleUtils() {
    $this->PeopleDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'people/image/';
    $this->imageFileUrl = $gDataUrl . '/people/image';

    $this->currentCategoryId = "peopleCurrentCategoryId";
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'people')) {
        mkdir($gDataPath . 'people');
      }
      mkdir($this->imageFilePath);
      chmod($this->imageFilePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "PEOPLE_DISPLAY_ALL" =>
      array($this->mlText[4], $this->mlText[5], PREFERENCE_TYPE_BOOLEAN, ''),
        "PEOPLE_NB_PER_ROW" =>
        array($this->mlText[32], $this->mlText[33], PREFERENCE_TYPE_RANGE, array(1, 10, 2)),
          "PEOPLE_NON_CLICKABLE_IMAGE" =>
          array($this->mlText[12], $this->mlText[13], PREFERENCE_TYPE_BOOLEAN, ''),
            "PEOPLE_HIDE_PROFILE" =>
            array($this->mlText[10], $this->mlText[11], PREFERENCE_TYPE_BOOLEAN, ''),
              "PEOPLE_PHONE_HIDE_PROFILE" =>
              array($this->mlText[44], $this->mlText[45], PREFERENCE_TYPE_BOOLEAN, ''),
                "PEOPLE_HIDE_EMAIL" =>
                array($this->mlText[46], $this->mlText[47], PREFERENCE_TYPE_BOOLEAN, ''),
                  "PEOPLE_PHONE_HIDE_EMAIL" =>
                  array($this->mlText[48], $this->mlText[49], PREFERENCE_TYPE_BOOLEAN, ''),
                    "PEOPLE_HIDE_SELECTOR" =>
                    array($this->mlText[6], $this->mlText[7], PREFERENCE_TYPE_BOOLEAN, ''),
                      "PEOPLE_PHONE_HIDE_SELECTOR" =>
                      array($this->mlText[42], $this->mlText[43], PREFERENCE_TYPE_BOOLEAN, ''),
                        "PEOPLE_DEFAULT_MINI_WIDTH" =>
                        array($this->mlText[30], $this->mlText[31], PREFERENCE_TYPE_TEXT, 100),
                          "PEOPLE_PHONE_DEFAULT_MINI_WIDTH" =>
                          array($this->mlText[36], $this->mlText[37], PREFERENCE_TYPE_TEXT, 70),
                            "PEOPLE_DEFAULT_LARGE_WIDTH" =>
                            array($this->mlText[34], $this->mlText[35], PREFERENCE_TYPE_TEXT, 300),
                              "PEOPLE_PHONE_DEFAULT_LARGE_WIDTH" =>
                              array($this->mlText[38], $this->mlText[39], PREFERENCE_TYPE_TEXT, 140),
                              );

    $this->preferenceUtils->init($this->preferences);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imageFilePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->imageIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (file_exists($this->imageFilePath . $oneFile)) {
            unlink($this->imageFilePath . $oneFile);
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

  // Get the next available list order
  function getNextListOrder($categoryId) {
    $listOrder = 1;
    if ($objects = $this->selectByCategoryId($categoryId)) {
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
    if ($link = $this->selectById($id)) {
      $listOrder = $link->getListOrder();
      $categoryId = $link->getCategoryId();
      if ($links = $this->selectByListOrder($categoryId, $listOrder)) {
        if (($listOrder == 0) || (count($links)) > 1) {
          $this->resetListOrder($categoryId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($link = $this->selectById($id)) {
      $listOrder = $link->getListOrder();
      $categoryId = $link->getCategoryId();
      if ($link = $this->selectByNextListOrder($categoryId, $listOrder)) {
        return($link);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($link = $this->selectById($id)) {
      $listOrder = $link->getListOrder();
      $categoryId = $link->getCategoryId();
      if ($link = $this->selectByPreviousListOrder($categoryId, $listOrder)) {
        return($link);
      }
    }
  }

  // Delete a person
  function deletePerson($peopleId) {
    $this->delete($peopleId);
  }

  // Render one person
  function render($peopleId) {
    global $gImagesUserUrl;
    global $gJSNoStatus;
    global $gPeopleUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $str = '';

    // If no one is specified
    // then get the first person
    if (!$peopleId) {
      if ($peoples = $this->selectAll()) {
        $people = $peoples[0];
        $peopleId = $people->getId();
      }
    }

    if (!$people = $this->selectById($peopleId)) {
      return;
    }

    $categoryId = $people->getCategoryId();
    $peopleName = $this->renderName($people);
    $profile = $this->renderProfile($people);
    $image = $this->renderImage($people);
    $email = $this->renderEmail($people);
    $workPhone = $people->getWorkPhone();
    $mobilePhone = $people->getMobilePhone();

    $str .= "\n<div class='people_item'>";

    $str .= "<div class='people_item_back_button'>";
    $str .= "<a href='$gPeopleUrl/display.php?peopleCategoryId=$categoryId' $gJSNoStatus>" . $this->websiteText[1];
    $str .= "<img src='$gImagesUserUrl/" . IMAGE_COMMON_UP . "' class='no_style_image_icon' title='' alt='' /></a>";
    $str .= "</div>";

    $str .= "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>";
    $str .= "<tr valign='top'>";
    $str .= "<td>";
    $str .= "<div class='people_item_image'>$image</div>";

    if (!$gIsPhoneClient) {
      $str .= "</td><td>";
    }

    $str .= "<div class='people_item_name'>$peopleName</div>";
    $str .= "<div class='people_item_profile'>$profile</div>";
    $str .= "<div class='people_item_email'>$email</div>";
    $str .= "<div class='people_item_work_phone'>$workPhone</div>";
    $str .= "<div class='people_item_mobile_phone'>$mobilePhone</div>";
    $str .= "</td>";
    $str .= "</tr>";
    $str .= "</table>";

    $str .= "\n</div>";

    return($str);
  }

  // Render the email
  function renderEmail($people) {
    if (!$people) {
      return;
    }

    $email = $people->getEmail();

    if ($email) {
      $str = "<a href='mailto:$email'>$email</a>";
    } else {
      $str = '';
    }

    return($str);
  }

  // Render the name
  function renderName($people) {
    if (!$people) {
      return;
    }

    $firstname = $people->getFirstname();
    $lastname = $people->getLastname();

    $str = $firstname . ' ' . $lastname;

    return($str);
  }

  // Render the profile
  function renderProfile($people) {
    if (!$people) {
      return;
    }

    $profile = $people->getProfile();

    $profile = nl2br($profile);

    $str = $profile;

    return($str);
  }

  // Get the image width
  function getImageWidth() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("PEOPLE_PHONE_DEFAULT_LARGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("PEOPLE_DEFAULT_LARGE_WIDTH");
    }

    return($width);
  }

  // Render the image
  function renderImage($people) {
    global $gUtilsUrl;

    if (!$people) {
      return;
    }

    $image = $people->getImage();

    if (!$image) {
      return;
    }

    // Resize the image to the following width
    $width = $this->getImageWidth();

    // A gif image cannot be resized
    // No support for the gif format due to copyrights issues
    if (!$this->fileUploadUtils->isGifImage($this->imageFilePath . $image)) {
      // The image is created on the fly
      $imageFilePath = $this->imageFilePath;
      $filename = urlencode($imageFilePath . $image);

      $url = $gUtilsUrl . "/printImage.php?filename=" . $filename . "&amp;width="
        . $width . "&amp;height=";
    } else {
      $url = "$this->imageFileUrl/$image";
    }

    $str = "<img class='people_item_image_file' src='$url' title='' alt='' width='$width' />";

    return($str);
  }

  // Render the image reduced to the size of a thumbnail
  function renderThumbnail($people) {
    global $gJSNoStatus;
    global $gPeopleUrl;
    global $gUtilsUrl;
    global $gIsPhoneClient;

    if (!$people) {
      return;
    }

    $peopleId = $people->getId();
    $image = $people->getImage();

    // If no image is specified then render nothing
    $imageFilePath = $this->imageFilePath;
    if (!$image || !is_file($imageFilePath . $image)) {
      return;
    }

    // Resize the image to the following width
    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("PEOPLE_PHONE_DEFAULT_MINI_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("PEOPLE_DEFAULT_MINI_WIDTH");
    }

    // A gif image cannot be resized
    // No support for the gif format due to copyrights issues
    if (!$this->fileUploadUtils->isGifImage($this->imageFilePath . $image)) {
      // The image is created on the fly
      $filename = urlencode($imageFilePath . $image);

      $url = $gUtilsUrl . "/printImage.php?filename=" . $filename . "&amp;width=" . $width . "&amp;height=";
    } else {
      $url = "$this->imageFileUrl/$image";
    }

    $nonClickable = $this->preferenceUtils->getValue("PEOPLE_NON_CLICKABLE_IMAGE");
    if ($nonClickable) {
      $str = "<img class='people_list_image_file' src='$url' title='' alt='' width='$width' />";
    } else {
      $str = "<a href='$gPeopleUrl/display_person.php?peopleId=$peopleId' $gJSNoStatus>"
        . "<img class='people_list_image_file' src='$url' title='' alt='' width='$width' />"
        . "</a>";
    }

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElements() {
    global $gImagesUserUrl;
    global $gStylingImage;

    $str = "<div class='people_item'>A person"
      . "<div class='people_item_back_button'>The back button "
      . "<img src='$gImagesUserUrl/" . IMAGE_COMMON_UP . "' class='no_style_image_icon' title='The back button' alt='' />"
      . "</div>"
      . "<div class='people_item_image'>The photo of the person"
      . "<img class='people_item_image_file' src='$gStylingImage' title='The border of the photo of the person' alt='' width='' />"
      . "</div>"
      . "<div class='people_item_name'>The name</div>"
      . "<div class='people_item_email'>The email address</div>"
      . "<div class='people_item_work_phone'>The work phone</div>"
      . "<div class='people_item_mobile_phone'>The mobile phone</div>"
      . "<div class='people_item_profile'>The profile</div>"
      . "</div>";

    return($str);
  }

}

?>
