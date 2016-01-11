<?

class FlashUtils extends FlashDB {

  var $mlText;

  var $propertyIntroFlashId;

  var $filePath;
  var $fileUrl;
  var $fileSize;

  var $wasDisplayed;

  var $wasDisplayedInPeriod;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $playerUtils;
  var $dynpageUtils;
  var $propertyUtils;
  var $fileUploadUtils;

  function FlashUtils() {
    $this->FlashDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->fileSize = 4096000;
    $this->filePath = $gDataPath . 'flash/file/';
    $this->fileUrl = $gDataUrl . '/flash/file';

    $this->propertyIntroFlashId = "FLASH_INTRO";
    $this->wasDisplayed = 'flashWasDisplayed';
    $this->wasDisplayedInPeriod = 'flashWasDisplayedInPeriod';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->filePath)) {
      if (!is_dir($gDataPath . 'flash')) {
        mkdir($gDataPath . 'flash');
      }
      mkdir($this->filePath);
      chmod($this->filePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "FLASH_INTRO_HIDDEN" =>
      array($this->mlText[1], $this->mlText[31], PREFERENCE_TYPE_BOOLEAN, ''),
        "FLASH_INTRO_POPUP" =>
        array($this->mlText[2], $this->mlText[34], PREFERENCE_TYPE_BOOLEAN, ''),
          "FLASH_INTRO_DISPLAY_ONCE" =>
          array($this->mlText[13], $this->mlText[32], PREFERENCE_TYPE_BOOLEAN, ''),
            "FLASH_INTRO_DISPLAY_PERIOD" =>
            array($this->mlText[19], $this->mlText[33], PREFERENCE_TYPE_RANGE, array(0, 30)),
              "FLASH_INTRO_PAGE_BG_COLOR" =>
              array($this->mlText[18], $this->mlText[35], PREFERENCE_TYPE_COLOR, ''),
                "FLASH_INTRO_SKIP_LINK" =>
                array($this->mlText[5], $this->mlText[36], PREFERENCE_TYPE_TEXT, ''),
                  "FLASH_INTRO_POPUP_WIDTH" =>
                  array($this->mlText[11], $this->mlText[40], PREFERENCE_TYPE_TEXT, ''),
                    "FLASH_INTRO_POPUP_HEIGHT" =>
                    array($this->mlText[12], $this->mlText[40], PREFERENCE_TYPE_TEXT, ''),
                      "FLASH_INTRO_POPUP_TOP" =>
                      array($this->mlText[14], $this->mlText[40], PREFERENCE_TYPE_TEXT, ''),
                        "FLASH_INTRO_POPUP_LEFT" =>
                        array($this->mlText[15], $this->mlText[40], PREFERENCE_TYPE_TEXT, ''),
                          );

    $this->preferenceUtils->init($this->preferences);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedFiles() {
    $handle = opendir($this->filePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*') && !stristr($oneFile, FLASH_WDDX_SUFFIX)) {
        if (!$this->fileIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (file_exists($this->filePath . $oneFile)) {
            unlink($this->filePath . $oneFile);
          }
        }
      }
    }
    closedir($handle);
  }

  // Check if a file is being used
  function fileIsUsed($file) {
    $isUsed = true;

    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByFile($file)) {
      if ($result->getRowCount() < 1) {
        if ($result = $this->dynpageUtils->dao->selectByImage($file)) {
          if ($result->getRowCount() < 1) {
            $isUsed = false;
          }
        }
      }
    }

    return($isUsed);
  }

  function deleteFile($flashId) {
    if ($flash = $this->selectById($flashId)) {
      $flash->setFile('');
      $this->update($flash);
    }
  }

  // Remove the non referenced wddx files from the directory
  function deleteUnusedWddxFiles() {
    $handle = opendir($this->filePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*') && stristr($oneFile, FLASH_WDDX_SUFFIX)) {
        if (!$this->wddxFileIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (file_exists($filePath . $oneFile)) {
            unlink($filePath . $oneFile);
          }
        }
      }
    }
    closedir($handle);
  }

  // Check if a wddx file is being used
  function wddxFileIsUsed($file) {
    $isUsed = true;

    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByWddxFile($file)) {
      if ($result->getRowCount() < 1) {
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Add a Flash file
  function add() {
    $flash = new Flash();
    $this->insert($flash);
    $flashId = $this->getLastInsertId();

    return($flashId);
  }

  // Duplicate a Flash file
  function duplicate($flashId) {
    if ($flash = $this->selectById($flashId)) {
      $this->insert($flash);
      $duplicatedFlashId = $this->getLastInsertId();

      return($duplicatedFlashId);
    }
  }

  // Render a Flash animation
  // The Flash animation may be passed the language code at rendering time
  // so as to offer the Flash developer a mean to select the correct language
  function render($flashId, $languageCode = '') {
    global $gTemplateUrl;
    global $gHomeUrl;

    if (!$flash = $this->selectById($flashId)) {
      return;
    }

    $file = $flash->getFile();
    if (!$file) {
      return;
    }

    $filePath = $this->filePath;
    $fileUrl = $this->fileUrl;

    $width = $flash->getWidth();
    $height = $flash->getHeight();
    $bgcolor = $flash->getBgcolor();
    $wddx = $flash->getWddx();

    $strFile = "$fileUrl/$file";

    if ($languageCode) {
      $strFile .= "?languageCode=$languageCode";

      if ($wddx) {
        $strFile .= "&wddxname=$fileUrl/$wddx";
      }
    } else if ($wddx) {
      $strFile .= "?wddxname=$fileUrl/$wddx";
    }

    $str = '';

    $str .= "\n<div class='flash'>";

    $libFlash = new LibFlash();
    if ($libFlash->isFlashFile($file)) {
      $libFlash->file = $strFile;
      $libFlash->width = $width;
      $libFlash->height = $height;
      $libFlash->bgcolor = $bgcolor;
      $str .= $libFlash->renderObject();
      $str .= <<<HEREDOC
<script type='text/javascript'>
<!--
hideFlashBorders(document);
-->
</script>
HEREDOC;
    } else if ($this->fileUploadUtils->isMP3Type($file)) {
      $this->playerUtils->setAutostart(true);
      $str .= $this->playerUtils->renderPlayer($strFile);
    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the tags
  // When creating an element in the template system, it is necessary to create its tags
  // A dummy rendering is used for this operation
  function renderTags() {
    $str = "\n<div class='flash'>";
    $str .= "\n</div>";

    return($str);
  }

  // Get the file name of the Flash intro
  function getIntroFlashName() {
    $file = '';

    $flashId = $this->getIntroFlashId();

    if ($flash = $this->selectById($flashId)) {
      $file = $flash->getFile();
    }

    return($file);
  }

  // Get the id of the Flash intro
  function getIntroFlashId() {
    $flashId = $this->propertyUtils->retrieve($this->propertyIntroFlashId);

    return($flashId);
  }

  // Set the file name of the Flash intro
  function setIntroFlashId($flashId) {
    $this->propertyUtils->store($this->propertyIntroFlashId, $flashId);
  }

  // Render the Flash intro object
  function renderFlashIntroObject() {

    $introFlashId = $this->getIntroFlashId();

    $str = '';

    if ($flash = $this->selectById($introFlashId)) {
      $str = $this->render($introFlashId);
    }

    return($str);
  }

}

?>
