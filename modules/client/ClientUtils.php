<?

class ClientUtils extends ClientDB {

  var $mlText;

  var $imagePath;
  var $imageUrl;
  var $imageSize;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $commonUtils;
  var $fileUploadUtils;

  function ClientUtils() {
    $this->ClientDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageSize = 200000;
    $this->imagePath = $gDataPath . 'client/image/';
    $this->imageUrl = $gDataUrl . '/client/image';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imagePath)) {
      if (!is_dir($gDataPath . 'client')) {
        mkdir($gDataPath . 'client');
      }
      mkdir($this->imagePath);
      chmod($this->imagePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "CLIENT_DEFAULT_WIDTH" =>
      array($this->mlText[0], $this->mlText[1], PREFERENCE_TYPE_TEXT, 100),
        "CLIENT_PHONE_DEFAULT_WIDTH" =>
        array($this->mlText[4], $this->mlText[5], PREFERENCE_TYPE_TEXT, 100),
          "CLIENT_CYCLE_WIDTH_TEMPLATE" =>
          array($this->mlText[2], $this->mlText[3], PREFERENCE_TYPE_TEXT, 100),
            "CLIENT_CYCLE_WIDTH_PAGE" =>
            array($this->mlText[6], $this->mlText[7], PREFERENCE_TYPE_TEXT, 100),
              "CLIENT_CYCLE_TIMEOUT" =>
              array($this->mlText[12], $this->mlText[13], PREFERENCE_TYPE_RANGE, array(1, 60, 10)),
              );

    $this->preferenceUtils->init($this->preferences);
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
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Get the next available list order
  function getNextListOrder() {
    $listOrder = 1;
    if ($clients = $this->selectAll()) {
      $total = count($clients);
      if ($total > 0) {
        $client = $clients[$total - 1];
        $listOrder = $client->getListOrder() + 1;
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
    if ($client = $this->selectById($id)) {
      $listOrder = $client->getListOrder();
      if ($clients = $this->selectByListOrder($listOrder)) {
        if (($listOrder == 0) || (count($clients)) > 1) {
          $this->resetListOrder();
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($client = $this->selectById($id)) {
      $listOrder = $client->getListOrder();
      if ($client = $this->selectByNextListOrder($listOrder)) {
        return($client);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($client = $this->selectById($id)) {
      $listOrder = $client->getListOrder();
      if ($client = $this->selectByPreviousListOrder($listOrder)) {
        return($client);
      }
    }
  }

  // Delete a client refrerence
  function deleteClient($clientId) {
    $this->delete($clientId);
  }

  // Render the list of client references
  function render() {
    global $gUtilsUrl;
    global $gIsPhoneClient;

    $clients = $this->selectAll();

    if (count($clients) == 0) {
      return;
    }

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("CLIENT_PHONE_DEFAULT_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("CLIENT_DEFAULT_WIDTH");
    }

    $str = '';

    $str .= "\n<div class='client_list'>";

    $str .= "\n<table border='0' width='100%' cellspacing='0' cellpadding='0'>";

    foreach ($clients as $client) {
      $name = $client->getName();
      $description = $client->getDescription();
      $image = $client->getImage();
      $url = $client->getUrl();

      if ($image && @file_exists($this->imagePath . $image)) {
        // A gif image cannot be resized
        // No support for the gif format due to copyrights issues
        if (!$this->fileUploadUtils->isGifImage($this->imagePath . $image)) {
          // The image is created on the fly
          $filename = $this->imagePath . $image;
          $filename = urlencode($filename);
          $imageUrl = $gUtilsUrl . "/printImage.php?filename="
            . $filename . "&amp;width=" . $width . "&amp;height=";
        } else {
          $imageUrl = $this->imageUrl . '/' . $image;
        }
        $strImg = "<a onclick=\"window.open(this.href, '_blank'); return(false);\" href='$url'>"
          . "<img class='client_list_image_file' src='$imageUrl' title='' alt='' />"
          . "</a>";
      } else {
        $strImg = '';
      }

      $strName = "<a onclick=\"window.open(this.href, '_blank'); return(false);\" href='$url'>$name</a>";

      $str .= "\n<tr>";
      $str .= "\n<td><div class='client_list_image'>$strImg</div>";
      $str .= "</td><td>";
      $str .= "\n<div class='client_list_name'>$strName</div>";
      $str .= "</td><td>";
      $str .= "\n<div class='client_list_description'>$description</div></td>";
      $str .= "\n</tr>";
    }

    $str .= "\n</table>";

    $str .= "\n</div>";

    return($str);
  }

  // Render an image cycle of the client images
  function renderImageCycleInTemplateElement() {
    $width = $this->preferenceUtils->getValue("CLIENT_CYCLE_WIDTH_TEMPLATE");

    $str = $this->renderImageCycle($width);

    return($str);
  }

  // Render an image cycle of the client images
  function renderImageCycleInPage() {
    $width = $this->preferenceUtils->getValue("CLIENT_CYCLE_WIDTH_PAGE");

    $str = $this->renderImageCycle($width);

    return($str);
  }

  // Render an image cycle of the client images
  function renderImageCycle($width) {
    global $gUtilsUrl;

    $str = '';

    $items = array();

    $clients = $this->selectAll();

    foreach ($clients as $client) {
      $image = $client->getImage();
      $url = $client->getUrl();

      if ($image && @file_exists($this->imagePath . $image)) {
        if (!$this->fileUploadUtils->isGifImage($this->imagePath . $image)) {
          $filename = $this->imagePath . $image;
          $filename = urlencode($filename);
          $imageSrc = $gUtilsUrl . "/printImage.php?filename="
            . $filename . "&amp;width=" . $width . "&amp;height=";
        } else {
          $imageSrc = "$this->imageUrl/$image";
        }

        $height = LibImage::getHeightFromWidth($this->imagePath . $image, $width);

        $item = "<div class='client_cycle_image'>"
          . "<a onclick=\"window.open(this.href, '_blank'); return(false);\" href='$url'>"
          . "<img src='$imageSrc' border='0' title='' width='$width' height='$height' />"
          . "</a>"
          . "</div>";

        array_push($items, $item);
      }
    }

    if (count($items) > 0) {
      $randomNumber = LibUtils::generateUniqueId();

      $timeout = $this->preferenceUtils->getValue("CLIENT_CYCLE_TIMEOUT");

      $str = "<div class='client_cycle'>"
        . $this->commonUtils->renderImageCycle('client_cycle_' . $randomNumber, $items, true, $timeout)
        . "</div>";
    }

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElements() {
    global $gStylingImage;

    $str = "<div class='client_list'>The client references"
      . "<div class='client_list_image'>The image of the client"
      . "<img class='client_list_image_file' src='$gStylingImage' title='The border of the image of the client' />"
      . "</div>"
      . "<div class='client_list_name'>The name</div>"
      . "<div class='client_list_description'>The description</div>"
      . "</div>"
      . "<div class='client_cycle'>The images cycle"
      . "<div class='client_cycle_image'>An image of the cycle"
      . "<img src='$gStylingImage' title='The border of the image' alt='' />"
      . "</div>"
      . "</div>";

    return($str);
  }

}

?>
