<?

class LinkCategoryUtils extends LinkCategoryDB {

  var $mlText;
  var $websiteText;

  var $languageUtils;
  var $preferenceUtils;
  var $commonUtils;
  var $linkUtils;
  var $fileUploadUtils;

  function LinkCategoryUtils() {
    $this->LinkCategoryDB();
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  // Get the list of categories
  function getAll() {
    $this->loadLanguageTexts();

    $list = array();

    if ($linkCategories = $this->selectAll()) {
      foreach ($linkCategories as $linkCategory) {
        $linkCategoryId = $linkCategory->getId();
        $name = $linkCategory->getName();
        $list['SYSTEM_PAGE_LINK_LIST' . $linkCategoryId] = $this->mlText[1]
          . " " . $name;
      }
    }

    return($list);
  }

  // Get the next available list order
  function getNextListOrder() {
    $listOrder = 1;
    if ($categorys = $this->selectAll()) {
      $total = count($categorys);
      if ($total > 0) {
        $category = $categorys[$total - 1];
        $listOrder = $category->getListOrder() + 1;
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
    if ($linkCategory = $this->selectById($id)) {
      $listOrder = $linkCategory->getListOrder();
      if ($linkCategories = $this->selectByListOrder($listOrder)) {
        if (($listOrder == 0) || (count($linkCategories)) > 1) {
          $this->resetListOrder();
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($category = $this->selectById($id)) {
      $listOrder = $category->getListOrder();
      if ($category = $this->selectByNextListOrder($listOrder)) {
        return($category);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($category = $this->selectById($id)) {
      $listOrder = $category->getListOrder();
      if ($category = $this->selectByPreviousListOrder($listOrder)) {
        return($category);
      }
    }
  }

  // Rnnder a list of links
  function render($linkCategoryId) {
    global $gTemplateUrl;
    global $gLinkUrl;

    $this->loadLanguageTexts();

    $displayAll = $this->preferenceUtils->getValue("LINK_DISPLAY_ALL");

    $links = array();
    if ($linkCategoryId > 0 && !$displayAll) {
      $links = $this->linkUtils->selectByCategoryId($linkCategoryId);
    } else {
      $links = $this->linkUtils->selectAll();
    }

    $str = '';

    $str .= "\n<div class='link_list'>";

    $hideSelector = $this->preferenceUtils->getValue("LINK_HIDE_SELECTOR");

    if ($this->countAll() > 1 && !$hideSelector) {
      $linkCategoryList = array('-1' => '');
      if ($categories = $this->selectAll()) {
        foreach ($categories as $wLinkCategory) {
          $wLinkCategoryId = $wLinkCategory->getId();
          $wName = $wLinkCategory->getName();
          $linkCategoryList[$wLinkCategoryId] = $wName;
        }
      }
      $strSelect = LibHtml::getSelectList("linkCategoryId", $linkCategoryList, $linkCategoryId, true);

      $str .= "\n<form action='$gLinkUrl/display.php' method='post'>";
      $str .= "\n<div class='link_list_category'>";
      $str .= "\n" . $this->websiteText[0] . " $strSelect";
      $str .= "\n</div>";
      $str .= "\n</form>";
    }

    $str .= "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>";

    foreach ($links as $link) {
      $str .= $this->renderLink($link);
    }

    $str .= "\n</table>";

    $str .= "\n</div>";

    return($str);
  }

  // Render a link
  function renderLink($link) {
    global $gUtilsUrl;
    global $gIsPhoneClient;

    if (!$link) {
      return;
    }

    $name = $link->getName();
    $description = $link->getDescription();
    $image = $link->getImage();
    $url = $link->getUrl();

    // Resize the image to the following width
    $width = $this->linkUtils->getImageWidth();

    if ($image && file_exists($this->linkUtils->imageFilePath . $image)) {
      // A gif image cannot be resized
      // No support for the gif format due to copyrights issues
      if (!$this->fileUploadUtils->isGifImage($this->linkUtils->imageFilePath . $image)) {
        // The image is created on the fly
        $filename = $this->linkUtils->imageFilePath . $image;
        $filename = urlencode($filename);

        $imageUrl = $gUtilsUrl . "/printImage.php?filename="
          . $filename . "&amp;width=" . $width . "&amp;height=";
      } else {
        $imageUrl = $this->linkUtils->imageFileUrl . '/' . $image;
      }
      $strImg = "<a onclick=\"window.open(this.href, '_blank'); return(false);\" href='$url'>"
        . "<img class='link_list_image_file' src='$imageUrl' title='$description' alt='' width='$width' />"
        . "</a>";
    } else {
      $strImg = "&nbsp;";
    }

    if ($name) {
      $strName = "<a onclick=\"window.open(this.href, '_blank'); return(false);\" href='$url' title='$description'>$name</a>";
    } else {
      $strName = '';
    }

    if ($gIsPhoneClient) {
      $separator = '';
    } else {
      $separator = "\n</td><td>";
    }

    $str = "\n<tr><td>"
      . "\n<div class='link_list_image'>$strImg</div>"
      . $separator
      . "\n<div class='link_list_name'>$strName</div>"
      . "\n</td></tr>";

    return($str);
  }

  // Render an image cycle of the client images
  function renderImageCycleInTemplateElement($linkCategoryId) {
    $width = $this->preferenceUtils->getValue("LINK_CYCLE_WIDTH_TEMPLATE");

    $str = $this->renderImageCycle($linkCategoryId, $width);

    return($str);
  }

  // Render an image cycle of the client images
  function renderImageCycleInPage($linkCategoryId) {
    $width = $this->preferenceUtils->getValue("LINK_CYCLE_WIDTH_PAGE");

    $str = $this->renderImageCycle($linkCategoryId, $width);

    return($str);
  }

  // Render an image cycle of the client images
  function renderImageCycle($linkCategoryId, $width) {
    global $gUtilsUrl;

    $str = '';

    $items = array();

    $displayAll = $this->preferenceUtils->getValue("LINK_DISPLAY_ALL");

    $links = array();
    if ($linkCategoryId > 0 && !$displayAll) {
      $links = $this->linkUtils->selectByCategoryId($linkCategoryId);
    } else {
      $links = $this->linkUtils->selectAll();
    }

    foreach ($links as $link) {
      $image = $link->getImage();
      $url = $link->getUrl();

      if ($image && file_exists($this->linkUtils->imageFilePath . $image)) {
        if (!$this->fileUploadUtils->isGifImage($this->linkUtils->imageFilePath . $image)) {
          $filename = $this->linkUtils->imageFilePath . $image;
          $filename = urlencode($filename);
          $imageSrc = $gUtilsUrl . "/printImage.php?filename="
            . $filename . "&amp;width=" . $width . "&amp;height=";
        } else {
          $imageSrc = $this->linkUtils->imageFileUrl . '/' . $image;
        }

        $height = LibImage::getHeightFromWidth($this->linkUtils->imageFilePath . $image, $width);

        $item = "<div class='link_cycle_image'>"
          . "<a onclick=\"window.open(this.href, '_blank'); return(false);\" href='$url'>"
          . "<img src='$imageSrc' border='0' title='' width='$width' height='$height' />"
          . "</a>"
          . "</div>";

        array_push($items, $item);
      }
    }

    if (count($items) > 0) {
      $randomNumber = LibUtils::generateUniqueId();

      $timeout = $this->preferenceUtils->getValue("LINK_CYCLE_TIMEOUT");

      $str = "<div class='link_cycle'>"
        . $this->commonUtils->renderImageCycle('link_cycle_' . $randomNumber, $items, true, $timeout)
        . "</div>";
    }

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElements() {
    global $gStylingImage;

    $str = "<div class='link_list'>The favorite links"
      . "<div class='link_list_category'>"
      . "Link category: A category"
      . "</div>"
      . "<div class='link_list_image'>The image of the link"
      . "<img class='link_list_image_file' src='$gStylingImage' title='The border of the image of the link' alt='' />"
      . "</div>"
      . "<div class='link_list_name'>The name</div>"
      . "</div>"
      . "<div class='link_cycle'>The images cycle"
      . "<div class='link_cycle_image'>An image of the cycle"
      . "<img src='$gStylingImage' title='The border of the image' alt='' />"
      . "</div>"
      . "</div>";

    return($str);
  }

}

?>
