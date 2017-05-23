<?

class NewsStoryUtils extends NewsStoryDB {

  var $mlText;
  var $websiteText;

  var $currentStatus;

  var $maxExcerptWords;

  var $audioFileSize;
  var $audioFilePath;
  var $audioFileUrl;

  var $newsStoryId;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $commonUtils;
  var $popupUtils;
  var $clockUtils;
  var $colorboxUtils;
  var $playerUtils;
  var $templateModelUtils;
  var $newsStoryImageUtils;
  var $newsStoryParagraphUtils;
  var $newsPaperUtils;
  var $newsPublicationUtils;
  var $newsEditorUtils;

  function NewsStoryUtils() {
    $this->NewsStoryDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->audioFileSize = 4096000;
    $this->audioFilePath = $gDataPath . 'news/newsStory/audio/';
    $this->audioFileUrl = $gDataUrl . '/news/newsStory/audio';

    $this->currentStatus = "newsCurrentNewsStoryStatus";
    $this->maxExcerptWords = 50;
    $this->newsStoryId = "newsStoryId";
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->audioFilePath)) {
      if (!is_dir($gDataPath . 'news')) {
        mkdir($gDataPath . 'news');
      }
      if (!is_dir($gDataPath . 'news/newsStory')) {
        mkdir($gDataPath . 'news/newsStory');
      }
      mkdir($this->audioFilePath);
      chmod($this->audioFilePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $templateModels = $this->templateModelUtils->getAllModels();

    $this->preferences = array(
      "NEWS_MERGE_PARAGRAPHS" =>
      array($this->mlText[45], $this->mlText[46], PREFERENCE_TYPE_BOOLEAN, ''),
        "NEWS_HIDE_HEADER" =>
        array($this->mlText[68], $this->mlText[69], PREFERENCE_TYPE_BOOLEAN, ''),
          "NEWS_SHARE_HEADINGS" =>
          array($this->mlText[70], $this->mlText[71], PREFERENCE_TYPE_BOOLEAN, ''),
            "NEWS_HIDE_RELEASE" =>
            array($this->mlText[18], $this->mlText[22], PREFERENCE_TYPE_BOOLEAN, ''),
              "NEWS_PAPER_HIDE_EDITOR" =>
              array($this->mlText[49], $this->mlText[50], PREFERENCE_TYPE_BOOLEAN, ''),
                "NEWS_STORY_HIDE_EDITOR" =>
                array($this->mlText[51], $this->mlText[52], PREFERENCE_TYPE_BOOLEAN, ''),
                  "NEWS_HIDE_EDITOR_EMAIL" =>
                  array($this->mlText[12], $this->mlText[25], PREFERENCE_TYPE_BOOLEAN, ''),
                    "NEWS_HIDE_EDITOR_PROFILE" =>
                    array($this->mlText[13], $this->mlText[24], PREFERENCE_TYPE_BOOLEAN, ''),
                      "NEWS_PLAYER_AUTOSTART" =>
                      array($this->mlText[17], $this->mlText[20], PREFERENCE_TYPE_BOOLEAN, ''),
                        "NEWS_PAPER_HIDE_PLAYER" =>
                        array($this->mlText[58], $this->mlText[59], PREFERENCE_TYPE_BOOLEAN, ''),
                          "NEWS_STORY_HIDE_PLAYER" =>
                          array($this->mlText[60], $this->mlText[61], PREFERENCE_TYPE_BOOLEAN, ''),
                            "NEWS_HIDE_SOCIAL_BUTTONS" =>
                            array($this->mlText[53], $this->mlText[54], PREFERENCE_TYPE_BOOLEAN, ''),
                              "NEWS_HIDE_RSS" =>
                              array($this->mlText[11], $this->mlText[27], PREFERENCE_TYPE_BOOLEAN, ''),
                                "NEWS_HIDE_FEED_RELEASE" =>
                                array($this->mlText[14], $this->mlText[26], PREFERENCE_TYPE_BOOLEAN, ''),
                                  "NEWS_FEED_MAX_DISPLAY_NUMBER" =>
                                  array($this->mlText[16], $this->mlText[28], PREFERENCE_TYPE_RANGE, array(0, 30, 10)),
                                    "NEWS_FEED_CYCLE_TIMEOUT" =>
                                    array($this->mlText[40], $this->mlText[41], PREFERENCE_TYPE_RANGE, array(1, 60, 10)),
                                      "NEWS_TEMPLATE_MODEL" =>
                                      array($this->mlText[19], $this->mlText[21], PREFERENCE_TYPE_SELECT, $templateModels),
                                        "NEWS_TEMPLATE_MODEL_ON_PHONE" =>
                                        array($this->mlText[42], $this->mlText[43], PREFERENCE_TYPE_SELECT, $templateModels),
                                              "NEWS_LIST_STEP" =>
                                              array($this->mlText[80], $this->mlText[81], PREFERENCE_TYPE_SELECT, array(10 => "10", 20 => "20", 50 => "50", 100 => "100")),
                                                "NEWS_HTML_EDITOR" =>
                                                array($this->mlText[76], $this->mlText[77], PREFERENCE_TYPE_SELECT,
                                                  array(
                                                    'HTML_EDITOR_CKEDITOR' => $this->mlText[79],
                                                  )),
                                                "NEWS_IMAGE_LENGTH_AXIS" =>
                                                array($this->mlText[64], $this->mlText[65], PREFERENCE_TYPE_SELECT, array('IMAGE_LENGTH_IS_HEIGHT' => $this->mlText[66], 'IMAGE_LENGTH_IS_WIDTH' => $this->mlText[67])),
                                                    "NEWS_STORY_IMAGE_WIDTH" =>
                                                    array($this->mlText[32], $this->mlText[33], PREFERENCE_TYPE_TEXT, 140),
                                                      "NEWS_STORY_PHONE_IMAGE_WIDTH" =>
                                                      array($this->mlText[9], $this->mlText[10], PREFERENCE_TYPE_TEXT, 100),
                                                  "NEWS_STORY_IMAGE_SMALL_WIDTH" =>
                                                  array($this->mlText[30], $this->mlText[31], PREFERENCE_TYPE_TEXT, 100),
                                                          "NEWS_PAPER_IMAGE_WIDTH" =>
                                                          array($this->mlText[29], $this->mlText[36], PREFERENCE_TYPE_TEXT, 140),
                                                            "NEWS_PAPER_PHONE_IMAGE_WIDTH" =>
                                                            array($this->mlText[37], $this->mlText[38], PREFERENCE_TYPE_TEXT, 100),
                                                              "NEWS_HEADING_IMAGE_WIDTH" =>
                                                              array($this->mlText[72], $this->mlText[74], PREFERENCE_TYPE_TEXT, 100),
                                                                "NEWS_HEADING_PHONE_IMAGE_WIDTH" =>
                                                                array($this->mlText[73], $this->mlText[75], PREFERENCE_TYPE_TEXT, 60),
                                                                );

    $this->preferenceUtils->init($this->preferences);
  }

  // Check if an html editor is being used
  function useHtmlEditor() {
    if ($this->useHtmlEditorCKEditor()) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the selected html editor is the CKEditor
  function useHtmlEditorCKEditor() {
    $htmlEditor = $this->preferenceUtils->getValue("NEWS_HTML_EDITOR");

    if ($htmlEditor == 'HTML_EDITOR_CKEDITOR') {
      return(true);
    } else {
      return(false);
    }
  }

  // Delete a news story
  function deleteNewsStory($newsStoryId) {
    global $gNewsUrl;

    // Delete the images of the news story
    $newsStoryImages = $this->newsStoryImageUtils->selectByNewsStoryId($newsStoryId);
    foreach ($newsStoryImages as $newsStoryImage) {
      $newsStoryImageId = $newsStoryImage->getId();
      $this->newsStoryImageUtils->delete($newsStoryImageId);
    }

    // Delete the paragraphs of the news story
    $newsStoryParagraphs = $this->newsStoryParagraphUtils->selectByNewsStoryId($newsStoryId);
    foreach ($newsStoryParagraphs as $newsStoryParagraph) {
      $newsStoryParagraphId = $newsStoryParagraph->getId();
      $this->newsStoryParagraphUtils->deleteParagraph($newsStoryParagraphId);
    }

    $this->delete($newsStoryId);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedAudioFiles() {
    $handle = opendir($this->audioFilePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->audioIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (file_exists($this->audioFilePath . $oneFile)) {
            unlink($this->audioFilePath . $oneFile);
          }
        }
      }
    }
    closedir($handle);
  }

  // Check if an audio file is being used
  function audioIsUsed($audio) {
    $isUsed = true;

    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByAudio($audio)) {
      if ($result->getRowCount() < 1) {
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Check if a news story is secured
  function isSecured($newsStoryId) {
    $secured = false;

    if ($newsStory = $this->selectById($newsStoryId)) {
      $newsPaperId = $newsStory->getNewsPaper();
      if ($newsPaper = $this->newsPaperUtils->selectById($newsPaperId)) {
        $newsPublicationId = $newsPaper->getNewsPublicationId();
        if ($newsPublication = $this->newsPublicationUtils->selectById($newsPublicationId)) {
          $secured = $newsPublication->getSecured();
        }
      }
    }

    return($secured);
  }

  // Duplicate a news story
  function duplicate($newsStoryId, $newsPaperId, $headline) {
    if ($newsStory = $this->selectById($newsStoryId)) {
      if (!$newsPaper = $this->newsPaperUtils->selectById($newsPaperId)) {
        $newsPaperId = $newsStory->getNewsPaper();
      }
      if (!$headline) {
        $headline = $newsStory->getHeadline();
      }
      $releaseDate = $this->clockUtils->getSystemDate();
      $newsStory->setReleaseDate($releaseDate);
      $newsStory->setNewsPaper($newsPaperId);
      $newsStory->setHeadline($headline);
      $this->insert($newsStory);
      $lastNewsStoryId = $this->getLastInsertId();

      // Duplicate the images
      $newsStoryImages = $this->newsStoryImageUtils->selectByNewsStoryId($newsStoryId);
      foreach ($newsStoryImages as $newsStoryImage) {
        $newsStoryImageId = $newsStoryImage->getId();
        $newsStoryImage->setImage($newsStoryImage->getImage());
        $newsStoryImage->setDescription($newsStoryImage->getDescription());
        $newsStoryImage->setListOrder($newsStoryImage->getListOrder());
        $newsStoryImage->setNewsStoryId($lastNewsStoryId);
        $this->newsStoryImageUtils->insert($newsStoryImage);
      }

      // Duplicate the paragraphs
      $newsStoryParagraphs = $this->newsStoryParagraphUtils->selectByNewsStoryId($newsStoryId);
      foreach ($newsStoryParagraphs as $newsStoryParagraph) {
        $newsStoryParagraphId = $newsStoryParagraph->getId();
        $newsStoryParagraph->setHeader($newsStoryParagraph->getHeader());
        $newsStoryParagraph->setBody($newsStoryParagraph->getBody());
        $newsStoryParagraph->setFooter($newsStoryParagraph->getFooter());
        $newsStoryParagraph->setNewsStoryId($lastNewsStoryId);
        $this->newsStoryParagraphUtils->insert($newsStoryParagraph);
      }

      return($lastNewsStoryId);
    }

    return(false);
  }

  // Get the next available list order
  function getNextListOrder($newsPaperId, $newsHeadingId) {
    $listOrder = 1;
    if ($objects = $this->selectByNewsPaperAndNewsHeading($newsPaperId, $newsHeadingId)) {
      $total = count($objects);
      if ($total > 0) {
        $object = $objects[$total - 1];
        $listOrder = $object->getListOrder() + 1;
      }
    }

    return($listOrder);
  }

  // Place the current object before another target one
  function placeFirst($currentObjectId) {
    $currentObject = $this->selectById($currentObjectId);
    $newsPaperId = $currentObject->getNewsPaper();
    $newsHeadingId = $currentObject->getNewsHeading();

    if ($objects = $this->selectByNewsPaperAndNewsHeading($newsPaperId, $newsHeadingId)) {
      if (count($objects) > 0) {
        $targetObject = $objects[0];
        $targetObjectId = $targetObject->getId();
        $this->placeBefore($currentObjectId, $targetObjectId);
      }
    } else {
      $listOrder = $this->getNextListOrder($newsPaperId, $newsHeadingId);
      $currentObject->setListOrder($listOrder);
      $this->update($currentObject);
    }

  }

  // Place the current object before another target one
  function placeBefore($currentObjectId, $targetObjectId) {
    if ($currentObjectId == $targetObjectId) {
      return;
    }

    if ($nextObject = $this->selectNext($currentObjectId)) {
      if ($nextObject->getId() == $targetObjectId) {
        return;
      }
    }

    $currentObject = $this->selectById($currentObjectId);

    if ($targetObject = $this->selectById($targetObjectId)) {
      $targetObjectListOrder = $targetObject->getListOrder();
    } else {
      $targetObjectListOrder = '';
    }

    // Reset the list order of the target object and all its followers
    $newsPaperId = $currentObject->getNewsPaper();
    $newsHeadingId = $currentObject->getNewsHeading();
    $currentListOrder = $currentObject->getListOrder();

    if ($objects = $this->selectByNewsPaperAndNewsHeading($newsPaperId, $newsHeadingId)) {
      // Start the next list order after the target object
      $nextListOrder = $targetObjectListOrder + 1;
      foreach($objects as $object) {
        $listOrder = $object->getListOrder();
        // Do not reset the list order of the objects preceding the target object
        if ($listOrder < $targetObjectListOrder) {
          continue;
        }
        $object->setListOrder($nextListOrder);
        $this->update($object);
        $nextListOrder++;
      }
    }

    // Update the list order of the current object
    // and set it with the list order of the specified target
    $currentObject->setListOrder($targetObjectListOrder);
    $this->update($currentObject);
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
    if ($newsStory = $this->selectById($id)) {
      $listOrder = $newsStory->getListOrder();
      $newsPaperId = $newsStory->getNewsPaper();
      $newsHeadingId = $newsStory->getNewsHeading();
      if ($newsStories = $this->selectByListOrder($newsPaperId, $newsHeadingId, $listOrder)) {
        if (($listOrder == 0) || (count($newsStories)) > 1) {
          $this->resetListOrder($newsPaperId, $newsHeadingId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($newsStory = $this->selectById($id)) {
      $listOrder = $newsStory->getListOrder();
      $newsPaperId = $newsStory->getNewsPaper();
      $newsHeadingId = $newsStory->getNewsHeading();
      if ($newsStory = $this->selectByNextListOrder($newsPaperId, $newsHeadingId, $listOrder)) {
        return($newsStory);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($newsStory = $this->selectById($id)) {
      $listOrder = $newsStory->getListOrder();
      $newsPaperId = $newsStory->getNewsPaper();
      $newsHeadingId = $newsStory->getNewsHeading();
      if ($newsStory = $this->selectByPreviousListOrder($newsPaperId, $newsHeadingId, $listOrder)) {
        return($newsStory);
      }
    }
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
    $imageLengthAxis = $this->preferenceUtils->getValue("NEWS_IMAGE_LENGTH_AXIS");

    if ($imageLengthAxis == 'IMAGE_LENGTH_IS_WIDTH') {
      return(true);
    }

    return(false);
  }

  // Attach the news story to a newsPaper
  function attachToNewsPaper($newsStoryId, $newsPaperId, $newsHeadingId) {
    if ($newsStory = $this->selectById($newsStoryId)) {
      $newsStory->setNewsPaper($newsPaperId);
      $newsStory->setNewsHeading($newsHeadingId);
      $listOrder = $this->getNextListOrder($newsPaperId, $newsHeadingId);
      $newsStory->setListOrder($listOrder);
      $this->update($newsStory);
    }

    return(true);
  }

  function hasLink($newsStory) {
    if (!$newsStory) {
      return;
    }

    if ($newsStory->getLink()) {
      return(true);
    } else {
      return(false);
    }
  }

  function hasParagraph($newsStory) {
    $str = '';

    if ($newsStory) {
      $newsStoryId = $newsStory->getId();
      if ($newsStoryParagraphs = $this->newsStoryParagraphUtils->selectByNewsStoryId($newsStoryId)) {
        foreach ($newsStoryParagraphs as $newsStoryParagraph) {
          $str .= trim(LibString::stripBR($newsStoryParagraph->getHeader()));
          $str .= trim(LibString::stripBR($newsStoryParagraph->getBody()));
          $str .= trim(LibString::stripBR($newsStoryParagraph->getFooter()));
        }
      }
    }

    if ($str) {
      return(true);
    } else {
      return(false);
    }
  }

  // Get the width of an image
  function getImageWidth() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("NEWS_STORY_PHONE_IMAGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("NEWS_STORY_IMAGE_WIDTH");
    }

    return($width);
  }

  // Render the image
  function renderImage($newsStory, $newsStoryParagraphId = '') {
    global $gNewsUrl;
    global $gUtilsUrl;
    global $gJSNoStatus;
    global $gIsPhoneClient;

    if (!$newsStory) {
      return;
    }

    // Get one image based on the paragraph index
    if ($newsStoryParagraphId) {
      $paragraphIndex = $this->getParagraphIndex($newsStoryParagraphId);
    } else {
      $paragraphIndex = 0;
    }

    $str = '';

    $image = '';
    $description = '';
    $newsStoryImageId = '';
    $newsStoryId = $newsStory->getId();
    if ($newsStoryImages = $this->newsStoryImageUtils->selectByNewsStoryId($newsStoryId)) {
      if (count($newsStoryImages) > 0) {
        if (isset($newsStoryImages[$paragraphIndex])) {
          $newsStoryImage	= $newsStoryImages[$paragraphIndex];
          $newsStoryImageId = $newsStoryImage->getId();
          $image = $newsStoryImage->getImage();
          $description = $newsStoryImage->getDescription();
        }
      }
    }

    $imageFilePath = $this->newsStoryImageUtils->imageFilePath;
    $imageFileUrl = $this->newsStoryImageUtils->imageFileUrl;

    if ($image && file_exists($imageFilePath . $image)) {
      if (LibImage::isImage($image)) {
        $width = $this->getImageWidth();

        if ($gIsPhoneClient && !LibImage::isGif($image)) {
          // Resize the image
          $filename = $imageFilePath . $image;

          $imageLengthIsHeight = $this->imageLengthIsHeight();
          if ($imageLengthIsHeight) {
            $width = LibImage::getWidthFromHeight($filename, $width);
          }

          $filename = urlencode($filename);

          $strUrl = $gUtilsUrl . "/printImage.php?filename=" . $filename . "&amp;width=$width&amp;height=";
        } else {
          $strUrl = "$imageFileUrl/$image";
        }

        $str .= "<div class='newsstory_image'>"
          . "<a href='$imageFileUrl/$image' rel='no_style_colorbox' $gJSNoStatus>"
          . "<img class='newsstory_image_file' src='$strUrl' title='"
          . $this->websiteText[8] . "' alt='' /></a>"
          . "</div>";

        // Render empty links to load up the images for the colobox display
        for ($i = 0; $i < count($newsStoryImages); $i++) {
          if ($i != $paragraphIndex) {
            $newsStoryImage = $newsStoryImages[$i];
            $image = $newsStoryImage->getImage();
            $str .= "<a href='$imageFileUrl/$image' rel='no_style_colorbox' $gJSNoStatus></a>";
          }
        }
      } else {
        $libFlash = new LibFlash();
        if ($libFlash->isFlashFile($this->newsStoryImageUtils->imageFileUrl . "/" . $image)) {
          $str = $libFlash->renderObject($this->newsStoryImageUtils->imageFileUrl. "/" . $image);
        }
      }

      $str .= "<div class='newsstory_image_description'>$description</div>";
    } else {
      $str = '';
    }

    return($str);
  }

  // Get the index of a paragraph
  function getParagraphIndex($newsStoryParagraphId) {
    $paragraphIndex = 0;

    if ($newsStoryParagraph = $this->newsStoryParagraphUtils->selectById($newsStoryParagraphId)) {
      $newsStoryId = $newsStoryParagraph->getNewsStoryId();

      if ($newsStoryParagraphs = $this->newsStoryParagraphUtils->selectByNewsStoryId($newsStoryId)) {
        foreach ($newsStoryParagraphs as $newsStoryParagraph) {
          $wNewsStoryParagraphId = $newsStoryParagraph->getId();
          if ($newsStoryParagraphId == $wNewsStoryParagraphId) {
            return($paragraphIndex);
          }
          $paragraphIndex++;
        }
      }
    }
  }

  // Render the editor name
  function renderEditorName($newsStory) {
    $this->loadLanguageTexts();

    $str = '';

    $hideEditor = $this->preferenceUtils->getValue("NEWS_STORY_HIDE_EDITOR");

    if ($hideEditor) {
      return;
    }

    if (!$newsStory) {
      return;
    }

    $newsEditorId = $newsStory->getNewsEditor();

    if ($newsEditorId) {
      if ($newsEditor = $this->newsEditorUtils->selectById($newsEditorId)) {
        $firstname = $this->newsEditorUtils->getFirstname($newsEditorId);
        $lastname = $this->newsEditorUtils->getLastname($newsEditorId);
        $email = $this->newsEditorUtils->getEmail($newsEditorId);

        $hideEmail = $this->preferenceUtils->getValue("NEWS_HIDE_EDITOR_EMAIL");

        if (!$hideEmail && $email) {
          $str .= $this->websiteText[39] . ' ' .  "<a href='mailto:$email'>"
            . $firstname . ' ' . $lastname . "</a>";
        } else {
          $str .=  $this->websiteText[39] . ' ' .  $firstname . ' ' . $lastname;
        }
      }
    }

    return($str);
  }

  // Render the editor profile
  function renderEditorProfile($newsStory) {
    if (!$newsStory) {
      return;
    }

    $newsEditorId = $newsStory->getNewsEditor();

    $profile = '';

    if ($newsEditorId) {
      if ($newsEditor = $this->newsEditorUtils->selectById($newsEditorId)) {
        $profile = $this->newsEditorUtils->getProfile($newsEditorId);
      }
    }

    return($profile);
  }

  // Render the body
  function renderParagraphs($newsStoryId, $newsStoryParagraphId = '') {
    $str = '';

    $str .= "<div class='newsstory_paragraphs'>";

    if ($newsStoryId) {
      if ($newsStoryParagraphId) {
        if ($newsStoryParagraph = $this->newsStoryParagraphUtils->selectById($newsStoryParagraphId)) {
          $header = $newsStoryParagraph->getHeader();
          $body = $newsStoryParagraph->getBody();
          $footer = $newsStoryParagraph->getFooter();
          $str = "<div class='newsstory_paragraph_header'>$header</div>"
            . "<div class='newsstory_paragraph_body'>$body</div>"
            . "<div class='newsstory_paragraph_footer'>$footer</div>";
        }
      } else {
        if ($newsStoryParagraphs = $this->newsStoryParagraphUtils->selectByNewsStoryId($newsStoryId)) {
          foreach ($newsStoryParagraphs as $newsStoryParagraph) {
            $header = $newsStoryParagraph->getHeader();
            $body = $newsStoryParagraph->getBody();
            $footer = $newsStoryParagraph->getFooter();
            $str .= "<div class='newsstory_paragraph_header'>$header</div>"
              . "<div class='newsstory_paragraph_body'>$body</div>"
              . "<div class='newsstory_paragraph_footer'>$footer</div>";
          }
        }
      }
    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the release date
  function renderRelease($newsStory) {
    $str = '';

    if (!$newsStory) {
      return;
    }

    $releaseDate = $newsStory->getReleaseDate();

    $releaseDate = $this->clockUtils->systemToLocalNumericDate($releaseDate);

    $str = $releaseDate;

    return($str);
  }

  // Print the news story
  function printNewsStory($newsStoryId) {
    global $gJSNoStatus;
    global $gNewsUrl;
    global $gTemplateUrl;

    if (!$newsStory = $this->selectById($newsStoryId)) {
      return;
    }

    $str = LibJavaScript::getJSLib();
    $str .= "\n<script type='text/javascript'>printPage();</script>";

    $str .= "\n<div class='newsstory'>";

    $releaseDate = $newsStory->getReleaseDate();
    $headline = $newsStory->getHeadline();
    $editorName = $this->renderEditorName($newsStory);

    $releaseDate = $this->clockUtils->systemToLocalNumericDate($releaseDate);

    $str .= "<div class='newsstory_header'>"
      . "<div class='newsstory_headline'>$headline</div>"
      . "<div class='newsstory_release'>$releaseDate</div>"
      . "<div class='newsstory_header_editor'>$editorName</div>"
      . "</div>";

    $str .= $this->renderParagraphs($newsStoryId);

    $str .= "\n</div>";

    return($str);
  }

  // Get the template model, if any, in which to render the newspapers and news stories
  function getTemplateModel() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $templateModelId = $this->preferenceUtils->getValue("NEWS_TEMPLATE_MODEL_ON_PHONE");
    } else {
      $templateModelId = $this->preferenceUtils->getValue("NEWS_TEMPLATE_MODEL");
    }

    return($templateModelId);
  }

  // Render the player
  function renderPlayer($newsStory) {
    global $gImagesUserUrl;
    global $gDataUrl;

    $str = '';

    $audio = $newsStory->getAudio();
    $audioUrl = $newsStory->getAudioUrl();

    if ($audio || $audioUrl) {
      $autostart = $this->preferenceUtils->getValue("NEWS_PLAYER_AUTOSTART");

      $this->playerUtils->setAutostart($autostart);

      if ($audio) {
        $str .= $this->playerUtils->renderPlayer("$gDataUrl/news/newsStory/audio/$audio");
      }

      if ($audioUrl) {
        $str .= $this->playerUtils->renderPlayer($audioUrl);
      }
    }

    return($str);
  }

  // Render the news story
  function renderNewsStory($newsStoryId, $newsStoryImageId = '') {
    $str = '';

    if ($newsStory = $this->selectById($newsStoryId)) {
      $str = $this->render($newsStory, $newsStoryImageId);
    }

    return($str);
  }

  // Render the download link
  function renderDownload($newsStory) {
    global $gDataUrl;
    global $gDataPath;

    $str = '';

    $audio = $newsStory->getAudio();

    if ($audio) {
      if (is_file($gDataPath . "news/newsStory/audio/$audio")) {
        $str = $this->playerUtils->renderDownload($gDataPath . "news/newsStory/audio/$audio");
      }
    }

    return($str);
  }

  // Render the news story
  function render($newsStory, $newsStoryImageId = '') {
    global $gImagesUserUrl;
    global $gJSNoStatus;
    global $gTemplateUrl;
    global $gNewsUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    if (!$newsStory) {
      return;
    }

    $newsStoryId = $newsStory->getId();

    // Check if the paragraphs are merged
    $newsStoryParagraphId = '';

    $mergeParagraphs = $this->preferenceUtils->getValue("NEWS_MERGE_PARAGRAPHS");

    if (!$mergeParagraphs) {
      $newsStoryParagraphId = LibEnv::getEnvHttpGET("newsStoryParagraphId");

      // Get the first paragraph if none
      if (!$newsStoryParagraphId) {
        if ($newsStoryParagraphs = $this->newsStoryParagraphUtils->selectByNewsStoryId($newsStoryId)) {
          if (count($newsStoryParagraphs) > 0) {
            $newsStoryParagraph = $newsStoryParagraphs[0];
            $newsStoryParagraphId = $newsStoryParagraph->getId();
          }
        }
      }
    }

    // Get the previous and next paragraph ids
    $previousParagraphId = $this->newsStoryParagraphUtils->getPreviousParagraphId($newsStoryParagraphId);
    $nextParagraphId = $this->newsStoryParagraphUtils->getNextParagraphId($newsStoryParagraphId);

    $str = '';

    $str .= $this->colorboxUtils->renderJsColorbox() . $this->colorboxUtils->renderWebsiteColorbox();

    $str .= "\n<div class='newsstory'>";

    $headline = $newsStory->getHeadline();
    $editorName = $this->renderEditorName($newsStory);
    $newsPaperId = $newsStory->getNewsPaper();

    $hideRelease = $this->preferenceUtils->getValue("NEWS_HIDE_RELEASE");

    $str .= "<div class='newsstory_header'><div class='newsstory_headline'>$headline</div>";

    if (!$hideRelease) {
      $releaseDate = $newsStory->getReleaseDate();

      $releaseDate = $this->clockUtils->systemToLocalNumericDate($releaseDate);

      $str .= "<div class='newsstory_release'>$releaseDate</div>";
    }

    // Render the editor on the first paragraph only
    if (!$previousParagraphId && $nextParagraphId) {
      $str .= "<div class='newsstory_header_editor'>$editorName</div>";
    }

    $str .= "</div>";

    $hidePlayer = $this->preferenceUtils->getValue("NEWS_STORY_HIDE_PLAYER");
    if (!$hidePlayer) {
      $strPlayer = $this->renderPlayer($newsStory);
      $audioDownload = $this->preferenceUtils->getValue("ELEARNING_DISPLAY_AUDIO_DOWNLOAD");
      if ($audioDownload) {
        $strPlayer .= ' ' . $this->renderDownload($newsStory);
      }

      if ($strPlayer) {
        $str .= "<div class='newsstory_player'>" . $strPlayer . "</div>";
      }
    }

    $str .= $this->renderImage($newsStory, $newsStoryParagraphId);

    $str .= $this->renderParagraphs($newsStoryId, $newsStoryParagraphId);

    $str .= "<div class='newsstory_footer'>";

    $str .= "\n<div class='newsstory_button_paragraph'>";
    if ($previousParagraphId) {
      $str .= "\n <a href='$gNewsUrl/newsStory/display.php?newsStoryId=$newsStoryId&newsStoryParagraphId=$previousParagraphId' $gJSNoStatus title='" . $this->websiteText[47] . "'>" . "<img src='$gImagesUserUrl/" . IMAGE_COMMON_LEFT . "' class='no_style_image_icon' title='' alt='' /><br />" . $this->websiteText[47] . "</a>";
    }
    if ($nextParagraphId) {
      $str .= "\n <a href='$gNewsUrl/newsStory/display.php?newsStoryId=$newsStoryId&newsStoryParagraphId=$nextParagraphId' $gJSNoStatus title='" . $this->websiteText[48] . "'>" . "<img src='$gImagesUserUrl/" . IMAGE_COMMON_RIGHT . "' class='no_style_image_icon' title='' alt='' /><br />" . $this->websiteText[48] . "</a>";
    }
    $str .= "\n</div>";

    // Render the editor on the last paragraph
    if (!$nextParagraphId) {
      $hideEditor = $this->preferenceUtils->getValue("NEWS_STORY_HIDE_EDITOR");
      $hideEditorProfile = $this->preferenceUtils->getValue("NEWS_HIDE_EDITOR_PROFILE");
      if (!$hideEditorProfile && !$hideEditor) {
        $editorProfile = $this->renderEditorProfile($newsStory);
      } else {
        $editorProfile = '';
      }
      $str .= "<div class='newsstory_footer_editor'>$editorName</div>"
        . "<div class='newsstory_footer_editor_profile'>$editorProfile</div>";
    }

    $str .= "\n<div class='newsstory_icons'>";

    if (!$gIsPhoneClient) {
      $str .= $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_PRINTER . "' class='no_style_image_icon' title='" .  $this->websiteText[3] . " 'alt='' />", "$gNewsUrl/newsStory/print.php?newsStoryId=$newsStoryId", 600, 600);

      $str .= ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_EMAIL_FRIEND . "' class='no_style_image_icon' title='" . $this->websiteText[4] . "' alt='' />", "$gNewsUrl/newsStory/send.php?newsStoryId=$newsStoryId", 600, 600);
    }

    $str .= "\n</div>";

    if (!$this->preferenceUtils->getValue("NEWS_HIDE_SOCIAL_BUTTONS")) {
      $strLink = "$gNewsUrl/newsStory/display.php?newsStoryId=$newsStoryId";
      $str .= "<div class='newsstory_social_buttons'>";
      $str .= $this->commonUtils->renderSocialNetworksButtons($headline, $strLink);
      $str .= " </div>";
    }

    $str .= "\n<div class='newsstory_button_back'>"
      . "\n<a href='$gNewsUrl/newsPaper/display.php?newsPaperId=$newsPaperId' $gJSNoStatus>"
      . "<img src='$gImagesUserUrl/" . IMAGE_NEWS_STORY_BACK . "' class='no_style_image_icon' title='' alt='' /><br />"
      . $this->websiteText[2]
      . "</a>"
      . "\n</div>";

    $str .= "\n</div>";

    $str .= "\n</div>";

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElements() {
    global $gStylingImage;
    global $gImagesUserUrl;

    $str = "<div class='newsstory'>A news story"
      . "<div class='newsstory_header'>The header"
      . "<div class='newsstory_headline'>The headline of the news story</div>"
      . "<div class='newsstory_release'>The release date of the news story</div>"
      . "<div class='newsstory_header_editor'>The name of the editor</div>"
      . "</div>"
      . "<div class='newsstory_player'>The audio player of the news story</div>"
      . "<div class='newsstory_image'>The image of the news story"
      . "<img class='newsstory_image_file' src='$gStylingImage' title='The border of the image of the news story' alt='' />"
      . "</div>"
      . "<div class='newsstory_image_description'>The description of the image</div>"
      . "<div class='newsstory_paragraphs'>The paragraphs"
      . "<div class='newsstory_paragraph_header'>The header of the paragraph</div>"
      . "<div class='newsstory_paragraph_body'>The body of the paragraph</div>"
      . "<div class='newsstory_paragraph_footer'>The footer of the paragraph</div>"
      . "</div>"
      . "<div class='newsstory_footer'>"
      . "<div class='newsstory_button_paragraph'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_COMMON_LEFT . "' class='no_style_image_icon' title='The previous paragraph' />"
      . " <img src='$gImagesUserUrl/" . IMAGE_COMMON_RIGHT . "' class='no_style_image_icon' title='The next paragraph' />"
      . "</div>"
      . "<div class='newsstory_footer_editor'>The name of the editor</div>"
      . "<div class='newsstory_footer_editor_profile'>The profile of the editor</div>"
      . "<div class='newsstory_icons'>"
      . "<input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_PRINTER . "' class='no_style_image_icon' title='An icon' />"
      . "</div>"
      . "<div class='newsstory_social_buttons'>The social networks buttons</div>"
      . "<div class='newsstory_button_back'>"
      . "<img src='$gImagesUserUrl/" . IMAGE_NEWS_STORY_BACK . "' class='no_style_image_icon' title='The back button' />"
      . "</div>"
      . "</div>"
      . "</div>";

    return($str);
  }

}

?>
