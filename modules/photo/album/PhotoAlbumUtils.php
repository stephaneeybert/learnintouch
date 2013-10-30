<?

class PhotoAlbumUtils extends PhotoAlbumDB {

  var $mlText;
  var $websiteText;

  var $nbPerRow;

  var $languageUtils;
  var $preferenceUtils;
  var $clockUtils;
  var $photoUtils;
  var $colorboxUtils;

  function PhotoAlbumUtils() {
    $this->PhotoAlbumDB();

    $this->init();
  }

  function init() {
    $this->nbPerRow = 3;
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  // Get the next available list order
  function getNextListOrder() {
    $listOrder = 1;
    if ($photoAlbums = $this->selectAll()) {
      $total = count($photoAlbums);
      if ($total > 0) {
        $photoAlbum = $photoAlbums[$total - 1];
        $listOrder = $photoAlbum->getListOrder() + 1;
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
    if ($photoAlbum = $this->selectById($id)) {
      $listOrder = $photoAlbum->getListOrder();
      if ($photoAlbums = $this->selectByListOrder($listOrder)) {
        if (($listOrder == 0) || (count($photoAlbums)) > 1) {
          $this->resetListOrder();
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($photoAlbum = $this->selectById($id)) {
      $listOrder = $photoAlbum->getListOrder();
      if ($photoAlbum = $this->selectByNextListOrder($listOrder)) {
        return($photoAlbum);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($photoAlbum = $this->selectById($id)) {
      $listOrder = $photoAlbum->getListOrder();
      if ($photoAlbum = $this->selectByPreviousListOrder($listOrder)) {
        return($photoAlbum);
      }
    }
  }

  function selectPublished($publicationDate = '') {
    $listPhotoAlbums = array();

    if (!$publicationDate) {
      $publicationDate = $this->clockUtils->getSystemDate();
    }

    if ($photoAlbums = $this->selectNotHidden()) {
      foreach ($photoAlbums as $photoAlbum) {
        $wPublicationDate = $photoAlbum->getPublicationDate();
        if ($this->clockUtils->systemDateIsSet($wPublicationDate) && $this->clockUtils->systemDateIsGreaterOrEqual($wPublicationDate, $publicationDate)) {
          array_push($listPhotoAlbums, $photoAlbum);
        }
      }
    }

    return($listPhotoAlbums);
  }

  // Render the search results
  function renderSearchList($reference, $pattern, $publicationDate) {
    global $gImagesUserUrl;
    global $gPhotoUrl;
    global $gShopUrl;
    global $gJSNoStatus;

    $this->loadLanguageTexts();

    $str = '';

    $str .= "\n<div class='photo_list'>";

    $strSearch = "\n<a href='$gPhotoUrl/search.php' $gJSNoStatus title='"
      .  $this->websiteText[52] .  "'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_PHOTO_SEARCH . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    $strViewList = "\n<a href='$gPhotoUrl/display_list.php' $gJSNoStatus title='"
      .  $this->websiteText[101] . "'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_PHOTO_ALBUM_LIST . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    $str .= "\n<div class='photo_list_comment'>"
      . "\n $strSearch"
      . "\n $strViewList"
      . "\n</div>";

    $systemDate = $this->clockUtils->getSystemDateTime();

    $photoAlbums = array();
    if ($reference) {
      $photos = $this->photoUtils->selectByReference($reference);
      if (count($photos > 0)) {
        foreach ($photos as $photo) {
          $photoAlbumId = $photo->getPhotoAlbum();
          if ($photoAlbum = $this->selectById($photoAlbumId)) {
            array_push($photoAlbums, $photoAlbum);
          }
        }
      }
    } else if ($pattern) {
      $photoAlbums = $this->selectLikePattern($pattern);
    } else if ($publicationDate) {
      $photoAlbums = $this->selectPublished($publicationDate);
    }

    if (count($photoAlbums) > 0) {
      $str .= $this->renderList($photoAlbums);
    } else {
      $str .= "\n<div class='photo_list_comment'>" . $this->websiteText[1] . "</div></td>";
    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the list of photo albums
  function renderList($photoAlbums = '') {
    global $gPhotoUrl;
    global $gJSNoStatus;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    if ($gIsPhoneClient) {
      $separator = '';
    } else {
      $separator = "\n</td><td>";
    }

    $str = '';

    $str .= "\n<div class='photo_album_list'>";

    if (!is_array($photoAlbums)) {
      $photoAlbums = $this->selectNotHidden();
    }

    if (is_array($photoAlbums) && count($photoAlbums) > 0) {

      if (!$gIsPhoneClient && !$this->preferenceUtils->getValue("PHOTO_NO_SLIDESHOW")) {
        $slideshowSpeed = $this->preferenceUtils->getValue("PHOTO_SLIDESHOW_SPEED");
        $str .= $this->colorboxUtils->renderJsColorbox() . $this->colorboxUtils->renderWebsiteColorbox($slideshowSpeed);
      }

      $str .= "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>";

      foreach ($photoAlbums as $photoAlbum) {
        $photoAlbumId = $photoAlbum->getId();
        $name = $photoAlbum->getName();
        $event = $photoAlbum->getEvent();
        $location = $photoAlbum->getLocation();
        $publicationDate = $photoAlbum->getPublicationDate();

        $strName = "<a href='$gPhotoUrl/display_album.php?photoAlbumId=$photoAlbumId' $gJSNoStatus title='" . $this->websiteText[2] . "'>$name</a>";

        $str .= "\n<tr>"
          . "<td><div class='photo_album_list_name'>$strName</div>"
          . $separator
          . "<div class='photo_album_list_event'>$event</div>"
          . $separator
          . "<div class='photo_album_list_location'>$location</div>"
          . $separator
          . "<div class='photo_album_list_date'>$publicationDate</div></td>"
          . "<tr>";

        $photos = $this->photoUtils->selectByPhotoAlbum($photoAlbumId);
        if (count($photos) > 0) {
          $str .= "\n<tr><td>";
          $str .= "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>";
          $nbPerRow = $this->preferenceUtils->getValue("PHOTO_NB_PER_ROW");
          for ($i = 0; $i < min($nbPerRow, count($photos)); $i++) {
            $photo = $photos[$i];
            $str .= "<td>" . $this->photoUtils->renderSmallImage($photo, true) . "</td>";
          }
          $str .= "\n</table>";
          $str .= "</td></tr>";
        }
      }

      $str .= "\n</table>";

    }

    $str .= "\n</div>";

    return($str);
  }

  // Render a photo album
  function render($photoAlbumId = '') {
    global $gPhotoUrl;
    global $gJsUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $displayAll = $this->preferenceUtils->getValue("PHOTO_DISPLAY_ALL");

    if (!isset($photoAlbumId)) {
      if (!$displayAll) {
        if ($photoAlbums = $this->selectNotHidden()) {
          if (count($photoAlbums) > 0) {
            $photoAlbum = $photoAlbums[0];
            $photoAlbumId = $photoAlbum->getId();
          }
        }
      }
    }

    $str = '';

    $str .= "\n<div class='photo_list'>";

    if ($gIsPhoneClient) {
      $hideSelector = $this->preferenceUtils->getValue("PHOTO_PHONE_HIDE_SELECTOR");
    } else {
      $hideSelector = $this->preferenceUtils->getValue("PHOTO_HIDE_SELECTOR");
    }

    if ($this->countAll() > 1 && !$hideSelector) {
      $photoAlbumList = array();
      if ($photoAlbums = $this->selectNotHidden()) {
        foreach ($photoAlbums as $wPhotoAlbum) {
          $wPhotoAlbumId = $wPhotoAlbum->getId();
          $wName = $wPhotoAlbum->getName();
          $photoAlbumList[$wPhotoAlbumId] = $wName;
        }
      }
      $strSelect = LibHtml::getSelectList("photoAlbumId", $photoAlbumList, $photoAlbumId, true);

      $str .= "\n<form action='$gPhotoUrl/display_album.php' method='post'>";
      $str .= "\n<div class='photo_list_selector'>";
      $str .= "\n" . $this->websiteText[0] . " $strSelect";
      $str .= "\n</div>";
      $str .= "\n</form>";
    }

    $photos = array();
    if (trim($photoAlbumId)) {
      // Get the photos of the selected album
      $photos = $this->photoUtils->selectByPhotoAlbum($photoAlbumId);
    } else if ($displayAll) {
      // Get the all the photos
      $photos = $this->photoUtils->selectAll();
    }

    $photoList = array();
    for ($i = 0; $i < count($photos); $i++) {
      $photo = $photos[$i];
      $photoList[$i] = $this->photoUtils->renderSmallPhoto($photo, $gIsPhoneClient);
    }

    // Get the number of photos per row
    if ($gIsPhoneClient) {
      $nbPerRow = 1;
    } else {
      $nbPerRow = $this->preferenceUtils->getValue("PHOTO_NB_PER_ROW");
    }

    // Make sure there is an upper limit set for the loop
    if (!$nbPerRow) {
      $nbPerRow = $this->nbPerRow;
    }

    if (!$gIsPhoneClient && !$this->preferenceUtils->getValue("PHOTO_NO_SLIDESHOW")) {
      $slideshowSpeed = $this->preferenceUtils->getValue("PHOTO_SLIDESHOW_SPEED");
      $str .= $this->colorboxUtils->renderJsColorbox() . $this->colorboxUtils->renderWebsiteColorbox($slideshowSpeed);
    }

    $str .= "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>";
    for ($i = 0; $i < count($photoList); $i = $i + $nbPerRow) {
      $str .= "\n<tr>";
      for ($j = 0; $j < $nbPerRow; $j++) {
        $str .= "\n<td style='vertical-align:top;'>"
          . LibUtils::getArrayValue($i+$j, $photoList) . "</td>";
      }
      $str .= "\n</tr>";
    }
    $str .= "\n</table>";

    $str .= "\n</div>";

    return($str);
  }

  // Get the cycle of album ids
  function getPhotoAlbumCycleIds() {
    $this->loadLanguageTexts();

    $list = array();

    if ($photoAlbums = $this->selectNotHidden()) {
      foreach ($photoAlbums as $photoAlbum) {
        $photoAlbumId = $photoAlbum->getId();
        $name = $photoAlbum->getName();
        $list['SYSTEM_PAGE_PHOTO_CYCLE' . $photoAlbumId] = $this->mlText[3] . ' ' . $name;
      }
    }

    return($list);
  }

  // Get the list of album ids
  function getPhotoAlbumListIds() {
    $this->loadLanguageTexts();

    $list = array();

    if ($photoAlbums = $this->selectNotHidden()) {
      foreach ($photoAlbums as $photoAlbum) {
        $photoAlbumId = $photoAlbum->getId();
        $name = $photoAlbum->getName();
        $list['SYSTEM_PAGE_PHOTO_LIST' . $photoAlbumId] = $this->mlText[0]
          . " " . $name;
      }
    }

    return($list);
  }

  // Delete a photo album
  function deleteAlbum($photoAlbumId) {
    // Delete the directory of the album
    if($photoAlbum = $this->selectById($photoAlbumId)) {
      $folderName = $photoAlbum->getFolderName();
      if ($folderName) {
        LibDir::deleteDirectory($this->photoUtils->imagePath . $folderName);
      }
    }

    $this->delete($photoAlbumId);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForPHotos() {
    global $gImagesUserUrl;
    global $gStylingImage;

    $str = "<div class='photo_list'>The photos of an album"
      . "<div class='photo_list_selector'>"
      . "Photo album: A photo album"
      . "</div>"
      . "<div class='photo_list_item'>"
      . "<div class='photo_list_name'>The name</div>"
      . "<div class='photo_list_image'>The photo"
      . "<img class='photo_list_image_file' src='$gStylingImage' title='The border of the photo' alt='' /></a>"
      . "</div>"
      . "<div class='photo_list_reference'>" 
      . "<span class='photo_list_reference_text'>The reference text</span>"
      . "<span class='photo_list_reference_value'>The reference value</span>"
      . "</div>"
      . "<div class='photo_list_icon'>The navigation icons</div>" 
      . "<div class='photo_list_price'>" 
      . "<span class='photo_list_price_text'>The price text</span>"
      . "<span class='photo_list_price_value'>The price value</span>"
      . "</div>"
      . "<div class='photo_list_description'>The description</div>"
      . "<div class='photo_list_comment'>The comment</div>"
      . "</div>"
      . "<div class='photo_cycle'>The images cycle"
      . "<div class='photo_cycle_image'>An image of the cycle"
      . "<img src='$gStylingImage' title='The border of the image' alt='' />"
      . "</div>"
      . "</div>"
      . "</div>";

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForAlbums() {
    $str = "<div class='photo_album_list'>The photo albums"
      . "<div class='photo_album_list_name'>The name of a photo album</div>" 
      . "<div class='photo_album_list_event'>The event of a photo album</div>" 
      . "<div class='photo_album_list_location'>The location of a photo album</div>" 
      . "<div class='photo_album_list_date'>The date of a photo album</div>" 
      . "</div>";

    return($str);
  }

}

?>
