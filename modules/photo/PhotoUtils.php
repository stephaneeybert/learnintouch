<?

class PhotoUtils extends PhotoDB {

  var $mlText;
  var $websiteText;

  var $imagePath;
  var $imageUrl;
  var $imageSize;

  var $maxWidth;
  var $maxHeight;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $commonUtils;
  var $photoAlbumUtils;
  var $photoFormatUtils;
  var $photoAlbumFormatUtils;
  var $shopItemUtils;
  var $fileUploadUtils;

  function PhotoUtils() {
    $this->PhotoDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imagePath = $gDataPath . 'photo/image/';
    $this->imageUrl = $gDataUrl . '/photo/image';
    $this->imageSize = 200000;
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imagePath)) {
      if (!is_dir($gDataPath . 'photo')) {
        mkdir($gDataPath . 'photo');
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

    $this->preferences = array(
      "PHOTO_NB_PER_ROW" =>
      array($this->mlText[32], $this->mlText[33], PREFERENCE_TYPE_RANGE, array(1, 10, 3)),
        "PHOTO_SLIDESHOW_SPEED" =>
        array($this->mlText[48], $this->mlText[49], PREFERENCE_TYPE_RANGE, array(1, 60, 5)),
          "PHOTO_NO_SLIDESHOW" =>
          array($this->mlText[53], $this->mlText[54], PREFERENCE_TYPE_BOOLEAN, ''),
          "PHOTO_NO_ZOOM" =>
          array($this->mlText[60], $this->mlText[61], PREFERENCE_TYPE_BOOLEAN, ''),
            "PHOTO_WATERMARK" =>
            array($this->mlText[22], $this->mlText[23], PREFERENCE_TYPE_TEXT, ''),
              "PHOTO_BOTTOM_WATERMARK" =>
              array($this->mlText[16], $this->mlText[17], PREFERENCE_TYPE_TEXT, ''),
                "PHOTO_ON_SALE" =>
                array($this->mlText[9], $this->mlText[18], PREFERENCE_TYPE_BOOLEAN, ''),
                  "PHOTO_FREE_DOWNLOAD" =>
                  array($this->mlText[25], $this->mlText[36], PREFERENCE_TYPE_BOOLEAN, ''),
                    "PHOTO_DISPLAY_ALL" =>
                    array($this->mlText[4], $this->mlText[5], PREFERENCE_TYPE_BOOLEAN, ''),
                      "PHOTO_LIST_HIDE_DESCRIPTION" =>
                      array($this->mlText[26], $this->mlText[27], PREFERENCE_TYPE_BOOLEAN, ''),
                        "PHOTO_LIST_HIDE_REFERENCE" =>
                        array($this->mlText[55], $this->mlText[56], PREFERENCE_TYPE_BOOLEAN, ''),
                          "PHOTO_LIST_HIDE_COMMENT" =>
                          array($this->mlText[28], $this->mlText[29], PREFERENCE_TYPE_BOOLEAN, ''),
                            "PHOTO_HIDE_SELECTOR" =>
                            array($this->mlText[6], $this->mlText[7], PREFERENCE_TYPE_BOOLEAN, ''),
                              "PHOTO_PHONE_HIDE_SELECTOR" =>
                              array($this->mlText[40], $this->mlText[41], PREFERENCE_TYPE_BOOLEAN, ''),
                                "PHOTO_PHONE_LIST_HIDE_DESCRIPTION" =>
                                array($this->mlText[38], $this->mlText[39], PREFERENCE_TYPE_BOOLEAN, ''),
                                  "PHOTO_IMAGE_LENGTH_AXIS" =>
                                  array($this->mlText[14], $this->mlText[15], PREFERENCE_TYPE_SELECT, array('IMAGE_LENGTH_IS_HEIGHT' => $this->mlText[42], 'IMAGE_LENGTH_IS_WIDTH' => $this->mlText[43])),
                                    "PHOTO_LIST_STEP" =>
                                    array($this->mlText[51], $this->mlText[52], PREFERENCE_TYPE_SELECT, array(10 => "10", 20 => "20", 50 => "50", 100 => "100")),
                                      "PHOTO_DEFAULT_MINI_WIDTH" =>
                                      array($this->mlText[30], $this->mlText[31], PREFERENCE_TYPE_TEXT, 100),
                                        "PHOTO_DEFAULT_LARGE_WIDTH" =>
                                        array($this->mlText[34], $this->mlText[35], PREFERENCE_TYPE_TEXT, 400),
                                          "PHOTO_PHONE_DEFAULT_MINI_WIDTH" =>
                                          array($this->mlText[10], $this->mlText[11], PREFERENCE_TYPE_TEXT, 100),
                                            "PHOTO_PHONE_DEFAULT_LARGE_WIDTH" =>
                                            array($this->mlText[12], $this->mlText[13], PREFERENCE_TYPE_TEXT, 200),
                                              "PHOTO_CYCLE_WIDTH_TEMPLATE" =>
                                              array($this->mlText[44], $this->mlText[45], PREFERENCE_TYPE_TEXT, 100),
                                                "PHOTO_CYCLE_WIDTH_PAGE" =>
                                                array($this->mlText[46], $this->mlText[47], PREFERENCE_TYPE_TEXT, 100),
                                                  "PHOTO_CYCLE_TIMEOUT" =>
                                                  array($this->mlText[57], $this->mlText[58], PREFERENCE_TYPE_RANGE, array(1, 60, 10)),
                                                  );

    $this->preferenceUtils->init($this->preferences);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imagePath);
    while ($oneDir = readdir($handle)) {
      if ($oneDir != "." && $oneDir != ".." && is_dir($this->imagePath . $oneDir)) {
        $dirHandle = opendir($this->imagePath . $oneDir);
        while ($oneFile = readdir($dirHandle)) {
          if ($oneFile != "." && $oneFile != "..") {
            if (!$this->imageIsUsed($oneDir, $oneFile)) {
              $oneFile = str_replace(" ", "\\ ", $oneFile);
              if (@file_exists($this->imagePath . $oneDir . '/' . $oneFile)) {
                @unlink($this->imagePath . $oneDir . '/' . $oneFile);
              }
            }
          }
        }
        closedir($dirHandle);
      }
    }
    closedir($handle);
  }

  // Check if an image is being used
  function imageIsUsed($album, $image) {
    $isUsed = true;

    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByAlbumAndImage($album, $image)) {
      if ($result->getRowCount() < 1) {
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  function noSlideShow($photoAlbum) {
    $noSlideShow = $this->preferenceUtils->getValue("PHOTO_NO_SLIDESHOW");
    if (!$noSlideShow && $photoAlbum) {
      $noSlideShow = $photoAlbum->getNoSlideShow();
    }

    return($noSlideShow);
  }

  function noZoom($photoAlbum) {
    $no = $this->preferenceUtils->getValue("PHOTO_NO_ZOOM");
    if (!$no && $photoAlbum) {
      $no = $photoAlbum->getNoZoom();
    }

    return($no);
  }

  // Empty an album
  // It deletes all the photos of an album
  function emptyAlbum($photoAlbumId) {
    if ($photos = $this->selectByPhotoAlbum($photoAlbumId)) {
      foreach ($photos as $photo) {
        $photoId = $photo->getId();
        $this->deletePhoto($photoId);
      }
    }
  }

  // Delete a photo
  function deletePhoto($photoId) {
    $this->delete($photoId);
  }

  // Check if the length of the images is considered to be a height
  function imageLengthIsHeight() {
    if ($this->imageLengthIsWidth()) {
      return(false);
    }

    return(true);
  }

  // Check if the length of the images is considered to be a width
  function imageLengthIsWidth() {
    $imageLengthAxis = $this->preferenceUtils->getValue("PHOTO_IMAGE_LENGTH_AXIS");

    if ($imageLengthAxis == 'IMAGE_LENGTH_IS_WIDTH') {
      return(true);
    }

    return(false);
  }

  // Get the price of a photo
  // If a format is specified then get the price for the format if possible
  function getPhotoPrice($photoId, $photoFormatId = '') {
    $price = 0;

    if ($photo = $this->selectById($photoId)) {
      $price = $photo->getPrice();
      $photoAlbumId = $photo->getPhotoAlbum();

      if (!$price) {
        if ($photoFormatId) {
          if ($photoAlbumFormat = $this->photoAlbumFormatUtils->selectByPhotoFormatIdAndPhotoAlbumId($photoFormatId, $photoAlbumId)) {
            $price = $photoAlbumFormat->getPrice();
          }
        }
      }

      if (!$price) {
        if ($photoAlbum = $this->photoAlbumUtils->selectById($photoAlbumId)) {
          $price = $photoAlbum->getPrice();
        }
      }

      if (!$price) {
        if ($photoFormatId) {
          if ($photoFormat = $this->photoFormatUtils->selectById($photoFormatId)) {
            $price = $photoFormat->getPrice();
          }
        }
      }
    }

    return($price);
  }

  // Render a photo in a small format
  function renderSmallPhoto($photo) {
    global $gJSNoStatus;
    global $gPhotoUrl;
    global $gUtilsUrl;
    global $gImagesUserUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    if (!$photo) {
      return;
    }

    $photoId = $photo->getId();
    $image = $photo->getImage();
    $reference = $photo->getReference();
    $name = $photo->getName();
    $description = $photo->getDescription();
    $comment = $photo->getComment();
    $photoAlbumId = $photo->getPhotoAlbum();
    $price = $this->getPhotoPrice($photoId);

    // Get the album name
    $folderName = '';
    if($photoAlbum = $this->photoAlbumUtils->selectById($photoAlbumId)) {
      $folderName = $photoAlbum->getFolderName();
    }

    // If no image is specified then render nothing
    if (!$image || !file_exists($this->imagePath . $folderName . '/' . $image)) {
      return;
    }

    if ($gIsPhoneClient) {
      $hideDescription = $this->preferenceUtils->getValue("PHOTO_PHONE_LIST_HIDE_DESCRIPTION");
    } else {
      $hideDescription = $this->preferenceUtils->getValue("PHOTO_LIST_HIDE_DESCRIPTION");
    }

    if ($gIsPhoneClient) {
      $hideComment = 1;
    } else {
      $hideComment = $this->preferenceUtils->getValue("PHOTO_LIST_HIDE_COMMENT");
    }

    $str = '';

    $str .= "<div class='photo_list_item'>";

    if ($name) {
      $str .= "<div class='photo_list_name'>$name</div>";
    }

    $str .= $this->renderSmallImage($photo);

    $hideReference = $this->preferenceUtils->getValue("PHOTO_LIST_HIDE_REFERENCE");

    if (!$hideReference && $reference) {
      $str .= "<div class='photo_list_reference'>"
        . "<span class='photo_list_reference_text'>" . $this->websiteText[0] . "</span>"
        . " <span class='photo_list_reference_value'>" . $reference . "</span>"
        . "</div>";
    }

    if (!$hideDescription && $description) {
      $str .= "<div class='photo_list_description'>$description</div>";
    }

    if (!$hideComment && $comment) {
      $comment = nl2br($comment);
      $str .= "<div class='photo_list_comment'>" . $comment . "</div>";
    }

    $onSale = $this->preferenceUtils->getValue("PHOTO_ON_SALE");
    if ($onSale) {
      $currency = $this->shopItemUtils->getCurrency();
      $strPrice = "<span class='photo_list_price_text'>" . $this->websiteText[8] . "</span>"
        . ' ' . "<span class='photo_list_price_value'>" . $price . "</span>" . ' ' . $currency;
      $str .= "<div class='photo_list_price'>$strPrice</div>";

      $str .= "<div class='photo_list_icon'>";

      $str .= $this->renderShopIcons($photoId);

      $url = "$gPhotoUrl/display_photo.php?photoId=$photoId";
      $str .= " <a href='$url' $gJSNoStatus title='" .  $this->websiteText[50] . "'>" . "<img src='$gImagesUserUrl/" . IMAGE_PHOTO_VIEW . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

      $freeDownload = $this->preferenceUtils->getValue("PHOTO_FREE_DOWNLOAD");
      if ($freeDownload) {
        $filename = $this->imagePath . $folderName . '/' . $image;
        $url = "$gUtilsUrl/download.php?filename=$filename";
        $str .= " <a href='$url' $gJSNoStatus title='" .  $this->websiteText[37] . "'>" . "<img src='$gImagesUserUrl/" . IMAGE_PHOTO_DOWNLOAD . "' class='no_style_image_icon' title='' alt='' />" . "</a>";
      }

      $str .= "</div>";
    }

    $str .= "</div>";

    return($str);
  }

  // Render a photo in a small format
  function renderSmallImage($photo, $linktoAlbums = false) {
    global $gJSNoStatus;
    global $gPhotoUrl;
    global $gIsPhoneClient;

    if (!$photo) {
      return;
    }

    $this->loadLanguageTexts();

    $photoId = $photo->getId();
    $image = $photo->getImage();
    $name = $photo->getName();
    $description = $photo->getDescription();
    $photoAlbumId = $photo->getPhotoAlbum();

    // Get the album name
    $folderName = '';
    if($photoAlbum = $this->photoAlbumUtils->selectById($photoAlbumId)) {
      $folderName = $photoAlbum->getFolderName();
    }

    // If no image is specified then render nothing
    if (!$image || !file_exists($this->imagePath . $folderName . '/' . $image)) {
      return;
    }

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("PHOTO_PHONE_DEFAULT_MINI_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("PHOTO_DEFAULT_MINI_WIDTH");
    }

    // A gif image cannot be resized
    // No support for the gif format due to copyrights issues
    if (!$this->fileUploadUtils->isGifImage($this->imagePath . $folderName . '/' . $image)) {
      // The image is created on the fly
      $filename = $this->imagePath . $folderName . '/' . $image;

      $imageLengthIsHeight = $this->imageLengthIsHeight();
      if ($imageLengthIsHeight) {
        $width = LibImage::getWidthFromHeight($filename, $width);
      }

      $filename = urlencode($filename);

      $imgSrc = $gPhotoUrl . "/printImage.php?filename=" . $filename . "&amp;width=" . $width . "&amp;height=";
    } else {
      $imgSrc = "$this->imageUrl/$folderName/$image";
    }

    $noSlideShow = $this->noSlideShow($photoAlbum);

    if ($linktoAlbums) {
      $strImg = "<a href='$gPhotoUrl/display_album.php?photoAlbumId=$photoAlbumId' $gJSNoStatus title='" .  $this->websiteText[59] . "'>"
        . "<img class='photo_list_image_file' src='$imgSrc' width='$width' title='$description' alt='' /></a>";
    } else if ($gIsPhoneClient || $noSlideShow) {
      $strImg = "<a href='$gPhotoUrl/display_photo.php?photoId=$photoId' $gJSNoStatus title='" .  $this->websiteText[50] . "'>"
        . "<img class='photo_list_image_file' src='$imgSrc' width='$width' title='$description' alt='' /></a>";
    } else {
      $strImg = "<a href='$this->imageUrl/$folderName/$image' title='$name' rel='no_style_colorbox' $gJSNoStatus>"
        . "<img class='photo_list_image_file' src='$imgSrc' width='$width' title='' alt='' /></a>";
    }

    $str = "<div class='photo_list_image'>" . $strImg . "</div>";

    return($str);
  }

  // Render a photo
  function renderBigPhoto($photo) {
    global $gPhotoUrl;
    global $gJSNoStatus;
    global $gIsPhoneClient;

    if (!$photo) {
      return;
    }

    $photoId = $photo->getId();
    $name = $photo->getName();
    $description = $photo->getDescription();
    $comment = $photo->getComment();
    $image = $photo->getImage();
    $reference = $photo->getReference();
    $url = $photo->getUrl();
    $price = $this->getPhotoPrice($photoId);
    $photoAlbumId = $photo->getPhotoAlbum();

    // Get the album name
    $folderName = '';
    if($photoAlbum = $this->photoAlbumUtils->selectById($photoAlbumId)) {
      $folderName = $photoAlbum->getFolderName();
    }

    // If no image is specified then render nothing
    if (!$image || !file_exists($this->imagePath . $folderName . '/' . $image)) {
      return;
    }

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("PHOTO_PHONE_DEFAULT_LARGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("PHOTO_DEFAULT_LARGE_WIDTH");
    }

    // A gif image cannot be resized
    // No support for the gif format due to copyright issues
    if (!$this->fileUploadUtils->isGifImage($this->imagePath . $folderName . '/' . $image)) {
      // The image is created on the fly
      $filename = $this->imagePath . $folderName . '/' . $image;

      $imageLengthIsHeight = $this->imageLengthIsHeight();
      if ($imageLengthIsHeight) {
        $width = LibImage::getWidthFromHeight($filename, $width);
      }

      $filename = urlencode($filename);

      $imgSrc = $gPhotoUrl . "/printImage.php?filename=" . $filename . "&amp;width=" . $width . "&amp;height=";
    } else {
      $imgSrc = $this->imageUrl . '/' . $folderName . '/' . $image;
    }

    if ($url) {
      $title = $url;
    } else {
      $title = '';
    }

    if (!$this->noZoom($photoAlbum)) {
      $strImg = "<div style='overflow: auto;'><a href='$this->imageUrl/$folderName/$image' class='zoomable' title='$title'>"
        . "<img class='photo_item_image_file' src='$imgSrc' width='$width' title='$title' alt='' />"
        . "</a></div>";
    } else {
      $strImg = "<img class='photo_item_image_file' src='$imgSrc' width='$width' title='$title' alt='' />";
    }

    if ($url) {
      $strImg = "<a onclick=\"window.open(this.href, '_blank'); return(false);\" href='$url'>$strImg</a>";
    }

    $str = '';
    $str .= "\n<div class='photo_item_photo'>";
    if ($name) {
      $str .= "\n<div class='photo_item_name'>$name</div>";
    }
    if ($description) {
      $str .= "\n<div class='photo_item_description'>$description</div>";
    }
    $str .= "\n<div class='photo_item_image'>$strImg</div>";
    if ($reference) {
      $str .= "\n<div class='photo_item_reference'>"
        . "<span class='photo_item_reference_text'>" . $this->websiteText[0] . "</span>"
        . " <span class='photo_item_reference_value'>" . $reference . "</span>"
        . "</div>";
    }

    if ($comment) {
      $str .= "\n<div class='photo_item_comment'>$comment</div>";
    }

    $onSale = $this->preferenceUtils->getValue("PHOTO_ON_SALE");
    if ($onSale) {
      $currency = $this->shopItemUtils->getCurrency();
      $strPrice = "<span class='photo_item_price_text'>" . $this->websiteText[8] . "</span>"
        . ' ' . "<span class='photo_item_price_value'>" . $price . "</span>" . ' ' . $currency;
      $str .= "\n<div class='photo_item_price'>$strPrice</div>";
    }

    $str .= "\n</div>";

    return($str);
  }

  // Get the next available list order
  function getNextListOrder($photoAlbumId) {
    $listOrder = 1;
    if ($photos = $this->selectByPhotoAlbum($photoAlbumId)) {
      $total = count($photos);
      if ($total > 0) {
        $photo = $photos[$total - 1];
        $listOrder = $photo->getListOrder() + 1;
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
    if ($photo = $this->selectById($id)) {
      $listOrder = $photo->getListOrder();
      $photoAlbumId = $photo->getPhotoAlbum();
      if ($photos = $this->selectByListOrder($photoAlbumId, $listOrder)) {
        if (($listOrder == 0) || (count($photos)) > 1) {
          $this->resetListOrder($photoAlbumId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($photo = $this->selectById($id)) {
      $listOrder = $photo->getListOrder();
      $photoAlbumId = $photo->getPhotoAlbum();
      if ($photo = $this->selectByNextListOrder($photoAlbumId, $listOrder)) {
        return($photo);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($photo = $this->selectById($id)) {
      $listOrder = $photo->getListOrder();
      $photoAlbumId = $photo->getPhotoAlbum();
      if ($photo = $this->selectByPreviousListOrder($photoAlbumId, $listOrder)) {
        return($photo);
      }
    }
  }

  // Render the shopping cart icons
  function renderShopIcons($photoId) {
    global $gJSNoStatus;
    global $gImagesUserUrl;
    global $gShopUrl;

    $this->loadLanguageTexts();

    $itemType = SHOP_CART_PHOTO;
    $strAddToSelection = "\n<a href='$gShopUrl/item/selection.php?itemType=$itemType&amp;itemId=$photoId' $gJSNoStatus title='" . $this->websiteText[21] . "'>" . "<img src='$gImagesUserUrl/" .  IMAGE_PHOTO_ADD_TO_SELECTION . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    $strViewSelection = "\n<a href='$gShopUrl/item/selection.php' $gJSNoStatus title='" .
      $this->websiteText[24] . "'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_PHOTO_SELECTION . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    $itemType = SHOP_CART_PHOTO;
    $strAddToCart = "\n<a href='$gShopUrl/item/addToCart.php?itemType=$itemType&amp;itemId=$photoId&amp;quantity=1' $gJSNoStatus title='" . $this->websiteText[19] . "'>" . "<img src='$gImagesUserUrl/" . IMAGE_PHOTO_ADD_TO_CART . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    $strViewCart = "\n<a href='$gShopUrl/item/displayCart.php' $gJSNoStatus title='" .  $this->websiteText[20] .
      "'>" . "<img src='$gImagesUserUrl/" . IMAGE_PHOTO_CART . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

    $str = $strAddToSelection . ' ' . $strViewSelection . ' ' . $strAddToCart . ' ' . $strViewCart;

    return($str);
  }

  // Render a photo
  function render($photoId) {
    global $gPhotoUrl;
    global $gJSNoStatus;
    global $gImagesUserUrl;

    if (!$photoId) {
      if ($photos = $this->selectAll()) {
        if (count($photos) > 0) {
          $photo = $photos[0];
          $photoId = $photo->getId();
        }
      }
    }

    if (!$photo = $this->selectById($photoId)) {
      return;
    }

    $this->loadLanguageTexts();

    $str = '';

    $str .= "\n<div class='photo_item'>";

    $onSale = $this->preferenceUtils->getValue("PHOTO_ON_SALE");

    if ($onSale) {
      $str .= "\n<div class='photo_item_icon'>"
        . $this->renderShopIcons($photoId)
        . "\n</div>";
    }

    $str .= "\n<div class='photo_item_nav_buttons'>";

    if ($previousPhoto = $this->selectPrevious($photo->getId())) {
      $previousPhotoId = $previousPhoto->getId();
      if ($previousPhotoId > 0) {
        $str .= " <a href='$gPhotoUrl/display_photo.php?photoId=$previousPhotoId' $gJSNoStatus title='" . $this->websiteText[2] . "'>";
        $str .= "<img src='$gImagesUserUrl/" . IMAGE_COMMON_LEFT . "' class='no_style_image_icon' title='' alt='' /></a>";
      }
    }

    if ($nextPhoto = $this->selectNext($photo->getId())) {
      $nextPhotoId = $nextPhoto->getId();
      if ($nextPhotoId > 0) {
        $str .= " <a href='$gPhotoUrl/display_photo.php?photoId=$nextPhotoId' $gJSNoStatus title='" .  $this->websiteText[3] . "'>";
        $str .= "<img src='$gImagesUserUrl/" . IMAGE_COMMON_RIGHT . "' class='no_style_image_icon' title='' alt='' /></a>";
      }
    }

    $photoAlbumId = '';
    if ($photo = $this->selectById($photoId)) {
      $photoAlbumId = $photo->getPhotoAlbum();
    }

    $str .= " <a href='$gPhotoUrl/display_album.php?photoAlbumId=$photoAlbumId' $gJSNoStatus title='"
      . $this->websiteText[1] . "'>";
    $str .= "<img src='$gImagesUserUrl/" . IMAGE_COMMON_UP . "' class='no_style_image_icon' title='' alt='' /></a>";

    $str .= "\n</div>";

    $str .= $this->renderBigPhoto($photo);

    $str .= "\n</div>";

    return($str);
  }

  // Render an image cycle of the album photos
  function renderImageCycleInTemplateElement($photoAlbumId) {
    $width = $this->preferenceUtils->getValue("PHOTO_CYCLE_WIDTH_TEMPLATE");

    $str = $this->renderImageCycle($photoAlbumId, $width);

    return($str);
  }

  // Render an image cycle of the album photos
  function renderImageCycleInPage($photoAlbumId) {
    $width = $this->preferenceUtils->getValue("PHOTO_CYCLE_WIDTH_PAGE");

    $str = $this->renderImageCycle($photoAlbumId, $width);

    return($str);
  }

  // Render an image cycle of the album photos
  function renderImageCycle($photoAlbumId, $width) {
    global $gPhotoUrl;

    $str = '';

    $items = array();

    $photos = $this->selectByPhotoAlbum($photoAlbumId);

    $folderName = '';
    if ($photoAlbumId) {
      $photoAlbum = $this->photoAlbumUtils->selectById($photoAlbumId);
      $folderName = $photoAlbum->getFolderName();
    }

    foreach ($photos as $photo) {
      $image = $photo->getImage();
      $url = $photo->getUrl();
      $name = $photo->getName();

      if ($image && @file_exists($this->imagePath . $folderName . '/' . $image)) {
        if (!$this->fileUploadUtils->isGifImage($this->imagePath . $folderName . '/' . $image)) {
          $filename = $this->imagePath . $folderName . '/' . $image;
          $filename = urlencode($filename);
          $imageSrc = $gPhotoUrl . "/printImage.php?filename=" . $filename . "&amp;width=" . $width . "&amp;height=";
        } else {
          $imageSrc = "$this->imageUrl/$folderName/$image";
        }

        $height = LibImage::getHeightFromWidth($this->imagePath . $folderName . '/' . $image, $width);

        $item = "<div class='photo_cycle_image'>"
          . "<a onclick=\"window.open(this.href, '_blank'); return(false);\" href='$url'>"
          . "<img src='$imageSrc' border='0' title='' width='$width' height='$height' />"
          . "</a>"
          . "<div class='photo_item_name'>$name</div>"
          . "</div>";

        array_push($items, $item);
      }
    }

    if (count($items) > 0) {
      $timeout = $this->preferenceUtils->getValue("PHOTO_CYCLE_TIMEOUT");

      $str = "<div class='photo_cycle'>"
        . $this->commonUtils->renderImageCycle('photo_cycle_' . $photoAlbumId, $items, true, $timeout)
        . "</div>";
    }

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForOnePhoto() {
    global $gImagesUserUrl;
    global $gStylingImage;

    $str = "<div class='photo_item'>A photo"
      . "<div class='photo_item_icon'>"
      . " <img src='$gImagesUserUrl/" . IMAGE_PHOTO_ADD_TO_SELECTION . "' class='no_style_image_icon' title='' alt='' />"
      . " <img src='$gImagesUserUrl/" . IMAGE_PHOTO_SELECTION . "' class='no_style_image_icon' title='' alt='' />"
      . " <img src='$gImagesUserUrl/" . IMAGE_PHOTO_ADD_TO_CART . "' class='no_style_image_icon' title='' alt='' />"
      . " <img src='$gImagesUserUrl/" . IMAGE_PHOTO_CART . "' class='no_style_image_icon' title='' alt='' />"
      . "</div>"
      . "<div class='photo_item_nav_buttons'>"
      . " <img src='$gImagesUserUrl/" . IMAGE_COMMON_LEFT . "' class='no_style_image_icon' title='' alt='' />"
      . " <img src='$gImagesUserUrl/" . IMAGE_COMMON_RIGHT . "' class='no_style_image_icon' title='' alt='' />"
      . " <img src='$gImagesUserUrl/" . IMAGE_COMMON_UP . "' class='no_style_image_icon' title='' alt='' />"
      . "</div>"
      . "<div class='photo_item_photo'>"
      . "<div class='photo_item_name'>The name</div>"
      . "<div class='photo_item_description'>The description</div>"
      . "<div class='photo_item_image'>The image"
      . "<img class='photo_item_image_file' src='$gStylingImage' title='The border of the image' alt='' />"
      . "</div>"
      . "<div class='photo_item_reference'>"
      . "<span class='photo_item_reference_text'>The reference text</span>"
      . "<span class='photo_item_reference_value'>The reference value</span>"
      . "</div>"
      . "<div class='photo_item_comment'>The comment</div>"
      . "<div class='photo_item_price'>"
      . "<span class='photo_item_price_text'>The price text</span>"
      . "<span class='photo_item_price_value'>The price</span>"
      . "</div>"
      . "</div>"
      . "</div>"
      . "<div class='photo_cycle'>The photos cycle"
      . "<div class='photo_cycle_image'>An image of the cycle"
      . "<img src='$gStylingImage' title='The border of the image' alt='' />"
      . "</div>"
      . "</div>";

    return($str);
  }

}

?>
