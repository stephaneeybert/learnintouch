<?

class ElearningCourseUtils extends ElearningCourseDB {

  var $mlText;
  var $websiteText;

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $currentMatterId;

  var $languageUtils;
  var $preferenceUtils;
  var $adminUtils;
  var $elearningExerciseUtils;
  var $elearningLessonUtils;
  var $elearningCourseItemUtils;
  var $elearningSessionCourseUtils;
  var $elearningSubscriptionUtils;
  var $elearningLessonParagraphUtils;
  var $elearningLessonHeadingUtils;
  var $fileUploadUtils;

  function ElearningCourseUtils() {
    $this->ElearningCourseDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'elearning/course/image/';
    $this->imageFileUrl = $gDataUrl . '/elearning/course/image';

    $this->currentMatterId = "elearningCurrentMatterId";
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'elearning')) {
        mkdir($gDataPath . 'elearning');
      }
      if (!is_dir($gDataPath . 'elearning/course')) {
        mkdir($gDataPath . 'elearning/course');
      }
      mkdir($this->imageFilePath);
      chmod($this->imageFilePath, 0755);
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
          if (@file_exists($filePath . $oneFile)) {
            @unlink($filePath . $oneFile);
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

  // Check if the course was created by the user
  function createdByUser($elearningCourseId, $userId) {
    $result = false;

    if ($elearningCourses = $this->selectByUserId($userId)) {
      foreach ($elearningCourses as $elearningCourse) {
        if ($elearningCourseId == $elearningCourse->getId()) {
          $result = true;
        }
      }
    }

    return($result);
  }

  // Check if the course is locked for the logged in admin
  function isLockedForLoggedInAdmin($elearningCourseId) {
    $locked = false;

    $adminLogin = $this->adminUtils->checkAdminLogin();
    if (!$this->adminUtils->isSuperAdmin($adminLogin)) {
      if ($elearningCourse = $this->selectById($elearningCourseId)) {
        $locked = $elearningCourse->getLocked();
      }
    }

    return($locked);
  }

  // Check if the participants can subscribe to the course by themselves
  function autoSubscriptions() {
    $auto = false;

    if ($elearningCourses = $this->selectAutoSubscription()) {
      if (count($elearningCourses) > 0) {
        $auto = true;
      }
    }

    return($auto);
  }

  // Check that the user is logged in
  function checkUserLogin() {
    $this->userUtils->checkValidUserLogin();
  }

  // Check if a user login is required to access the course
  function requireUserLogin($elearningCourse) {
    $loginIsRequired = $this->preferenceUtils->getValue("ELEARNING_SECURED");

    if (!$loginIsRequired) {
      $loginIsRequired = $elearningCourse->getSecured();
    }

    return($loginIsRequired);
  }

  // Check if the course requires a login and if the user is logged in
  function checkUserLoginForCourse($elearningCourse) {
    if ($this->requireUserLogin($elearningCourse)) {
      $this->checkUserLogin();
    }
  }

  // Check if the participants can subscribe to the course by themselves
  function autoSubscription($elearningCourse) {
    if ($elearningCourse->getAutoSubscription()) {
      return(true);
    } else {
      return(false);
    }
  }

  // Get the list of courses
  function getAll() {
    $list = array();

    if ($elearningCourses = $this->selectAll()) {
      foreach ($elearningCourses as $elearningCourse) {
        $elearningCourseId = $elearningCourse->getId();
        $name = $elearningCourse->getName();
        $list['SYSTEM_PAGE_ELEARNING_COURSE' . $elearningCourseId] = $this->mlText[14]
          . " " . $name;
      }
    }

    return($list);
  }

  // Duplicate a course
  function duplicate($elearningCourseId, $name, $description) {
    if ($elearningCourse = $this->selectById($elearningCourseId)) {
      $duplicateElearningCourse = new ElearningCourse();
      $duplicateElearningCourse->setName($name);
      if ($description) {
        $duplicateElearningCourse->setDescription($description);
      }
      $duplicateElearningCourse->setImage($elearningCourse->getImage());
      $duplicateElearningCourse->setInstantCorrection($elearningCourse->getInstantCorrection());
      $duplicateElearningCourse->setInstantCongratulation($elearningCourse->getInstantCongratulation());
      $duplicateElearningCourse->setInstantSolution($elearningCourse->getInstantSolution());
      $duplicateElearningCourse->setImportable($elearningCourse->getImportable());
      $duplicateElearningCourse->setLocked($elearningCourse->getLocked());
      $duplicateElearningCourse->setSecured($elearningCourse->getSecured());
      $duplicateElearningCourse->setFreeSamples($elearningCourse->getFreeSamples());
      $duplicateElearningCourse->setAutoSubscription($elearningCourse->getAutoSubscription());
      $duplicateElearningCourse->setInterruptTimedOutExercise($elearningCourse->getInterruptTimedOutExercise());
      $duplicateElearningCourse->setResetExerciseAnswers($elearningCourse->getResetExerciseAnswers());
      $duplicateElearningCourse->setExerciseOnlyOnce($elearningCourse->getExerciseOnlyOnce());
      $duplicateElearningCourse->setExerciseAnyOrder($elearningCourse->getExerciseAnyOrder());
      $duplicateElearningCourse->setSaveResultOption($elearningCourse->getSaveResultOption());
      $duplicateElearningCourse->setShuffleQuestions($elearningCourse->getShuffleQuestions());
      $duplicateElearningCourse->setShuffleAnswers($elearningCourse->getShuffleAnswers());
      $duplicateElearningCourse->setMatterId($elearningCourse->getMatterId());
      $duplicateElearningCourse->setUserId($elearningCourse->getUserId());
      $this->insert($duplicateElearningCourse);
      $lastInsertElearningCourseId = $this->getLastInsertId();

      if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByCourseId($elearningCourseId)) {
        foreach ($elearningCourseItems as $elearningCourseItem) {
          $duplicateElearningCourseItem = new ElearningCourseItem();
          $duplicateElearningCourseItem->setElearningCourseId($lastInsertElearningCourseId);
          $duplicateElearningCourseItem->setElearningExerciseId($elearningCourseItem->getElearningExerciseId());
          $duplicateElearningCourseItem->setElearningLessonId($elearningCourseItem->getElearningLessonId());
          $duplicateElearningCourseItem->setListOrder($elearningCourseItem->getListOrder());
          $this->elearningCourseItemUtils->insert($duplicateElearningCourseItem);
        }
      }
    }
  }

  function deleteCourse($elearningCourseId, $clearSubscriptions = false) {
    // Check that there are no sessions using the course
    if (!$elearningSessionCourse = $this->elearningSessionCourseUtils->selectByCourseId($elearningCourseId)) {
      if ($clearSubscriptions) {
        if ($elearningSubscriptions = $this->elearningSubscriptionUtils->selectByCourseId($elearningCourseId)) {
          foreach ($elearningSubscriptions as $elearningSubscription) {
            $elearningSubscription->setCourseId('');
            $this->elearningSubscriptionUtils->update($elearningSubscription);
          }
        }
      }

      if (!$elearningSubscriptions = $this->elearningSubscriptionUtils->selectByCourseId($elearningCourseId)) {
        // Delete the course items if any
        if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByCourseId($elearningCourseId)) {
          foreach ($elearningCourseItems as $elearningCourseItem) {
            $this->elearningCourseItemUtils->delete($elearningCourseItem->getId());
          }
        }

        // Delete the course information if any
        if ($elearningCourseInfos = $this->elearningCourseInfoUtils->selectByCourseId($elearningCourseId)) {
          foreach ($elearningCourseInfos as $elearningCourseInfo) {
            $elearningCourseInfoId = $elearningCourseInfo->getId();
            $this->elearningCourseInfoUtils->deleteCourseInfo($elearningCourseInfoId);
          }
        }

        $this->delete($elearningCourseId);
      }
    }
  }

  // Get the exercises of a course
  function getCourseExercises($elearningCourseId) {
    $elearningExerciseIds = array();

    if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByCourseId($elearningCourseId)) {
      foreach ($elearningCourseItems as $elearningCourseItem) {
        $elearningExerciseId = $elearningCourseItem->getElearningExerciseId();
        $elearningLessonId = $elearningCourseItem->getElearningLessonId();

        if ($elearningExerciseId) {
          array_push($elearningExerciseIds, $elearningExerciseId);
        } else if ($elearningLessonId) {
          if ($elearningLesson = $this->elearningLessonUtils->selectById($elearningLessonId)) {
            $elearningLessonModelId = $elearningLesson->getLessonModelId();
            if ($elearningLessonHeadings = $this->elearningLessonHeadingUtils->selectByElearningLessonModelId($elearningLessonModelId)) {
              foreach ($elearningLessonHeadings as $elearningLessonHeading) {
                $elearningLessonHeadingId = $elearningLessonHeading->getId();
                if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonIdAndLessonHeadingId($elearningLessonId, $elearningLessonHeadingId)) {
                  foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
                    array_push($elearningExerciseIds, $elearningLessonParagraph->getElearningExerciseId());
                  }
                }
              }
            }

            if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonIdAndNoLessonHeading($elearningLessonId)) {
              foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
                array_push($elearningExerciseIds, $elearningLessonParagraph->getElearningExerciseId());
              }
            }
          }
        }
      }
    }

    return($elearningExerciseIds);
  }

  function selectCoursesNotAssignedToSession($elearningSessionId) {
    $courses = array();

    $elearningCourses = $this->selectAll();
    if ($elearningCourses) {
      foreach ($elearningCourses as $elearningCourse) {
        $elearningCourseId = $elearningCourse->getId();
        if (!$this->elearningSessionCourseUtils->selectBySessionIdAndCourseId($elearningSessionId, $elearningCourseId)) {
          array_push($courses, $elearningCourse);
        }
      }
    }

    return($courses);
  }

  // Render the list of courses
  function renderList() {
    global $gElearningUrl;
    global $gIsPhoneClient;

    $elearningCourses = $this->selectAll();

    $str = '';

    $str .= "\n<div class='elearning_course_list'>";

    $str .= "\n<div class='elearning_course_list_title'>"
      . $this->websiteText[5] . "</div>";

    $str .= "\n<div class='elearning_course_list_comment'>"
      . $this->websiteText[6] . "</div>";

    $str .= "\n<table border='0' width='100%' cellspacing='0' cellpadding='0'>";

    foreach ($elearningCourses as $elearningCourse) {
      $elearningCourseId = $elearningCourse->getId();
      $name = $elearningCourse->getName();
      $description = $elearningCourse->getDescription();
      $strImage = $elearningCourse->renderImage($elearningCourseId);

      $strName = "<a href='$gElearningUrl/course/display.php?elearningCourseId=$elearningCourseId'>$name</a>";

      $str .= "\n<tr>";
      $str .= "\n<td><div class='elearning_course_list_image'>$strImage</div>";
      $str .= "</td><td>";
      $str .= "\n<td><div class='elearning_course_list_name'>$strName</div>";
      $str .= "</td><td>";
      $str .= "\n<div class='elearning_course_list_description'>$description</div></td>";
      $str .= "\n</tr>";
    }

    $str .= "\n</table>";

    $str .= "\n</div>";

    return($str);
  }

  // Render the image of the course
  function renderImage($elearningCourseId, $emailFormat = false) {
    global $gDataPath;
    global $gDataUrl;
    global $gUtilsUrl;
    global $gIsPhoneClient;

    if (!$elearningCourse = $this->selectById($elearningCourseId)) {
      return;
    }

    $image = $elearningCourse->getImage();

    $imagePath  = $this->imageFilePath;
    $imageUrl  = $this->imageFileUrl;

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("ELEARNING_PHONE_EXERCISE_IMAGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_IMAGE_WIDTH");
    }

    $str = '';

    if ($image && @file_exists($imagePath . $image)) {
      $str .= "<div class='elearning_course_image'>";

      if (LibImage::isImage($imagePath . $image)) {

        // Check if the images are to be rendered in an email format
        // If so the image file path will be replaced bi 'cid' sequences
        // and no on-the-fly image resizing should take place
        if ($emailFormat) {
          $url = $imageUrl . '/' . $image;
        } else {
          if (!$this->fileUploadUtils->isGifImage($imagePath . $image)) {
            // The image is created on the fly
            $filename = urlencode($imagePath . $image);
            $url = $gUtilsUrl . "/printImage.php?filename=" . $filename
              . "&amp;width=" . $width . "&amp;height=";
          } else {
            $url = $imageUrl . '/' . $image;
          }
        }

        $str .= "<img class='elearning_course_image_file' src='$url' title='' alt='' width='$width' />";
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

}

?>
