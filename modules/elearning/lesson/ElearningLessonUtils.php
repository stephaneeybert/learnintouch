<?

class ElearningLessonUtils extends ElearningLessonDB {

  var $mlText;
  var $websiteText;

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $audioFileSize;
  var $audioFilePath;
  var $audioFileUrl;

  var $languageUtils;
  var $preferenceUtils;
  var $commonUtils;
  var $popupUtils;
  var $userUtils;
  var $adminUtils;
  var $playerUtils;
  var $profileUtils;
  var $websiteUtils;
  var $elearningResultUtils;
  var $elearningCourseUtils;
  var $elearningCourseItemUtils;
  var $elearningExerciseUtils;
  var $elearningLessonParagraphUtils;
  var $elearningLessonHeadingUtils;
  var $elearningSubscriptionUtils;
  var $fileUploadUtils;

  function ElearningLessonUtils() {
    $this->ElearningLessonDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'elearning/lesson/image/';
    $this->imageFileUrl = $gDataUrl . '/elearning/lesson/image';

    $this->audioFileSize = 4096000;
    $this->audioFilePath = $gDataPath . 'elearning/lesson/audio/';
    $this->audioFileUrl = $gDataUrl . '/elearning/lesson/audio';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'elearning')) {
        mkdir($gDataPath . 'elearning');
      }
      if (!is_dir($gDataPath . 'elearning/lesson')) {
        mkdir($gDataPath . 'elearning/lesson');
      }
      mkdir($this->imageFilePath);
      chmod($this->imageFilePath, 0755);
    }

    if (!is_dir($this->audioFilePath)) {
      mkdir($this->audioFilePath);
      chmod($this->audioFilePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imageFilePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->imageIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (@file_exists($this->imageFilePath . $oneFile)) {
            @unlink($this->imageFilePath . $oneFile);
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
        if ($result = $this->dao->selectIntroductionLikeImage($image)) {
          if ($result->getRowCount() < 1) {
            $isUsed = false;
          }
        }
      }
    }

    return($isUsed);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedAudioFiles() {
    $handle = opendir($this->audioFilePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->audioIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (@file_exists($this->audioFilePath . $oneFile)) {
            @unlink($this->audioFilePath . $oneFile);
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

  // Check if at least one exercise of a lesson has some results
  function hasResults($elearningLessonId) {
    if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonId($elearningLessonId)) {
      foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
        $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
        if ($elearningResults = $this->elearningResultUtils->selectByExerciseId($elearningExerciseId)) {
          return(true);
        }
      }
    }

    return(false);
  }

  // Check if a lesson has at least one exercise
  function hasExercises($elearningLessonId) {
    if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonId($elearningLessonId)) {
      foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
        $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
        if ($elearningExerciseId) {
          return(true);
        }
      }
    }

    return(false);
  }

  // Delete a lesson
  function deleteLesson($elearningLessonId) {
    if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByLessonId($elearningLessonId)) {
      foreach ($elearningCourseItems as $elearningCourseItem) {
        $elearningCourseItemId = $elearningCourseItem->getId();
        $this->elearningCourseItemUtils->delete($elearningCourseItemId);
      }
    }

    if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonId($elearningLessonId)) {
      foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
        $this->elearningLessonParagraphUtils->deleteParagraph($elearningLessonParagraph->getId());
      }
    }

    $this->delete($elearningLessonId);
  }

  // Move a lesson, and possibly its exercises, into the garbage bin
  function putInGarbage($elearningLessonId, $exercisesInGarbage) {
    if ($exercisesInGarbage) {
      if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonId($elearningLessonId)) {
        foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
          $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
          if ($elearningExerciseId) {
            // Make sure the exercise is not used in a course
            if (!$elearningCourseItems = $this->elearningCourseItemUtils->selectByExerciseId($elearningExerciseId)) {
              // Make sure the exercise is not used by another lesson's paragraph
              if (!$wElearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByOtherLessonExerciseId($elearningExerciseId, $elearningLessonId)) {
                $this->elearningExerciseUtils->putInGarbage($elearningExerciseId);
              }
            }
          }
        }
      }
    }

    // A lesson, when put into the garbage, is removed from all the courses
    if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByLessonId($elearningLessonId)) {
      foreach ($elearningCourseItems as $elearningCourseItem) {
        $elearningCourseItemId = $elearningCourseItem->getId();
        $this->elearningCourseItemUtils->delete($elearningCourseItemId);
      }
    }

    if ($elearningLesson = $this->selectById($elearningLessonId)) {
      $elearningLesson->setGarbage(true);

      // Free the name of the lesson when it is put into the garbage
      $randomNumber = LibUtils::generateUniqueId();
      $name = $elearningLesson->getName() . ELEARNING_GARBAGE . '_' . $randomNumber;
      $elearningLesson->setName($name);

      $this->update($elearningLesson);
    }
  }

  // Restore a lesson from the garbage
  function restoreFromGarbage($elearningLessonId) {
    if ($elearningLesson = $this->selectById($elearningLessonId)) {
      // The paragraphs under lesson model headings
      $elearningLessonModelId = $elearningLesson->getLessonModelId();
      if ($elearningLessonHeadings = $this->elearningLessonHeadingUtils->selectByElearningLessonModelId($elearningLessonModelId)) {
        foreach ($elearningLessonHeadings as $elearningLessonHeading) {
          $elearningLessonHeadingId = $elearningLessonHeading->getId();
          if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonIdAndLessonHeadingId($elearningLessonId, $elearningLessonHeadingId)) {
            foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
              $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
              if ($elearningExerciseId) {
                $this->elearningExerciseUtils->restoreFromGarbage($elearningExerciseId);
              }
            }
          }
        }
      }

      // The paragraphs not under lesson model headings
      if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonIdAndNoLessonHeading($elearningLessonId)) {
        foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
          $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
          if ($elearningExerciseId) {
            $this->elearningExerciseUtils->restoreFromGarbage($elearningExerciseId);
          }
        }
      }

      $elearningLesson->setGarbage(false);
      $this->update($elearningLesson);
    }
  }

  // Check if the lesson is locked for the logged in admin
  function isLockedForLoggedInAdmin($elearningLessonId) {
    $locked = false;

    $adminLogin = $this->adminUtils->checkAdminLogin();
    if (!$this->adminUtils->isSuperAdmin($adminLogin)) {
      if ($elearningLesson = $this->selectById($elearningLessonId)) {
        $locked = $elearningLesson->getLocked();
      }
    }

    return($locked);
  }

  // Get the internal links for the lessons
  // The lessons are searched using their name or their course name if any
  function getInternalLinks($searchPattern) {
    $list = array();

    if ($searchPattern) {
      if ($elearningLessons = $this->selectLikePatternInLessonAndCourse($searchPattern)) {
        foreach ($elearningLessons as $elearningLesson) {
          $elearningLessonId = $elearningLesson->getId();
          $name = $elearningLesson->getName();
          $list['SYSTEM_PAGE_ELEARNING_LESSON' . $elearningLessonId] = $this->mlText[14] . " " . $name;
        }
      }
    }

    return($list);
  }

  // Check if at least one exercise of a lesson is available to a participant
  function isParticipantExerciseAvailable($elearningSubscriptionId, $elearningLessonId) {
    $isAvailable = false;

    if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonId($elearningLessonId)) {
      foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
        $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
        $exerciseIsAvailable = $this->elearningExerciseUtils->isParticipantExerciseAvailable($elearningSubscriptionId, $elearningExerciseId);
        if ($exerciseIsAvailable) {
          $isAvailable = true;
        }
      }
    }

    return($isAvailable);
  }

  // Duplicate a lesson
  function duplicate($elearningLessonId, $name, $description) {
    if ($elearningLesson = $this->selectById($elearningLessonId)) {
      $elearningLesson->setName($name);
      if ($description) {
        $elearningLesson->setDescription($description);
      }
      $this->insert($elearningLesson);
      $lastInsertElearningLessonId = $this->getLastInsertId();

      // Duplicate the lesson paragraphs
      $elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonId($elearningLessonId);
      foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
        $elearningLessonParagraphId = $elearningLessonParagraph->getId();
        $this->elearningLessonParagraphUtils->duplicate($elearningLessonParagraphId, $lastInsertElearningLessonId);
      }
    }
  }

  // Get the paragraphs of a lesson, possibly sorted by model headings
  function getLessonParagraphs($elearningLesson) {
    $paragraphs = array();

    // The paragraphs under lesson model headings
    $elearningLessonModelId = $elearningLesson->getLessonModelId();
    if ($elearningLessonModelId) {
      if ($elearningLessonHeadings = $this->elearningLessonHeadingUtils->selectByElearningLessonModelId($elearningLessonModelId)) {
        foreach ($elearningLessonHeadings as $elearningLessonHeading) {
          $elearningLessonHeadingId = $elearningLessonHeading->getId();
          $name = $elearningLessonHeading->getName();
          $content = $elearningLessonHeading->getContent();
          $image = $elearningLessonHeading->getImage();
          if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonIdAndLessonHeadingId($elearningLesson->getId(), $elearningLessonHeadingId)) {
            foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
              array_push($paragraphs, array($elearningLessonParagraph, $name, $content, $elearningLessonHeadingId));
            }
          }
        }
      }
    }

    // The paragraphs not under lesson model headings
    if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonIdAndNoLessonHeading($elearningLesson->getId())) {
      foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
        array_push($paragraphs, array($elearningLessonParagraph, '', '', ''));
      }
    }

    return($paragraphs);
  }

  // Search for a lesson
  function renderSearch() {
    global $gElearningUrl;

    $this->loadLanguageTexts();

    $randomNumber = LibUtils::generateUniqueId();

    $str = "<div class='elearning_search'>";

    $str .= "<div class='elearning_search_title'>" . $this->websiteText[0] . "</div>";

    $str .= "<div class='elearning_search_field'>"
      . "<form action='$gElearningUrl/lesson/display_lesson.php' method='post'>"
      . "<input class='elearning_search_input' type='text' id='elearningLessonName$randomNumber' value='' size='10' />"
      . "<input type='hidden' name='elearningLessonId' id='elearningLessonId$randomNumber' />"
      . $this->commonUtils->ajaxAutocomplete("$gElearningUrl/lesson/suggestLessons.php", "elearningLessonName$randomNumber", "elearningLessonId$randomNumber")
      . "</form>"
      . "</div>";

    $str .= "</div>";

    return($str);
  }

  // Render a link to a lesson
  function renderLessonComposeLink($elearningLessonId, $caption = '') {
    global $gElearningUrl;
    global $gJSNoStatus;
    global $gCommonImagesUrl;
    global $gImageLesson;

    $str = '';

    if ($elearningLesson = $this->selectById($elearningLessonId)) {
      $str .= " <a href='$gElearningUrl/lesson/compose.php?elearningLessonId=$elearningLessonId' $gJSNoStatus title='$caption'><img border='0' src='$gCommonImagesUrl/$gImageLesson' title='$caption' style='vertical-align:middle;'>" . $elearningLesson->getName() . "</a>";
    }

    return($str);
  } 

  // Get the list of links to compose page of exercises of a lesson
  function renderLessonExerciseComposeLinks($elearningLessonId, $caption = '') {
    $str = '';

    if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonId($elearningLessonId)) {
      foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
        $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
        $str .= $this->elearningExerciseUtils->renderExerciseComposeLink($elearningExerciseId, $caption);
      }
    }

    return($str);
  }

  // Get the list of links to exercises of a lesson
  function renderLessonExerciseDisplayLinks($elearningLessonId, $caption = '') {
    global $gElearningUrl;
    global $gCommonImagesUrl;
    global $gImageExercise;
    global $gJSNoStatus;

    $str = '';

    if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonId($elearningLessonId)) {
      foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
        $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
        if ($elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId)) {
          $str .= "<a href='$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus title='$caption'><img border='0' src='$gCommonImagesUrl/$gImageExercise' title='$caption' style='vertical-align:middle;'> " . $elearningExercise->getName() . "</a>";
        }
      }
    }

    return($str);
  }

  // Render the download link
  function renderDownload($audio) {
    global $gDataUrl;
    global $gDataPath;

    $str = '';

    if ($audio) {
      if (@is_file($gDataPath . "elearning/lesson/audio/$audio")) {
        $str = $this->playerUtils->renderDownload($gDataPath . "elearning/lesson/audio/$audio");
      }
    }

    return($str);
  }

  // Render the player
  function renderPlayer($audio) {
    global $gDataUrl;
    global $gDataPath;

    $str = '';

    if ($audio) {
      $str .= "<div class='elearning_lesson_player'>";

      $autoStartAudioPlayer = $this->elearningExerciseUtils->autoStartAudioPlayer();

      $this->playerUtils->setAutostart($autoStartAudioPlayer);

      if (@is_file($gDataPath . "elearning/lesson/audio/$audio")) {
        $str = $this->playerUtils->renderPlayer("$gDataUrl/elearning/lesson/audio/$audio");
      }

      $str .= "</div>";
    }

    return($str);
  }

  // Print the lesson
  function printLesson($elearningLessonId) {
    $str = LibJavaScript::getJSLib();
    $str .= "\n<script type='text/javascript'>printPage();</script>";

    $str .= $this->renderLessonForPrint($elearningLessonId);

    return($str);
  }

  // Render the description
  function renderDescription($description) {
    $description = nl2br($description);

    $str = "\n<div class='elearning_lesson_description'>$description</div>";

    return($str);
  }

  // Render the whole lesson in a printer friendly format
  function renderLessonForPrint($elearningLessonId) {
    global $gCommonImagesUrl;

    if (!$elearningLesson = $this->selectById($elearningLessonId)) {
      return;
    }

    $name = $elearningLesson->getName();
    $description = $elearningLesson->getDescription();

    $str = "\n<div class='elearning_lesson'>";

    // Render the logo
    $logo = $this->profileUtils->getLogoFilename();
    if ($logo && @is_file($this->profileUtils->filePath . $logo) && $this->elearningExerciseUtils->displayWebsiteLogo()) {
      $str .= "<div><img src='$this->profileUtils->fileUrl/$logo' title='' alt='' /></div>";
    }

    $str .= "\n<div class='elearning_lesson_name'>$name</div>";

    $str .= $this->renderDescription($description);

    $str .= $this->renderImage($elearningLesson);

    $paragraphs = $this->getLessonParagraphs($elearningLesson);

    $previousElearningLessonHeadingId = '';
    foreach ($paragraphs as $paragraph) {
      list($elearningLessonParagraph, $headingName, $headingContent, $elearningLessonHeadingId) = $paragraph;
      $elearningLessonParagraphId = $elearningLessonParagraph->getId();
      $headline = $elearningLessonParagraph->getHeadline();
      $body = $elearningLessonParagraph->getBody();
      $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
      $exerciseTitle = $elearningLessonParagraph->getExerciseTitle();

      if ($elearningLessonHeadingId != $previousElearningLessonHeadingId) {
        $previousElearningLessonHeadingId = $elearningLessonHeadingId;
        $str .= "<div class='elearning_lesson_heading'>";
        if ($headingName) {
          $str .= "\n<div class='elearning_lesson_heading_name'>$headingName</div>";
        }
        $str .= $this->elearningLessonHeadingUtils->renderImage($elearningLessonHeadingId);
        if ($headingContent) {
          $str .= "\n<div class='elearning_lesson_heading_content'>$headingContent</div>";
        }
      }

      $str .= "<div class='elearning_lesson_paragraph'>";

      $str .= "\n<div class='elearning_lesson_paragraph_headline'>$headline</div>";

      $str .= $this->elearningLessonParagraphUtils->renderImage($elearningLessonParagraphId);

      $str .= "\n<div class='elearning_lesson_paragraph_body'>$body</div>";

      // Render the link to the exercise if any
      if ($elearningExerciseId) {
        if ($elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId)) {
          $title = $elearningExercise->getName();
          if ($exerciseTitle) {
            $title = $exerciseTitle;
          }
          $title = $this->websiteText[14] . ' ' . $title;
          $str .= "\n<div class='elearning_lesson_paragraph_exercise'>" . $title . "</div>";
        }
      }

      $str .= "</div>";
      $str .= "</div>";
    }

    $str .= $this->elearningExerciseUtils->renderCopyright();

    $str .= $this->elearningExerciseUtils->renderAddress();

    return($str);
  }

  // Render the image of the lesson
  function renderImage($elearningLesson, $emailFormat = false) {
    global $gDataPath;
    global $gDataUrl;
    global $gUtilsUrl;
    global $gIsPhoneClient;

    $image = $elearningLesson->getImage();

    $imagePath = $this->imageFilePath;
    $imageUrl = $this->imageFileUrl;

    // Resize the image to the following width
    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("ELEARNING_PHONE_EXERCISE_IMAGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_IMAGE_WIDTH");
    }

    $str = '';

    if ($image && @file_exists($imagePath . $image)) {
      $str .= "<div class='elearning_lesson_image'>";

      if (LibImage::isImage($imagePath . $image)) {

        // Check if the images are to be rendered in an email format
        // If so the image file path will be replaced bi 'cid' sequences
        // and no on-the-fly image resizing should take place
        if ($emailFormat) {
          $url = $imageUrl . '/' . $image;
        } else {
          if ($width && !$this->fileUploadUtils->isGifImage($imagePath . $image)) {
            // The image is created on the fly
            $filename = urlencode($imagePath . $image);
            $url = $gUtilsUrl . "/printImage.php?filename=" . $filename
              . "&amp;width=" . $width . "&amp;height=";
          } else {
            $url = $imageUrl . '/' . $image;
          }
        }

        $str .= "<img class='elearning_lesson_image_file' src='$url' title='' alt='' />";
      } else {
        $libFlash = new LibFlash();
        if ($libFlash->isFlashFile($imageFile)) {
          $str .= $libFlash->renderObject("$imageUrl/$image");
        }
      }
      $str .= "</div>";
    }

    return($str);
  }

  // Check that the user is logged in
  function checkUserLogin() {
    $this->userUtils->checkValidUserLogin();
  }

  // Check if the lesson requires a login and if the user is logged in
  function checkUserLoginForLesson($elearningLesson, $elearningSubscription) {
    if ($this->requireUserLogin($elearningLesson, $elearningSubscription)) {
      // Check for a login if required and if the lesson is not marked as public
      // A public lesson escapes the login procedure
      if (!$elearningLesson->getPublicAccess()) {
        if (!$this->isFreeSample($elearningLesson, $elearningSubscription)) {
          $this->checkUserLogin();
        }
      }
    }
  }

  // Check if the lesson is a free sample
  // A free sample is a course item that is offered for free and does not require a user login
  function isFreeSample($elearningLesson, $elearningSubscription) {
    $freeSample = false;

    if ($elearningSubscription) {
      $elearningCourseId = $elearningSubscription->getCourseId();
      if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
        $freeSamples = $elearningCourse->getFreeSamples();
        if ($freeSamples > 0) {
          if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByCourseId($elearningCourseId)) {
            $i = 1;
            foreach ($elearningCourseItems as $elearningCourseItem) {
              if ($elearningCourseItem->getElearningLessonId() == $elearningLesson->getId()) {
                break;
              }
              $i++;
            }
            if ($i <= $freeSamples) {
              $freeSample = true;
            }
          }
        }
      }
    }

    return($freeSample);
  }

  // Check if a user login is required to access the lesson
  function requireUserLogin($elearningLesson, $elearningSubscription) {
    $loginIsRequired = $this->preferenceUtils->getValue("ELEARNING_SECURED");

    if (!$loginIsRequired) {
      if ($elearningSubscription) {
        $elearningCourseId = $elearningSubscription->getCourseId();
        if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
          $loginIsRequired = $elearningCourse->getSecured();
        }
      }
    }

    if (!$loginIsRequired) {
      $loginIsRequired = $elearningLesson->getSecured();
    }

    return($loginIsRequired);
  }

  // Check if a lesson is part of only one course
  function partOfOnlyOneCourse($elearningLessonId) {
    if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByLessonId($elearningLessonId)) {
      if (count($elearningCourseItems) == 1) {
        return(true);
      }
    }

    return(false);
  }

  // Check if the content was created by the user
  function createdByUser($elearningLessonId, $userId) {
    if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByLessonId($elearningLessonId)) {
      foreach ($elearningCourseItems as $elearningCourseItem) {
        $elearningCourseId = $elearningCourseItem->getElearningCourseId();
        if ($this->elearningCourseUtils->createdByUser($elearningCourseId, $userId)) {
          return(true);
        }
      }
    }

    return(false);
  }

  // Render some instructions
  function renderInstructions($elearningLessonId) {
    $instructions = $this->getInstructions($elearningLessonId);;

    if ($instructions) {
      $instructions = "<div class='elearning_lesson_instruction'>$instructions</div>";
    }

    return($instructions );
  }

  // Get some instructions
  function getInstructions($elearningLessonId) {
    $instructions = '';

    if ($elearningLesson = $this->selectById($elearningLessonId)) {
      $currentLanguageCode = $this->languageUtils->getCurrentLanguageCode();
      $instructions = $elearningLesson->getInstructions();
      $instructions = $this->languageUtils->getTextForLanguage($instructions, $currentLanguageCode);
    }

    return($instructions );
  }

  // Render a lesson
  function renderLesson($elearningLesson, $elearningSubscription) {
    global $gUserUrl;
    global $gElearningUrl;
    global $gImagesUserUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $elearningLessonId = $elearningLesson->getId();
    $name = $elearningLesson->getName();
    $description = $elearningLesson->getDescription();
    $introduction = $elearningLesson->getIntroduction();
    $audio = $elearningLesson->getAudio();
    $public = $elearningLesson->getPublicAccess();
    $elearningLessonModelId = $elearningLesson->getLessonModelId();

    // Get the logged in user if any
    $email = $this->userUtils->getUserEmail();

    $str = "\n<div class='elearning_lesson'>";

    $str .= "\n<div class='elearning_lesson_name'>$name</div>";

    $str .= $this->renderDescription($description);

    $str .= $this->renderImage($elearningLesson);

    $str .= $this->renderPlayer($audio);

    if ($this->elearningExerciseUtils->displayDownloadAudioFileIcon()) {
      $str .= ' ' . $this->renderDownload($audio);
    }

    $str .= $this->renderInstructions($elearningLessonId);

    $str .= "\n<div class='elearning_lesson_introduction'>$introduction</div>";

    $paragraphs = $this->getLessonParagraphs($elearningLesson);

    $previousElearningLessonHeadingId = '';
    foreach ($paragraphs as $paragraph) {
      list($elearningLessonParagraph, $headingName, $headingContent, $elearningLessonHeadingId) = $paragraph;
      $elearningLessonParagraphId = $elearningLessonParagraph->getId();
      $headline = $elearningLessonParagraph->getHeadline();
      $body = $elearningLessonParagraph->getBody();
      $audio = $elearningLessonParagraph->getAudio();
      $video = $elearningLessonParagraph->getVideo();
      $videoUrl = $elearningLessonParagraph->getVideoUrl();
      $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
      $exerciseTitle = $elearningLessonParagraph->getExerciseTitle();

      if ($gIsPhoneClient) {
        $video = $this->commonUtils->adjustVideoWidthToPhone($video);
      }

      if ($elearningLessonHeadingId != $previousElearningLessonHeadingId) {
        $previousElearningLessonHeadingId = $elearningLessonHeadingId;
        $str .= "<div class='elearning_lesson_heading'>";
        if ($headingName) {
          $str .= "\n<div class='elearning_lesson_heading_name'>$headingName</div>";
        }
        $str .= $this->elearningLessonHeadingUtils->renderImage($elearningLessonHeadingId);
        if ($headingContent) {
          $str .= "\n<div class='elearning_lesson_heading_content'>$headingContent</div>";
        }
        $str .= "</div>";
      }

      $str .= "<div class='elearning_lesson_paragraph'>";

      $str .= "\n<div class='elearning_lesson_paragraph_headline'>$headline</div>";

      $str .= $this->elearningLessonParagraphUtils->renderImage($elearningLessonParagraphId);

      $str .= $this->elearningLessonParagraphUtils->renderPlayer($audio);

      $str .= "\n<div class='elearning_lesson_paragraph_body'>$body</div>";

      // Render the video
      if ($video) {
        $str .= "\n<div class='elearning_lesson_paragraph_video'>$video</div>";
      }
      if ($videoUrl) {
        $str .= "\n<div class='elearning_lesson_paragraph_video'>"
          . "<a href='$videoUrl' title='" . $this->websiteText[13] . "' onclick=\"window.open(this.href, '_blank'); return(false);\">" . $this->websiteText[12] . "</a>"
          . "</div>";
      }

      // Render the link to the exercise if any
      if ($elearningExerciseId) {
        if ($elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId)) {
          $title = $elearningExercise->getName();
          if ($exerciseTitle) {
            $title = $exerciseTitle;
          }
          $title = $this->websiteText[14] . ' ' . $title;
          if ($elearningSubscription) {
            $elearningSubscriptionId = $elearningSubscription->getId();
          } else {
            $elearningSubscriptionId = '';
          }
          $str .= "\n<div class='elearning_lesson_paragraph_exercise'>"
            . "<a href='$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId' title='" . $this->websiteText[3] . "' onclick=\"window.open(this.href, '_blank'); return(false);\">" . $title . "</a>"
            . "</div>";
        }
      }

      $str .= "</div>";
    }

    // Render the copyright notice
    $str .= $this->elearningExerciseUtils->renderCopyright();

    // Render the website address
    $str .= $this->elearningExerciseUtils->renderAddress();

    if (!$gIsPhoneClient) {
      $str .= "\n<div class='elearning_lesson_icons'>";
      $str .= ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_PRINTER . "' class='no_style_image_icon' title='" .  $this->websiteText[112] . " 'alt='' />", "$gElearningUrl/lesson/print_lesson.php?elearningLessonId=$elearningLessonId", 600, 600);

      $str .= ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_EMAIL_FRIEND . "' class='no_style_image_icon' title='" . $this->websiteText[80] .  "' alt='' />", "$gElearningUrl/lesson/send.php?elearningLessonId=$elearningLessonId", 600, 600);
      $str .= "\n</div>";

      if (!$this->preferenceUtils->getValue("ELEARNING_HIDE_SOCIAL_BUTTONS")) {
        $strLink = "$gElearningUrl/lesson/display_lesson.php?elearningLessonId=$elearningLessonId";
        $str .= "<div class='elearning_social_buttons'>";
        $str .= $this->commonUtils->renderSocialNetworksButtons($name, $strLink);
        $str .= " </div>";
      }

      if ($this->websiteUtils->isCurrentWebsiteOption('OPTION_AFFILIATE')) {
        $str .= "<div>" . $this->commonUtils->renderPoweredByLearnInTouch() . "</div>";
      }
    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForLesson() {
    global $gImagesUserUrl;
    global $gStylingImage;

    $str = "\n<div class='elearning_lesson'>A lesson"
      . "<div class='elearning_lesson_name'>The name of the lesson</div>"
      . "<div class='elearning_lesson_description'>The description of the lesson</div>"
      . "<div class='elearning_level'>The level of the lesson"
      . "<span class='elearning_level_labelled_name'>The label and name of the level"
      . "<span class='elearning_level_name'>The name of the level</span>"
      . "</span>"
      . "</div>"
      . "<div class='elearning_lesson_heading'>A heading"
      . "<div class='elearning_lesson_heading_name'>The name of the heading</div>"
      . "<div class='elearning_lesson_heading_content'>The content of the heading</div>"
      . "</div>"
      . "<div class='elearning_lesson_introduction'>The introduction of the lesson</div>"
      . "<div class='elearning_lesson_image'>The image of the lesson"
      . "<img class='elearning_lesson_image_file' src='$gStylingImage' title='The border of the image of the lesson' alt='' />"
      . "</div>"
      . "<div class='elearning_lesson_player'>The audio player of the lesson</div>"
      . "<div class='elearning_lesson_instruction'>The instructions for the lesson</div>"
      . "<div class='elearning_lesson_paragraph'>A paragraph in the lesson"
      . "<div class='elearning_lesson_paragraph_headline'>The headline of a paragraph</div>"
      . "<div class='elearning_lesson_paragraph_image'>The image of a paragraph"
      . "<img class='elearning_lesson_paragraph_image_file' src='$gStylingImage' title='The border of the image of a paragraph' alt='' />"
      . "</div>"
      . "<div class='elearning_lesson_paragraph_player'>The audio player of a paragraph</div>"
      . "<div class='elearning_lesson_paragraph_body'>The body of a paragraph</div>"
      . "<div class='elearning_lesson_paragraph_video'>The video of a paragraph</div>"
      . "<div class='elearning_lesson_paragraph_exercise'>The link to an exercise from a paragraph</div>"
      . "</div>"
      . "<div class='elearning_exercise_copyright'>The copyright notice</div>"
      . "<div class='elearning_exercise_address'>The address</div>"
      . "<div class='elearning_lesson_icons'>The icons</div>"
      . "<div class='elearning_social_buttons'>The social networks buttons</div>"
      . "</div>";

    return($str);
  }

}

?>
