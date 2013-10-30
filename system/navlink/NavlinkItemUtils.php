<?

class NavlinkItemUtils extends NavlinkItemDB {

  var $imageSize;
  var $imagePath;
  var $imageUrl;

  var $templateUtils;

  function NavlinkItemUtils() {
    $this->NavlinkItemDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageSize = 200000;
    $this->imagePath = $gDataPath . 'navlink/image/';
    $this->imageUrl = $gDataUrl . '/navlink/image';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imagePath)) {
      if (!is_dir($gDataPath . 'navlink')) {
        mkdir($gDataPath . 'navlink');
      }
      mkdir($this->imagePath);
      chmod($this->imagePath, 0755);
    }
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imagePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->imageIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (@file_exists($this->imagePath . $oneFile)) {
            @unlink($this->imagePath . $oneFile);
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
        if ($result = $this->dao->selectByImageOver($image)) {
          if ($result->getRowCount() < 1) {
            $isUsed = false;
          }
        }
      }
    }

    return($isUsed);
  }

  // Add a navigation link
  function add() {
    $navlink = new Navlink();
    $this->insert($navlink);
    $navlinkId = $this->getLastInsertId();

    return($navlinkId);
  }

  // Delete a navigation link
  function deleteNavlink($navlinkId) {
    $this->delete($navlinkId);
  }

  // Render
  function render($navlinkId) {
    global $gTemplateUrl;
    global $gHomeUrl;

    if (!$navlink = $this->selectById($navlinkId)) {
      return;
    }

    $text = $navlink->getText();
    $description = $navlink->getDescription();
    $image = $navlink->getImage();
    $imageOver = $navlink->getImageOver();
    $url = $navlink->getUrl();
    $blankTarget = $navlink->getBlankTarget();
    $templateModelId = $navlink->getTemplateModelId();

    $imagePath = $this->imagePath;
    $imageUrl = $this->imageUrl;

    $strUrl = '';
    if ($url) {
      $strUrl = $this->templateUtils->renderPageUrl($url, $templateModelId);
    }

    if ($blankTarget) {
      $strTarget = "onclick=\"window.open(this.href, '_blank'); return(false);\"";
    } else {
      $strTarget = '';
    }

    $str = '';

    // Prevent a gap below the image
    // A link image may show a gap below
    if (!$text) {
      $strNoGap = "style='font-size:0px;'";
    } else {
      $strNoGap = "";
    }

    $str .= "\n<div class='navlink' $strNoGap>";

    if ($image && @file_exists($imagePath . $image)) {
      if ($imageOver && is_file($imagePath . $imageOver)) {
        $strOnMouseOver = "onmouseover=\"src='$imageUrl/$imageOver'\" onmouseout=\"src='$imageUrl/$image'\"";
      } else {
        $strOnMouseOver = '';
      }

      $anchor = "<img class='navlink_image' src='$imageUrl/$image' $strOnMouseOver title='$description' alt='' />";

      // Insert a blank space only if there is some text
      if ($text) {
        $anchor .= ' ' . $text;
      }
    } else {
      $anchor = $text;
    }

    $strNoDottedBorder = LibJavaScript::getNoDottedBorder();

    if ($strUrl) {
      $str .= "<a href='$strUrl' $strNoDottedBorder $strTarget title='$description'>$anchor</a>";
    } else {
      $str .= $anchor;
    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the tags
  // When creating an element in the template system, it is necessary to create its tags
  // A dummy rendering is used for this operation
  function renderTags() {
    $str = "\n<div class='navlink'>";
    $str .= "\n</div>";

    return($str);
  }

}

?>
