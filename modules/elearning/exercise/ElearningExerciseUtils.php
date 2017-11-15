<?

class ElearningExerciseUtils extends ElearningExerciseDB {

  var $mlText;
  var $websiteText;

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $audioFileSize;
  var $audioFilePath;
  var $audioFileUrl;

  var $cookieDuration;

  var $cookieVisitorEmail;
  var $cookieTeacherId;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $commonUtils;
  var $popupUtils;
  var $profileUtils;
  var $clockUtils;
  var $playerUtils;
  var $lexiconEntryUtils;
  var $userUtils;
  var $socialUserUtils;
  var $adminUtils;
  var $websiteUtils;
  var $mailAddressUtils;
  var $templateUtils;
  var $templateModelUtils;
  var $elearningExercisePageUtils;
  var $elearningCourseUtils;
  var $elearningCourseItemUtils;
  var $elearningCourseInfoUtils;
  var $elearningSubscriptionUtils;
  var $elearningLessonUtils;
  var $elearningQuestionUtils;
  var $elearningResultUtils;
  var $elearningResultRangeUtils;
  var $elearningQuestionResultUtils;
  var $elearningAnswerUtils;
  var $elearningSessionUtils;
  var $elearningClassUtils;
  var $elearningTeacherUtils;
  var $elearningScoringUtils;
  var $elearningLevelUtils;
  var $elearningLessonParagraphUtils;
  var $elearningAssignmentUtils;
  var $fileUploadUtils;

  function ElearningExerciseUtils() {
    $this->ElearningExerciseDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'elearning/exercise/image/';
    $this->imageFileUrl = $gDataUrl . '/elearning/exercise/image';

    $this->audioFileSize = 4096000;
    $this->audioFilePath = $gDataPath . 'elearning/exercise/audio/';
    $this->audioFileUrl = $gDataUrl . '/elearning/exercise/audio';

    $this->cookieDuration = 60 * 60 * 8;
    $this->cookieVisitorEmail = "elearningVisitorEmail";
    $this->cookieTeacherId = "elearningTeacherId";
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'elearning')) {
        mkdir($gDataPath . 'elearning');
      }
      if (!is_dir($gDataPath . 'elearning/exercise')) {
        mkdir($gDataPath . 'elearning/exercise');
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

  function loadPreferences() {
    $this->loadLanguageTexts();

    $templateModels = $this->templateModelUtils->getAllModels();

    $this->preferences = array(
      "ELEARNING_SECURED" =>
      array($this->mlText[1], $this->mlText[31], PREFERENCE_TYPE_BOOLEAN, ''),
        "ELEARNING_AUTOMATIC_ALERT" =>
        array($this->mlText[82], $this->mlText[83], PREFERENCE_TYPE_SELECT, array('' => '', 'ELEARNING_AUTOMATIC_ALERT_EXERCISE' => $this->mlText[89], 'ELEARNING_AUTOMATIC_ALERT_EXERCISE_TEXT' => $this->mlText[93], 'ELEARNING_AUTOMATIC_ALERT_ASSIGNMENT' => $this->mlText[97])),
          "ELEARNING_SEND_RESULT" =>
          array($this->mlText[66], $this->mlText[67], PREFERENCE_TYPE_BOOLEAN, ''),
            "ELEARNING_HIDE_RESULT_IF_NO_EMAIL" =>
            array($this->mlText[68], $this->mlText[69], PREFERENCE_TYPE_BOOLEAN, ''),
              "ELEARNING_CONTACT_PAGE" =>
              array($this->mlText[84], $this->mlText[85], PREFERENCE_TYPE_BOOLEAN, ''),
                "ELEARNING_DISPLAY_CONTACT_PAGE_BUTTON" =>
                array($this->mlText[171], $this->mlText[172], PREFERENCE_TYPE_BOOLEAN, ''),
                  "ELEARNING_CONTACT_PAGE_FIELD_REQUIRED" =>
                  array($this->mlText[22], $this->mlText[23], PREFERENCE_TYPE_BOOLEAN, ''),
                    "ELEARNING_CONTACT_ON_MESSAGE" =>
                    array($this->mlText[187], $this->mlText[188], PREFERENCE_TYPE_BOOLEAN, ''),
                      "ELEARNING_REGISTER_EMAIL" =>
                      array($this->mlText[63], $this->mlText[65], PREFERENCE_TYPE_BOOLEAN, ''),
                        "ELEARNING_RESET_ANSWERS" =>
                        array($this->mlText[149], $this->mlText[159], PREFERENCE_TYPE_BOOLEAN, ''),
                              "ELEARNING_SAVE_RESULT" =>
                              array($this->mlText[201], $this->mlText[202], PREFERENCE_TYPE_SELECT, array('' => '', 'ELEARNING_SAVE_RESULT_FIRST' => $this->mlText[203], 'ELEARNING_SAVE_RESULT_EVERY_TIME' => $this->mlText[204], 'ELEARNING_SAVE_RESULT_LAST_ONLY' => $this->mlText[231], 'ELEARNING_SAVE_RESULT_BETTER' => $this->mlText[205])),
                        "ELEARNING_SAVE_RESULT_WATCHED_LIVE" =>
                        array($this->mlText[247], $this->mlText[248], PREFERENCE_TYPE_BOOLEAN, ''),
                                                        "ELEARNING_INACTIVE_DURATION" =>
                                                         array($this->mlText[34], $this->mlText[9], PREFERENCE_TYPE_TEXT, '1'),
                                                        "ELEARNING_ABSENT_DURATION" =>
                                                         array($this->mlText[216], $this->mlText[217], PREFERENCE_TYPE_TEXT, '15'),
                                "ELEARNING_MULTIPLE_ANSWERS" =>
                                array($this->mlText[117], $this->mlText[118], PREFERENCE_TYPE_BOOLEAN, ''),
                                  "ELEARNING_INSTANT_CORRECTION" =>
                                  array($this->mlText[194], $this->mlText[195], PREFERENCE_TYPE_BOOLEAN, ''),
                                    "ELEARNING_INSTANT_SOLUTION" =>
                                    array($this->mlText[208], $this->mlText[209], PREFERENCE_TYPE_BOOLEAN, ''),
                                      "ELEARNING_INSTANT_ON_NO_ANSWER" =>
                                      array($this->mlText[249], $this->mlText[250], PREFERENCE_TYPE_BOOLEAN, ''),
                                      "ELEARNING_INSTANT_EXPLANATION_ON" =>
                                      array($this->mlText[199], $this->mlText[200], PREFERENCE_TYPE_BOOLEAN, ''),
                                        "ELEARNING_INSTANT_EXPLANATION" =>
                                        array($this->mlText[197], $this->mlText[198], PREFERENCE_TYPE_MLTEXT, $this->mlText[196]),
                                          "ELEARNING_INSTANT_CONGRATULATION_ON" =>
                                          array($this->mlText[223], $this->mlText[224], PREFERENCE_TYPE_BOOLEAN, ''),
                                            "ELEARNING_INSTANT_CONGRATULATION" =>
                                            array($this->mlText[221], $this->mlText[222], PREFERENCE_TYPE_MLTEXT, $this->mlText[220]),
                              "ELEARNING_GRAPH_FILTER" =>
                              array($this->mlText[257], $this->mlText[258], PREFERENCE_TYPE_SELECT, array('' => '', 'ELEARNING_GRAPH_FILTER_INSTANT' => $this->mlText[259], 'ELEARNING_GRAPH_FILTER_NOT_INSTANT' => $this->mlText[260])),
                                              "ELEARNING_SHUFFLE_QUESTIONS" =>
                                              array($this->mlText[212], $this->mlText[213], PREFERENCE_TYPE_BOOLEAN, ''),
                                                "ELEARNING_SHUFFLE_ANSWERS" =>
                                                array($this->mlText[214], $this->mlText[215], PREFERENCE_TYPE_BOOLEAN, ''),
                                                  "ELEARNING_EXERCISE_INTERRUPT" =>
                                                  array($this->mlText[123], $this->mlText[124], PREFERENCE_TYPE_BOOLEAN, ''),
                                                    "ELEARNING_HIDE_TIMES" =>
                                                    array($this->mlText[139], $this->mlText[140], PREFERENCE_TYPE_BOOLEAN, ''),
                                                      "ELEARNING_EXERCISE_TIMEOUT" =>
                                                      array($this->mlText[130], $this->mlText[131], PREFERENCE_TYPE_MLTEXT, $this->mlText[132]),
                                                        "ELEARNING_EXERCISE_ABANDONED" =>
                                                        array($this->mlText[126], $this->mlText[127], PREFERENCE_TYPE_TEXT, 60),
                                                          "ELEARNING_HIDE_KEYBOARD" =>
                                                          array($this->mlText[218], $this->mlText[219], PREFERENCE_TYPE_BOOLEAN, ''),
                                                            "ELEARNING_HIDE_LEVEL" =>
                                                            array($this->mlText[232], $this->mlText[233], PREFERENCE_TYPE_BOOLEAN, ''),
                                                              "ELEARNING_HIDE_SOCIAL_BUTTONS" =>
                                                              array($this->mlText[150], $this->mlText[151], PREFERENCE_TYPE_BOOLEAN, ''),
                                                                "ELEARNING_KEYBOARD_LETTERS" =>
                                                                array($this->mlText[61], $this->mlText[70], PREFERENCE_TYPE_TEXT, ''),
                                                                  "ELEARNING_DISPLAY_EXPLANATION" =>
                                                                  array($this->mlText[182], $this->mlText[183], PREFERENCE_TYPE_BOOLEAN, ''),
                                                                      "ELEARNING_REQUIRE_SESSION" =>
                                                                      array($this->mlText[116], $this->mlText[119], PREFERENCE_TYPE_BOOLEAN, ''),
                                                                        "ELEARNING_REQUIRE_COURSE" =>
                                                                        array($this->mlText[146], $this->mlText[147], PREFERENCE_TYPE_BOOLEAN, ''),
                                                                          "ELEARNING_REQUIRE_CLASS" =>
                                                                          array($this->mlText[141], $this->mlText[142], PREFERENCE_TYPE_BOOLEAN, ''),
                                                                            "ELEARNING_REQUIRE_TEACHER" =>
                                                                            array($this->mlText[143], $this->mlText[144], PREFERENCE_TYPE_BOOLEAN, ''),
                                                                                "ELEARNING_COURSE_IMPORT" =>
                                                                                array($this->mlText[210], $this->mlText[211], PREFERENCE_TYPE_BOOLEAN, ''),
                                                                                  "ELEARNING_PRINT_SOLUTION_PAGE" =>
                                                                                  array($this->mlText[28], $this->mlText[29], PREFERENCE_TYPE_BOOLEAN, ''),
                              "ELEARNING_EXERCISE_PAGE_TAB" =>
                              array($this->mlText[234], $this->mlText[235], PREFERENCE_TYPE_SELECT, array('' => '', 'ELEARNING_PAGE_TAB_IS_NUMBER' => $this->mlText[236], 'ELEARNING_PAGE_TAB_WITH_NUMBER' => $this->mlText[237])),
                                                                                    "ELEARNING_DISPLAY_LEXICON_LIST" =>
                                                                                    array($this->mlText[17], $this->mlText[39], PREFERENCE_TYPE_BOOLEAN, ''),
                                                                                      "ELEARNING_DISPLAY_LOGO" =>
                                                                                      array($this->mlText[18], $this->mlText[35], PREFERENCE_TYPE_BOOLEAN, ''),
                                                                                        "ELEARNING_DISPLAY_COPYRIGHT" =>
                                                                                        array($this->mlText[19], $this->mlText[36], PREFERENCE_TYPE_BOOLEAN, ''),
                                                                                          "ELEARNING_DISPLAY_ADDRESS" =>
                                                                                          array($this->mlText[20], $this->mlText[37], PREFERENCE_TYPE_BOOLEAN, ''),
                                                                                            "ELEARNING_DISPLAY_AUDIO_DOWNLOAD" =>
                                                                                            array($this->mlText[98], $this->mlText[99], PREFERENCE_TYPE_BOOLEAN, ''),
                                                                                              "ELEARNING_PLAYER_AUTOSTART" =>
                                                                                              array($this->mlText[73], $this->mlText[74], PREFERENCE_TYPE_BOOLEAN, ''),
                                                                                                "ELEARNING_TEMPLATE_MODEL" =>
                                                                                                array($this->mlText[76], $this->mlText[77], PREFERENCE_TYPE_SELECT, $templateModels),
                                                                                                  "ELEARNING_TEMPLATE_MODEL_ON_PHONE" =>
                                                                                                  array($this->mlText[78], $this->mlText[79], PREFERENCE_TYPE_SELECT, $templateModels),
                                                                                                    "ELEARNING_DEFAULT_SUBSCRIPTION_DURATION" =>
                                                                                                    array($this->mlText[86], $this->mlText[96], PREFERENCE_TYPE_TEXT, ''),
                                                                                                          "ELEARNING_RESULT_GRADE_SCALE" =>
                                                                                                          array($this->mlText[184], $this->mlText[185], PREFERENCE_TYPE_SELECT, array(10 => "10", 20 => "20", 100 => "100")),
                                                                                                            "ELEARNING_AUTO_DELETE_RESULTS" =>
                                                                                                            array($this->mlText[175], $this->mlText[176], PREFERENCE_TYPE_SELECT, array('' => '', 6 => "6", 12 => "12", 24 => "24", 36 => "36", 48 => "48")),
                                                                                                              "ELEARNING_LIST_DEFAULT_EMPTY" =>
                                                                                                              array($this->mlText[102], $this->mlText[113], PREFERENCE_TYPE_BOOLEAN, ''),
                                                                                                                "ELEARNING_LIST_STEP" =>
                                                                                                                array($this->mlText[100], $this->mlText[101], PREFERENCE_TYPE_SELECT, array(10 => "10", 20 => "20", 50 => "50", 100 => "100")),
                                                                                                                  "ELEARNING_HTML_EDITOR" =>
                                                                                                                  array($this->mlText[189], $this->mlText[190], PREFERENCE_TYPE_SELECT,
                                                                                                                    array(
                                                                                                                      'HTML_EDITOR_CKEDITOR' => $this->mlText[192],
                                                                                                                    )),
                                                                                                                  "ELEARNING_EXERCISE_IMAGE_WIDTH" =>
                                                                                                                  array($this->mlText[40], $this->mlText[41], PREFERENCE_TYPE_TEXT, 300),
                                                                                                                    "ELEARNING_PHONE_EXERCISE_IMAGE_WIDTH" =>
                                                                                                                    array($this->mlText[42], $this->mlText[43], PREFERENCE_TYPE_TEXT, 140),
                                                                                                                      "ELEARNING_EXERCISE_PAGE_IMAGE_WIDTH" =>
                                                                                                                      array($this->mlText[48], $this->mlText[49], PREFERENCE_TYPE_TEXT, 200),
                                                                                                                        "ELEARNING_PHONE_EXERCISE_PAGE_IMAGE_WIDTH" =>
                                                                                                                        array($this->mlText[50], $this->mlText[51], PREFERENCE_TYPE_TEXT, 140),
                                                                                                                          "ELEARNING_QUESTION_IMAGE_WIDTH" =>
                                                                                                                          array($this->mlText[44], $this->mlText[45], PREFERENCE_TYPE_TEXT, 200),
                                                                                                                            "ELEARNING_PHONE_QUESTION_IMAGE_WIDTH" =>
                                                                                                                            array($this->mlText[46], $this->mlText[47], PREFERENCE_TYPE_TEXT, 140),
                                                                                                                              "ELEARNING_EXERCISE_INSTRUCTIONS_START" =>
                                                                                                                              array($this->mlText[24], $this->mlText[25], PREFERENCE_TYPE_MLTEXT, $this->mlText[26]),
                                                                                                                                "ELEARNING_EXERCISE_INSTRUCTIONS_END" =>
                                                                                                                                array($this->mlText[103], $this->mlText[104], PREFERENCE_TYPE_MLTEXT, $this->mlText[105]),
                                                                                                                                  "ELEARNING_EXERCISE_PAGE_INSTRUCTIONS_START" =>
                                                                                                                                  array($this->mlText[106], $this->mlText[107], PREFERENCE_TYPE_MLTEXT, $this->mlText[108]),
                                                                                                                                    "ELEARNING_EXERCISE_PAGE_INSTRUCTIONS_END" =>
                                                                                                                                    array($this->mlText[109], $this->mlText[110], PREFERENCE_TYPE_MLTEXT, $this->mlText[111]),
                                                                                                                                      "ELEARNING_CONTACT_PAGE_MESSAGE_EMAIL_WISHED" =>
                                                                                                                                      array($this->mlText[58], $this->mlText[59], PREFERENCE_TYPE_MLTEXT, $this->mlText[60]),
                                                                                                                                        "ELEARNING_EXERCISE_THANKS_IDENTIFIED" =>
                                                                                                                                        array($this->mlText[62], $this->mlText[57], PREFERENCE_TYPE_MLTEXT, $this->mlText[64]),
                                                                                                                                          "ELEARNING_EXERCISE_THANKS_UNIDENTIFIED" =>
                                                                                                                                          array($this->mlText[56], $this->mlText[57], PREFERENCE_TYPE_MLTEXT, $this->mlText[30]),
                                                                                                                                            "ELEARNING_EXERCISE_THANKS_CANNOT_IDENTIFY" =>
                                                                                                                                            array($this->mlText[179], $this->mlText[57], PREFERENCE_TYPE_MLTEXT, $this->mlText[180]),
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

  // Check if the exercise is locked for the logged in admin
  function isLockedForLoggedInAdmin($elearningExerciseId) {
    $locked = false;

    $adminLogin = $this->adminUtils->checkAdminLogin();
    if (!$this->adminUtils->isSuperAdmin($adminLogin)) {
      if ($elearningExercise = $this->selectById($elearningExerciseId)) {
        $locked = $elearningExercise->getLocked();
      }
    }

    return($locked);
  }

  // Delete an exercise
  function deleteExercise($elearningExerciseId) {
    if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByExerciseId($elearningExerciseId)) {
      foreach ($elearningCourseItems as $elearningCourseItem) {
        $elearningCourseItemId = $elearningCourseItem->getId();
        $this->elearningCourseItemUtils->delete($elearningCourseItemId);
      }
    }

    if ($elearningResults = $this->elearningResultUtils->selectByExerciseId($elearningExerciseId)) {
      foreach ($elearningResults as $elearningResult) {
        $this->elearningResultUtils->deleteQuestionsResults($elearningResult->getId());
        $elearningResult->setElearningExerciseId(0);
        $this->elearningResultUtils->update($elearningResult);
      }
    }

    if ($elearningExercisePages = $this->elearningExercisePageUtils->selectByExerciseId($elearningExerciseId)) {
      foreach ($elearningExercisePages as $elearningExercisePage) {
        $this->elearningExercisePageUtils->deleteExercisePage($elearningExercisePage->getId());
      }
    }

    if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByExerciseId($elearningExerciseId)) {
      foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
        $elearningLessonParagraph->setElearningExerciseId('');
        $this->elearningLessonParagraphUtils->update($elearningLessonParagraph);
      }
    }

    $this->delete($elearningExerciseId);
  }

  // Move an exercise into the garbage bin
  function putInGarbage($elearningExerciseId) {
    // Remove the links to the courses if any
    // An exercise, when put into the garbage, is removed from all the courses
    if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByExerciseId($elearningExerciseId)) {
      foreach ($elearningCourseItems as $elearningCourseItem) {
        $elearningCourseItemId = $elearningCourseItem->getId();
        $this->elearningCourseItemUtils->delete($elearningCourseItemId);
      }
    }

    if ($elearningExercise = $this->selectById($elearningExerciseId)) {
      $elearningExercise->setGarbage(true);

      // Free the name of the exercise when it is put into the garbage
      $randomNumber = LibUtils::generateUniqueId();
      $name = $elearningExercise->getName() . ELEARNING_GARBAGE . '_' . $randomNumber;
      $elearningExercise->setName($name);

      $this->update($elearningExercise);
    }
  }

  // Restore an exercise from the garbage
  function restoreFromGarbage($elearningExerciseId) {
    if ($elearningExercise = $this->selectById($elearningExerciseId)) {
      $elearningExercise->setGarbage(false);

      $this->update($elearningExercise);

      return(true);
    } else {
      return(false);
    }
  }

  // Get the internal links for the exercises
  // The exercises are searched using their name or their course name if any
  function getExerciseInternalLinks($searchPattern) {
    $this->loadLanguageTexts();

    $list = array();

    if ($searchPattern) {
      if ($elearningExercises = $this->selectLikePatternInExerciseAndCourse($searchPattern)) {
        foreach ($elearningExercises as $elearningExercise) {
          $elearningExerciseId = $elearningExercise->getId();
          $name = $elearningExercise->getName();
          $list['SYSTEM_PAGE_ELEARNING_EXERCISE' . $elearningExerciseId] = $this->mlText[14] . " " . $name;
        }
      }
    }

    return($list);
  }

  // Check if an exercise is available to a participant
  function isParticipantExerciseAvailable($elearningSubscriptionId, $elearningExerciseId) {
    $exerciseIsAvailable = true;

    // Do not offer the exercises already done if they are to be done only once
    if ($this->exerciseOnlyOnce($elearningSubscriptionId) && $this->elearningSubscriptionUtils->exerciseHasResults($elearningSubscriptionId, $elearningExerciseId)) {
      $exerciseIsAvailable = false;
    } else if (!$this->exerciseAnyOrder($elearningSubscriptionId)) {
      // If the subscription is for a particular course, then do not offer the exercises other than the first one that has not yet been done
      if ($elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
        if ($elearningSubscription->getCourseId()) {
          if ($elearningExerciseId != $this->elearningSubscriptionUtils->getNextExercise($elearningSubscription)) {
            $exerciseIsAvailable = false;
          }
        }
      }
    }

    return($exerciseIsAvailable);
  }

  // Duplicate an exercise
  function duplicate($elearningExerciseId, $name, $description) {
    // Duplicate the exercise
    if ($elearningExercise = $this->selectById($elearningExerciseId)) {
      $elearningExercise->setName($name);
      if ($description) {
        $elearningExercise->setDescription($description);
      }
      $this->insert($elearningExercise);
      $lastInsertElearningExerciseId = $this->getLastInsertId();

      // Duplicate the exercise pages
      $elearningExercisePages = $this->elearningExercisePageUtils->selectByExerciseId($elearningExerciseId);
      foreach ($elearningExercisePages as $elearningExercisePage) {
        $elearningExercisePageId = $elearningExercisePage->getId();
        $this->elearningExercisePageUtils->duplicate($elearningExercisePageId, $lastInsertElearningExerciseId);
      }
    }
  }

  // Search for a an exercise
  function renderSearch() {
    global $gElearningUrl;

    $this->loadLanguageTexts();

    $randomNumber = LibUtils::generateUniqueId();

    $str = "<div class='elearning_search'>";

    $str .= "<div class='elearning_search_title'>" . $this->websiteText[0] . "</div>";

    $str .= "<div class='elearning_search_field'>"
      . "<form action='$gElearningUrl/exercise/display_exercise.php' method='post'>"
      . "<input class='elearning_search_input' type='text' id='elearningExerciseName$randomNumber' value='' size='10' />"
      . "<input type='hidden' name='elearningExerciseId' id='elearningExerciseId$randomNumber' />"
      . $this->commonUtils->ajaxAutocomplete("$gElearningUrl/exercise/suggestExercises.php", "elearningExerciseName$randomNumber", "elearningExerciseId$randomNumber")
      . "</form>"
      . "</div>";

    $str .= "</div>";

    return($str);
  }

  // Render a link to an exercise
  function renderExerciseComposeLink($elearningExerciseId, $caption = '') {
    global $gElearningUrl;
    global $gJSNoStatus;
    global $gCommonImagesUrl;
    global $gImageExercise;

    $str = '';

    if ($elearningExercise = $this->selectById($elearningExerciseId)) {
      $str .= " <a href='$gElearningUrl/exercise/compose.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus title='$caption'><img border='0' src='$gCommonImagesUrl/$gImageExercise' title='$caption' style='vertical-align:middle;'> " . $elearningExercise->getName() . "</a>";
    }

    return($str);
  }

  // Render the assignments of a participant
  function renderParticipantAssignments() {
    global $gElearningUrl;
    global $gImagesUserUrl;
    global $gJSNoStatus;
    global $gCommonImagesUrl;
    global $gImagePerson;

    $this->loadLanguageTexts();

    $userId = $this->userUtils->getLoggedUserId();

    $str = '';

    $str .= "\n<div class='elearning_course'>";

    if ($userId) {
      $systemDate = $this->clockUtils->getSystemDate();

      if ($elearningSubscriptions = $this->elearningSubscriptionUtils->selectOpenedUserSubscriptions($userId, $systemDate)) {
        $str .= "\n<div class='elearning_course_page_title'>" . $this->websiteText[168] . "</div>";
        $str .= "\n<table border='0' width='100%' cellspacing='0' cellpadding='0'>";
        $str .= "<tr>"
         . "<td>"
         . "<div class='elearning_course_header'>" . $this->websiteText[166] . "</div>"
         . "</td><td>"
         . "<div class='elearning_course_header'>" . $this->websiteText[13] . "</div>"
         . "</td><td>"
         . "</td>"
         . "</tr>";
        foreach($elearningSubscriptions as $elearningSubscription) {
          $elearningSubscriptionId = $elearningSubscription->getId();
          $watchLive = $elearningSubscription->getWatchLive();
          if ($elearningAssignments = $this->elearningAssignmentUtils->selectBySubscriptionIdAndOpened($elearningSubscriptionId, $systemDate)) {

            $strDisplayGraph = ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_COURSE_GRAPH . "' class='no_style_image_icon' title='" .  $this->websiteText[186] . " 'alt='' style='vertical-align:middle;' />", "$gElearningUrl/assignment/display_graph.php?elearningSubscriptionId=$elearningSubscriptionId", 600, 600);
            $str .= "<tr>"
             . "<td>"
             . "</td><td>"
             . "</td><td>"
             . "<div class='elearning_course_icons' style='white-space:nowrap;'>"
             . "$strDisplayGraph"
             . "</div>"
             . "</td>"
             . "</tr>"
             . "<tr>"
             . "<td colspan='3'>" . $this->renderWhiteboard($elearningSubscriptionId) . "</td>"
             . "</tr>";
            foreach ($elearningAssignments as $elearningAssignment) {
              $elearningAssignmentId = $elearningAssignment->getId();
              $elearningExerciseId = $elearningAssignment->getElearningExerciseId();
              if (!($elearningAssignment->getOnlyOnce() && $this->elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $elearningExerciseId))) {
                if ($elearningExercise = $this->selectById($elearningExerciseId)) {
                  $name = $elearningExercise->getName();
                  $strExerciseName = "<a href='$gElearningUrl/assignment/do_assignment.php?elearningAssignmentId=$elearningAssignmentId' $gJSNoStatus title='" . $this->websiteText[91] . "'><img src='$gImagesUserUrl/" . IMAGE_ELEARNING_EXERCISE . "' class='no_style_image_icon' title='' alt='' style='vertical-align:middle;' /> " . $name . "</a>";
                  $strDoAssignment = "<a href='$gElearningUrl/assignment/do_assignment.php?elearningAssignmentId=$elearningAssignmentId' $gJSNoStatus title='" . $this->websiteText[91] . "'>" . "<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_EXERCISE . "' class='no_style_image_icon' title='' alt='' style='vertical-align:middle;' /></a>";
                  $strWatchLive = '';
                  if ($watchLive) {
                    $elearningTeacherId = $elearningSubscription->getTeacherId();
                    if ($elearningTeacher = $this->elearningTeacherUtils->selectById($elearningTeacherId)) {
                      $firstname = $this->elearningTeacherUtils->getFirstname($elearningTeacherId);
                      $lastname = $this->elearningTeacherUtils->getLastname($elearningTeacherId);
                      $teacherEmail = $this->elearningTeacherUtils->getEmail($elearningTeacherId);
                      $anchor = "<img border='0' src='$gCommonImagesUrl/$gImagePerson' class='no_style_image_icon' title='' alt='' style='vertical-align:middle;'>";
                      $title = $this->websiteText[120] . ' ' . $firstname . ' ' . $lastname;
                      $strImage = $this->popupUtils->getUserTipPopup($anchor, $title , 300, 200);
                      $strWatchLive = "<a href='mailto:$teacherEmail' $gJSNoStatus title=''>$strImage</a>";
                    }
                  }
                  $levelName = '';
                  $elearningLevelId = $elearningExercise->getLevelId();
                  if ($elearningLevel = $this->elearningLevelUtils->selectById($elearningLevelId)) {
                    $levelName = $elearningLevel->getName();
                  }
                  $str .= "\n<tr>";
                  $str .= "\n<td class='no_style_list_line'>";
                  $str .= "\n<div class='elearning_course_cell'>" . $strExerciseName. "</div>";
                  $str .= "</td><td class='no_style_list_line'>";
                  $str .= "\n<div class='elearning_course_cell'>" . $levelName. "</div>";
                  $str .= "</td><td class='no_style_list_line'>";
                  $str .= "\n<div class='elearning_course_icons' style='white-space:nowrap;'>";
                  $str .= $strDoAssignment . ' ' . $strWatchLive;
                  $str .= "</div>";
                  $str .= "\n</td>";
                  $str .= "\n</tr>";
                }
              }
            }
          }
        }
        $str .= "\n</table>";
      } else {
        $str .= "\n<div class='elearning_course_message'>" . $this->websiteText[138] . "</div>";
        if ($this->elearningCourseUtils->autoSubscriptions()) {
          $strMessage = $this->websiteText[240] . ' ' . "<a href='$gElearningUrl/subscription/add.php?' $gJSNoStatus title=''>" . $this->websiteText[241] . '</a> ' . $this->websiteText[242];
        } else {
          $strMessage = $this->websiteText[243] . ' ' . "<a href='$gContactUrl/post.php?' $gJSNoStatus title=''>" . $this->websiteText[244] . '</a> ' . $this->websiteText[245];
        }
        $str .= "\n<br /><div class='elearning_course_message'>$strMessage</div>";
      }
    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the course of a participant
  function renderParticipantCourse($elearningSubscription) {
    global $gElearningUrl;
    global $gImagesUserUrl;
    global $gJSNoStatus;
    global $gContactUrl;
    global $gIsPhoneClient;
    global $gCommonImagesUrl;
    global $gImagePeople;

    $this->loadLanguageTexts();

    $str = '';

    $elearningSubscriptionId = $elearningSubscription->getId();
    $elearningSessionId = $elearningSubscription->getSessionId();
    $elearningCourseId = $elearningSubscription->getCourseId();
    $sessionName = '';
    $courseName = '';
    if ($elearningSession = $this->elearningSessionUtils->selectById($elearningSessionId)) {
      $sessionName = $elearningSession->getName();
    }
    if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
      $courseName = $elearningCourse->getName();
    }
    $str .= "\n<div class='elearning_course_page_title'>";
    if ($courseName || $sessionName) {
      $strTitle = '';
      if ($courseName) {
        $strTitle .= $this->websiteText[5] . " '" . $courseName . "'";
      }
      if ($sessionName) {
        $strTitle .= ' ' . $this->websiteText[6] . " '" . $sessionName . "'";
      }
      $str .= $strTitle;
    }
    $str .= "</div>";

    $str .= $this->elearningCourseInfoUtils->render($elearningCourseId);

    $str .= <<<HEREDOC
<script type="text/javascript">
$(document).ready(function() {
  $('.lesson_exercise_toggle').click(function(){
    $('.lesson_exercise_row_'+$(this).attr('elearningLessonId')).each(function() {
      $(this).toggle();
    });
  });
});
</script>
HEREDOC;

    $str .= "\n<table border='0' width='100%' cellspacing='0' cellpadding='0'>";

    $labelGrade = $this->userUtils->getTipPopup($this->websiteText[135], $this->websiteText[133], 300, 200);
    $labelRatio = $this->userUtils->getTipPopup($this->websiteText[136], $this->websiteText[134], 300, 200);
    $labelAnswers = $this->userUtils->getTipPopup($this->websiteText[254], $this->websiteText[255], 300, 200);
    $labelPoints = $this->userUtils->getTipPopup($this->websiteText[252], $this->websiteText[253], 300, 200);

    $strDisplayGraph = ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_COURSE_GRAPH . "' class='no_style_image_icon' title='" .  $this->websiteText[186] . " 'alt='' style='vertical-align:middle;' />", "$gElearningUrl/result/display_graph.php?elearningSubscriptionId=$elearningSubscriptionId", 600, 600);

    $instantCorrection = $this->hasInstantCorrection();
    $str .= "\n<tr>";
    $str .= "\n<td class='no_style_list_line'>";
    $str .= "</td>";
    if (!$instantCorrection) {
      $str .= "<td class='no_style_list_line'>";
      $str .= "\n<div class='elearning_course_cell'>$labelGrade</div>";
      $str .= "</td><td class='no_style_list_line'>";
      $str .= "\n<div class='elearning_course_cell'>$labelRatio</div>";
      $str .= "</td><td class='no_style_list_line'>";
      $str .= "\n<div class='elearning_course_cell'>$labelAnswers</div>";
      $str .= "</td><td class='no_style_list_line'>";
      $str .= "\n<div class='elearning_course_cell'>$labelPoints</div>";
      $str .= "</td>";
    }
    $str .= "<td class='no_style_list_line'>";
    $str .= "\n<div class='elearning_course_icons'>$strDisplayGraph</div>";
    $str .= "\n</td>";
    $str .= "\n</tr>";
    $str .= "\n<tr>"
          . "<td colspan='2'>" . $this->renderWhiteboard($elearningSubscriptionId) . "</td>"
          . "</tr>";

    $elearningCourseItems = $this->elearningCourseItemUtils->selectByCourseId($elearningCourseId);

    $totalNbCorrectAnswers = 0;
    $totalNbIncorrectAnswers = 0;
    $totalNbQuestions = 0;
    $totalPoints = 0;
    foreach ($elearningCourseItems as $elearningCourseItem) {
      $elearningExerciseId = $elearningCourseItem->getElearningExerciseId();
      $elearningLessonId = $elearningCourseItem->getElearningLessonId();

      if ($elearningExercise = $this->selectById($elearningExerciseId)) {
        $elearningResultId = '';
        $points = 0;
        $grade = 0;
        $nbQuestions = 0;
        $nbIncorrectAnswers = 0;
        $nbCorrectAnswers = 0;
        $nbIncorrectAnswers = 0;
        if ($elearningResult = $this->elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $elearningExerciseId)) {
          $elearningResultId = $elearningResult->getId();
          $resultTotals = $this->elearningResultUtils->getExerciseTotals($elearningExerciseId, $elearningResultId);
          $nbQuestions = $this->elearningResultUtils->getResultNbQuestions($resultTotals);
          $nbIncorrectAnswers = $this->elearningResultUtils->getResultNbIncorrectAnswers($resultTotals);
          $nbCorrectAnswers = $this->elearningResultUtils->getResultNbCorrectAnswers($resultTotals);
          $nbIncorrectAnswers = $this->elearningResultUtils->getResultNbIncorrectAnswers($resultTotals);
          $points = $this->elearningResultUtils->getResultNbPoints($resultTotals);
          $grade = $this->elearningResultRangeUtils->calculateGrade($nbCorrectAnswers, $nbQuestions);
        }

        $name = $elearningExercise->getName();

        $exerciseIsAvailable = $this->isParticipantExerciseAvailable($elearningSubscriptionId, $elearningExerciseId);

        if ($exerciseIsAvailable) {
          if ($this->elearningSubscriptionUtils->selectByUserIdAndTeacherId($elearningSubscription->getUserId(), $elearningSubscription->getTeacherId())) {
            $strCopilot = $this->popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePeople' title='" . $this->websiteText[246] . "' style='vertical-align:middle;'>", "$gElearningUrl/subscription/copilot.php?elearningSubscriptionId=$elearningSubscriptionId&elearningExerciseId=$elearningExerciseId&lastExercisePageId=", 900, 800);
          } else {
            $strCopilot = '';
          }
          $strExerciseName = "<a href='$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus title='" . $this->websiteText[91] . "'><img src='$gImagesUserUrl/" . IMAGE_ELEARNING_EXERCISE . "' class='no_style_image_icon' title='' alt='' style='vertical-align:middle;' /> $name</a>";
          $strDoExercise = "<a href='$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus title='" . $this->websiteText[91] . "'>" . "<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_EXERCISE . "' class='no_style_image_icon' title='' alt='' style='vertical-align:middle;' /></a>";
        } else {
          $strCopilot = '';
          $strDoExercise = '';
          $strExerciseName = $name;
        }

        if (!$gIsPhoneClient && $this->elearningSubscriptionUtils->exerciseHasResults($elearningSubscriptionId, $elearningExerciseId)) {
          $strDisplayResult = "<a href='$gElearningUrl/result/display.php?elearningResultId=$elearningResultId'>"
            . "<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_RESULT . "' class='no_style_image_icon' title='" .  $this->websiteText[94] . " 'alt='' style='vertical-align:middle;' /></a>";

          $strSendResult = ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_EMAIL_FRIEND . "' class='no_style_image_icon' title='" .  $this->websiteText[90] .  "' alt='' style='vertical-align:middle;' />", "$gElearningUrl/result/send.php?elearningResultId=$elearningResultId", 600, 600);

          $strPrintResult = ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_PRINTER . "' class='no_style_image_icon' title='" .  $this->websiteText[10] . " 'alt='' style='vertical-align:middle;' />", "$gElearningUrl/result/print.php?elearningResultId=$elearningResultId", 600, 600);
        } else {
          $strDisplayResult = '';
          $strSendResult = '';
          $strPrintResult = '';
        }

        $totalNbCorrectAnswers += $nbCorrectAnswers;
        $totalNbIncorrectAnswers += $nbIncorrectAnswers;
        $totalNbQuestions += $nbQuestions;
        $totalPoints += $points;

        $str .= "\n<tr>"
          . "\n<td class='no_style_list_line'><div class='elearning_course_cell'>$strExerciseName</div>"
          . "</td>";
        if (!$instantCorrection) {
          $strResultGrades = $this->elearningResultUtils->renderResultGrades('', $grade, $nbCorrectAnswers, $nbQuestions, $points);
          $strResultRatio = $this->elearningResultUtils->renderResultRatio('', $nbCorrectAnswers, $nbQuestions);
          $strResultAnswers = $this->elearningResultUtils->renderResultAnswers('', $nbCorrectAnswers, $nbIncorrectAnswers, $nbQuestions);
          $strResultPoints = $this->elearningResultUtils->renderResultPoints('', $points);

          $str .= "<td class='no_style_list_line'>"
          . "\n<div class='elearning_course_results' style='white-space:nowrap;'>$strResultGrades</div>"
          . "</td><td class='no_style_list_line'>"
          . "\n<div class='elearning_course_points'>$strResultRatio</div>"
          . "</td><td class='no_style_list_line'>"
          . "\n<div class='elearning_course_points'>$strResultAnswers</div>"
          . "</td><td class='no_style_list_line'>"
          . "\n<div class='elearning_course_points'>$strResultPoints</div>"
          . "</td>";
        }
        $str .= "<td class='no_style_list_line'>"
          . "\n<div class='elearning_course_icons' style='white-space:nowrap;'>"
          . "$strCopilot $strDoExercise $strDisplayResult $strSendResult $strPrintResult</div></td>";
      } else if ($elearningLesson = $this->elearningLessonUtils->selectById($elearningLessonId)) {
        $lessonName = $elearningLesson->getName();
        $strLessonName = "<a href='$gElearningUrl/lesson/display_lesson.php?elearningLessonId=$elearningLessonId&elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus title='" . $this->websiteText[4] . "'><img border='0' src='$gImagesUserUrl/" . IMAGE_ELEARNING_LESSON . "' title='" . $this->websiteText[8] . "' style='vertical-align:middle;' /> $lessonName</a>";
        $strDoLesson = "<a href='$gElearningUrl/lesson/display_lesson.php?elearningLessonId=$elearningLessonId&elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus title='" . $this->websiteText[4] . "'>" . "<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_LESSON . "' class='no_style_image_icon' title='" . $this->websiteText[4] . "' alt='' style='vertical-align:middle;' /></a>";

        $elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonId($elearningLessonId);
        if ($this->elearningLessonUtils->hasExercises($elearningLessonId)) {
          $strLessonName .= " <span class='lesson_exercise_toggle' elearningLessonId='$elearningLessonId' ><img src='$gImagesUserUrl/" . IMAGE_ELEARNING_EXERCISE . "' class='no_style_image_icon' title='" . $this->websiteText[251] . "' alt='' style='vertical-align:middle;' /></span>";
        }

        $str .= "\n<tr>"
          . "\n<td class='no_style_list_line'><div class='elearning_course_cell'>$strLessonName</div>"
          . "</td>";
        if (!$instantCorrection) {
          $str .= "<td class='no_style_list_line'>"
          . "</td><td class='no_style_list_line'>"
          . "</td><td class='no_style_list_line'>"
          . "</td><td class='no_style_list_line'>"
          . "</td>";
        }
        $str .= "<td class='no_style_list_line'><div class='elearning_course_icons' style='white-space:nowrap;'>$strDoLesson</div>"
          . "\n</td>";

        if ($elearningLessonParagraphs) {
          foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
            $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();

            if ($elearningExercise = $this->selectById($elearningExerciseId)) {
              $elearningResultId = '';
              $points = 0;
              $nbQuestions = 0;
              $nbCorrectAnswers = 0;
              $nbIncorrectAnswers = 0;
              if ($elearningResult = $this->elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $elearningExerciseId)) {
                $elearningResultId = $elearningResult->getId();
                $resultTotals = $this->elearningResultUtils->getExerciseTotals($elearningExerciseId, $elearningResultId);
                $nbQuestions = $this->elearningResultUtils->getResultNbQuestions($resultTotals);
                $nbCorrectAnswers = $this->elearningResultUtils->getResultNbCorrectAnswers($resultTotals);
                $nbIncorrectAnswers = $this->elearningResultUtils->getResultNbIncorrectAnswers($resultTotals);
                $points = $this->elearningResultUtils->getResultNbPoints($resultTotals);
                $grade = $this->elearningResultRangeUtils->calculateGrade($nbCorrectAnswers, $nbQuestions);
              }

              $name = $elearningExercise->getName();

              $exerciseIsAvailable = $this->isParticipantExerciseAvailable($elearningSubscriptionId, $elearningExerciseId);

              if ($exerciseIsAvailable) {
                if ($this->elearningSubscriptionUtils->selectByUserIdAndTeacherId($elearningSubscription->getUserId(), $elearningSubscription->getTeacherId())) {
                  $strCopilot = $this->popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePeople' title='" . $this->websiteText[246] . "' style='vertical-align:middle;'>", "$gElearningUrl/subscription/copilot.php?elearningSubscriptionId=$elearningSubscriptionId&elearningExerciseId=$elearningExerciseId&lastExercisePageId=", 900, 800);
                } else {
                  $strCopilot = '';
                }
                $strExerciseName = "<a href='$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus title='" . $this->websiteText[91] . "'><img src='$gImagesUserUrl/" . IMAGE_ELEARNING_EXERCISE . "' class='no_style_image_icon' title='' alt='' style='vertical-align:middle;' /> " . $name . "</a>";
                $strDoExercise = "<a href='$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus title='" . $this->websiteText[91] . "'>" . "<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_EXERCISE . "' class='no_style_image_icon' title='' alt='' style='vertical-align:middle;' /></a>";
              } else {
                $strCopilot = '';
                $strDoExercise = '';
                $strExerciseName = $name;
              }

              if (!$gIsPhoneClient && $this->elearningSubscriptionUtils->exerciseHasResults($elearningSubscriptionId, $elearningExerciseId)) {
                $strDisplayResult = "<a href='$gElearningUrl/result/display.php?elearningResultId=$elearningResultId'>"
                  . "<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_RESULT . "' class='no_style_image_icon' title='" .  $this->websiteText[94] . " 'alt='' style='vertical-align:middle;' /></a>";

                $strSendResult = ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_EMAIL_FRIEND . "' class='no_style_image_icon' title='" .  $this->websiteText[90] .  "' alt='' style='vertical-align:middle;' />", "$gElearningUrl/result/send.php?elearningResultId=$elearningResultId", 600, 600);

                $strPrintResult = ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_PRINTER . "' class='no_style_image_icon' title='" .  $this->websiteText[10] . " 'alt='' style='vertical-align:middle;' />", "$gElearningUrl/result/print.php?elearningResultId=$elearningResultId", 600, 600);
              } else {
                $strDisplayResult = '';
                $strSendResult = '';
                $strPrintResult = '';
              }

              $totalNbCorrectAnswers += $nbCorrectAnswers;
              $totalNbIncorrectAnswers += $nbIncorrectAnswers;
              $totalNbQuestions += $nbQuestions;
              $totalPoints += $points;

              $str .= "\n<tr class='lesson_exercise_row_$elearningLessonId' style='display:none;'>"
                . "\n<td class='no_style_list_line'><div class='elearning_course_cell'>&nbsp;&nbsp;&nbsp;&nbsp;$strExerciseName</div>"
                . "</td>";
              if (!$instantCorrection) {
                $strResultGrades = $this->elearningResultUtils->renderResultGrades('', $grade, $nbCorrectAnswers, $nbQuestions, $points);
                $strResultRatio = $this->elearningResultUtils->renderResultRatio('', $nbCorrectAnswers, $nbQuestions);
                $strResultAnswers = $this->elearningResultUtils->renderResultAnswers('', $nbCorrectAnswers, $nbIncorrectAnswers, $nbQuestions);
                $strResultPoints = $this->elearningResultUtils->renderResultPoints('', $points);

                $str .= "<td class='no_style_list_line'>"
                . "\n<div class='elearning_course_results'>$strResultGrades</div>"
                . "</td><td class='no_style_list_line'>"
                . "\n<div class='elearning_course_points'>$strResultRatio</div>"
                . "</td><td class='no_style_list_line'>"
                . "\n<div class='elearning_course_points'>$strResultAnswers</div>"
                . "</td><td class='no_style_list_line'>"
                . "\n<div class='elearning_course_points'>$strResultPoints</div>"
                . "</td>";
              }
              $str .= "<td class='no_style_list_line'>"
                . "\n<div class='elearning_course_icons' style='white-space:nowrap;'>"
                . "$strCopilot $strDoExercise $strDisplayResult $strSendResult $strPrintResult</div></td>";
            }
          }
        }
      }
    }

    if ($totalNbQuestions > 0) {
      $grade = $this->elearningResultRangeUtils->calculateGrade($totalNbCorrectAnswers, $totalNbQuestions);
      $strResultGrades = $this->elearningResultUtils->renderResultGrades('', $grade, $totalNbCorrectAnswers, $totalNbQuestions, $totalPoints);
      $strResultRatio = $this->elearningResultUtils->renderResultRatio('', $totalNbCorrectAnswers, $totalNbQuestions);
      $strResultAnswers = $this->elearningResultUtils->renderResultAnswers('', $totalNbCorrectAnswers, $totalNbIncorrectAnswers, $totalNbQuestions);
      $strResultPoints = $this->elearningResultUtils->renderResultPoints('', $totalPoints);

      $strResultGrades = $this->userUtils->getTipPopup($strResultGrades, $this->websiteText[114], 300, 200);
      $strResultRatio = $this->userUtils->getTipPopup($strResultRatio, $this->websiteText[193], 300, 200);
    } else {
      $strResultGrades = '';
      $strResultRatio = '';
      $strResultAnswers = '';
      $strResultPoints = '';
    }

    $str .= "<tr>";
    $str .= "<td>";
    $str .= "</td>";
    if (!$instantCorrection) {
      $str .= "<td>";
      $str .= "<div class='elearning_course_cell' style='white-space:nowrap;'>$strResultGrades</div>";
      $str .= "</td><td>";
      $str .= "<div class='elearning_course_cell' style='white-space:nowrap;'>$strResultRatio</div>";
      $str .= "</td><td>";
      $str .= "<div class='elearning_course_cell' style='white-space:nowrap;'>$strResultAnswers</div>";
      $str .= "</td><td>";
      $str .= "<div class='elearning_course_cell' style='white-space:nowrap;'>$strResultPoints</div>";
      $str .= "</td>";
    }
    $str .= "<td style='white-space:nowrap;'>";
    $str .= "</td>";
    $str .= "</tr>";

    $str .= "\n</table>";

    return($str);
  }

  // Render the subscriptions of a participant
  function renderParticipantSubscriptions($participantUserId) {
    global $gElearningUrl;
    global $gImagesUserUrl;
    global $gJSNoStatus;
    global $gContactUrl;

    $systemDate = $this->clockUtils->getSystemDate();

    if ($participantUserId) {
      $teacherUserId = $this->userUtils->getLoggedUserId();
      if ($this->elearningSubscriptionUtils->isTeacherParticipant($participantUserId, $teacherUserId)) {
        $elearningSubscriptions = $this->elearningSubscriptionUtils->selectOpenedUserSubscriptionsWithCourse($participantUserId, $systemDate);
      }
    } else {
      $teacherUserId = '';
      $participantUserId = $this->userUtils->getLoggedUserId();

      $elearningSubscriptions = $this->elearningSubscriptionUtils->selectOpenedUserSubscriptionsWithCourse($participantUserId, $systemDate);
    }

    $this->loadLanguageTexts();

    $str = '';

    $str .= "\n<div class='elearning_course'>";

    if ($teacherUserId) {
      if ($user = $this->userUtils->selectById($participantUserId)) {
        $str .= "\n<div class='elearning_course_page_title'>" . $this->websiteText[169]
          . ' ' . $user->getFirstname() . ' ' . $user->getLastname()
          . "</div>";
      }
    } else {
      $str .= "\n<div class='elearning_course_page_title'>" . $this->websiteText[75] . "</div>";
    }

    $str .= "\n<div class='elearning_course_icons'>";

    if ($this->elearningCourseUtils->autoSubscriptions()) {
      $str .= "\n<a href='$gElearningUrl/subscription/add.php' $gJSNoStatus title='" . $this->websiteText[115] . "'><img src='$gImagesUserUrl/" . IMAGE_COMMON_ADD . "' class='no_style_image_icon' title='" .  $this->websiteText[115] .  "' alt='' style='vertical-align:middle;' />" . "</a>";
    }

    $str .= "</div>";

    if ($elearningSubscriptions && count($elearningSubscriptions) > 0) {
      $str .= "\n<table border='0' width='100%' cellspacing='0' cellpadding='0'>";
      $labelSession = $this->userUtils->getTipPopup($this->websiteText[153], $this->websiteText[156], 300, 200);
      $labelCourse = $this->userUtils->getTipPopup($this->websiteText[157], $this->websiteText[158], 300, 200);
      $labelClass = $this->userUtils->getTipPopup($this->websiteText[160], $this->websiteText[161], 300, 200);
      $labelTeacher = $this->userUtils->getTipPopup($this->websiteText[162], $this->websiteText[165], 300, 200);
      $str .= "\n<tr>";
      $str .= "\n<td>";
      $str .= "\n<div class='elearning_course_header'>$labelCourse</div>";
      $str .= "</td><td>";
      $str .= "\n<div class='elearning_course_header'>$labelSession</div>";
      $str .= "</td><td>";
      $str .= "\n<div class='elearning_course_header'>$labelClass</div>";
      $str .= "</td><td>";
      $str .= "\n<div class='elearning_course_header'>$labelTeacher</div>";
      $str .= "\n</td>";
      $str .= "\n</tr>";
      foreach($elearningSubscriptions as $elearningSubscription) {
        $elearningSubscriptionId = $elearningSubscription->getId();
        $subscriptionDate = $elearningSubscription->getSubscriptionDate();
        $subscriptionDate = $this->clockUtils->systemToLocalNumericDate($subscriptionDate);
        if ($elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
          $strSession = '';
          $elearningSessionId = $elearningSubscription->getSessionId();
          if ($elearningSession = $this->elearningSessionUtils->selectById($elearningSessionId)) {
            $sessionName = $elearningSession->getName();
            $openDate = $elearningSession->getOpenDate();
            $closeDate = $elearningSession->getCloseDate();
            $strOpenDate = $this->clockUtils->systemToLocalNumericDate($openDate);
            if ($this->clockUtils->systemDateIsSet($closeDate)) {
              $strCloseDate = $this->clockUtils->systemToLocalNumericDate($closeDate);
            } else {
              $strCloseDate = '';
            }
            $sessionDate = $this->websiteText[71] . ' ' . $strOpenDate;
            if ($closeDate) {
              $sessionDate .= ' ' . $this->websiteText[72] . ' ' . $strCloseDate;
            }
            $strSession = "<div title='$sessionDate'>$sessionName</div>";
          }
          $strCourse = '';
          $elearningCourseId = $elearningSubscription->getCourseId();
          if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
            $strCourse = "<a href='$gElearningUrl/subscription/display_participant_course.php?elearningSubscriptionId=$elearningSubscriptionId' style='text-decoration:none; vertical-align:middle;' title='" . $this->websiteText[12] . "' ><img src='$gImagesUserUrl/" . IMAGE_ELEARNING_COURSE . "' class='no_style_image_icon' title='' style='vertical-align:middle;' /> " . $elearningCourse->getName() . "</a>";
          }
          $strClass = '';
          $elearningClassId = $elearningSubscription->getClassId();
          if ($elearningClass = $this->elearningClassUtils->selectById($elearningClassId)) {
            $description = $elearningClass->getDescription();
            $strClass = "<span title='$description'>" . $elearningClass->getName() . '</span>';
          }
          $strTeacher = '';
          $elearningTeacherId = $elearningSubscription->getTeacherId();
          if ($elearningTeacher = $this->elearningTeacherUtils->selectById($elearningTeacherId)) {
            $userId = $elearningTeacher->getUserId();
            if ($user = $this->userUtils->selectById($userId)) {
              $strTeacher = $user->getFirstname() . ' ' . $user->getLastname();
            }
          }
          $strCommands = "<a href='$gElearningUrl/subscription/unsubscribe.php?elearningSubscriptionId=$elearningSubscriptionId' style='text-decoration:none; vertical-align:middle;' title='" . $this->websiteText[167] . "' ><img src='$gImagesUserUrl/" . IMAGE_COMMON_DELETE . "' class='no_style_image_icon' title='' style='vertical-align:middle;' /></a>";
          $str .= "\n<tr>"
            . "<td class='no_style_list_line'>"
            . "$strCourse"
            . "</td><td class='no_style_list_line'>"
            . $strSession
            . "</td><td class='no_style_list_line'>"
            . "$strClass"
            . "</td><td class='no_style_list_line'>"
            . "$strTeacher</td>"
            . "</td><td class='no_style_list_line'>"
            . "$strCommands</td>";
        }
      }
      $str .= "\n</table>";
    } else {
      $str .= "\n<div class='elearning_course_message'>" . $this->websiteText[138] . "</div>";
      if ($this->elearningCourseUtils->autoSubscriptions()) {
        $strMessage = $this->websiteText[240] . ' ' . "<a href='$gElearningUrl/subscription/add.php?' $gJSNoStatus title=''>" . $this->websiteText[241] . '</a> ' . $this->websiteText[242];
      } else {
        $strMessage = $this->websiteText[243] . ' ' . "<a href='$gContactUrl/post.php?' $gJSNoStatus title=''>" . $this->websiteText[244] . '</a> ' . $this->websiteText[245];
      }
      $str .= "\n<br /><div class='elearning_course_message'>$strMessage</div>";
    }

    $str .= "\n</div>";

    return($str);
  }

  function partOfOnlyOneCourse($elearningExerciseId) {
    if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByExerciseId($elearningExerciseId)) {
      if (count($elearningCourseItems) == 1) {
        return(true);
      }
    }

    return(false);
  }

  // Check if the content was created by the user
  function createdByUser($elearningExerciseId, $userId) {
    if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByExerciseId($elearningExerciseId)) { 
      foreach ($elearningCourseItems as $elearningCourseItem) {
        $elearningCourseId = $elearningCourseItem->getElearningCourseId();
        if ($this->elearningCourseUtils->createdByUser($elearningCourseId, $userId)) {
          return(true);
        }
      }
    }

    return(false);
  }

  // Check if an exercise has a scoring page
  function exerciseHasScoring($elearningExerciseId) {
    $hasScoring = false;

    if ($elearningExercise = $this->selectById($elearningExerciseId)) {
      $scoringId = $elearningExercise->getScoringId();
      if ($elearningScoring = $this->elearningScoringUtils->selectById($scoringId)) {
        $hasScoring = true;
      }
    }

    return($hasScoring);
  }

  // Get the template model, if any, in which to render the courses, lessons and exercises
  function getTemplateModel() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $templateModelId = $this->preferenceUtils->getValue("ELEARNING_TEMPLATE_MODEL_ON_PHONE");
    } else {
      $templateModelId = $this->preferenceUtils->getValue("ELEARNING_TEMPLATE_MODEL");
    }

    return($templateModelId);
  }

  // Render the download link
  function renderDownload($audio) {
    global $gDataUrl;
    global $gDataPath;

    $str = '';

    if ($audio) {
      if (is_file($gDataPath . "elearning/exercise/audio/$audio")) {
        $str = $this->playerUtils->renderDownload($gDataPath . "elearning/exercise/audio/$audio");
      }
    }

    return($str);
  }

  // Render the player
  function renderPlayer($audio, $autostart) {
    global $gDataUrl;
    global $gDataPath;

    $str = '';

    if ($audio) {
      $str .= "<div class='elearning_exercise_player'>";

      if (!$autostart) {
        $autostart = $this->autoStartAudioPlayer();
      }

      $this->playerUtils->setAutostart($autostart);

      if (is_file($gDataPath . "elearning/exercise/audio/$audio")) {
        if ($this->displayDownloadAudioFileIcon()) {
          $str .= $this->renderDownload($audio) . ' ';
        }
        $str .= $this->playerUtils->renderPlayer("$gDataUrl/elearning/exercise/audio/$audio");
      }

      $str .= "</div>";
    }

    return($str);
  }

  // Render the copyright notice
  function renderCopyright() {
    $str ='';

    $copyright = $this->profileUtils->getWebSiteCopyright();

    if ($copyright) {
      $str = "<div class='elearning_exercise_copyright'>$copyright</div>";
    }

    return($str);
  }

  // Render the website address
  function renderAddress() {

    $str ='';

    $address = $this->profileUtils->getWebSiteAddress();

    if ($address) {
      $str = "<div class='elearning_exercise_address'>$address</div>";
    }

    return($str);
  }

  // Print the exercise
  function printExerciseIntroduction($elearningExerciseId) {
    $str = LibJavaScript::getJSLib();
    $str .= "\n<script type='text/javascript'>printPage();</script>";

    $str .= $this->renderIntroductionForPrint($elearningExerciseId);

    return($str);
  }

  // Print the exercise
  function printExercise($elearningExerciseId) {
    $str = LibJavaScript::getJSLib();
    $str .= "\n<script type='text/javascript'>printPage();</script>";

    $str .= $this->renderExerciseForPrint($elearningExerciseId);

    return($str);
  }

  // Render the description
  function renderDescription($description) {
    $description = nl2br($description);

    $str = "\n<div class='elearning_exercise_description'>$description</div>";

    return($str);
  }

  // Render the exercise introduction in a printer friendly format
  function renderIntroductionForPrint($elearningExerciseId) {
    if (!$elearningExercise = $this->selectById($elearningExerciseId)) {
      return;
    }

    $name = $elearningExercise->getName();
    $description = $elearningExercise->getDescription();
    $introduction = $elearningExercise->getIntroduction();

    $str = "\n<div class='elearning_exercise'>";

    // Render the logo
    $logo = $this->profileUtils->getLogoFilename();
    if ($logo && is_file($this->profileUtils->filePath . $logo) && $this->displayWebsiteLogo()) {
      $str .= "<div><img src='$this->profileUtils->fileUrl/$logo' title='' alt='' style='vertical-align:middle;' /></div>";
    }

    $str .= "\n<div class='elearning_exercise_name'>$name</div>";

    $str .= $this->renderDescription($description);

    $str .= $this->renderImage($elearningExercise->getImage());

    $str .= "\n<div class='elearning_exercise_introduction'>$introduction</div>";

    if ($this->displayLexiconList()) {
      $str .= $this->lexiconEntryUtils->renderLexiconTooltipsForPrintFromContent($introduction);
    }

    if ($this->displayCopyright()) {
      $str .= $this->renderCopyright();
    }

    if ($this->displayWebsiteAddress()) {
      $str .= $this->renderAddress();
    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the whole exercise in a printer friendly format
  function renderExerciseForPrint($elearningExerciseId) {
    if (!$elearningExercise = $this->selectById($elearningExerciseId)) {
      return;
    }

    $name = $elearningExercise->getName();
    $description = $elearningExercise->getDescription();
    $introduction = $elearningExercise->getIntroduction();

    $str = "\n<div class='elearning_exercise'>";

    $logo = $this->profileUtils->getLogoFilename();
    if ($logo && is_file($this->profileUtils->filePath . $logo) && $this->displayWebsiteLogo()) {
      $str .= "<div><img src='$this->profileUtils->fileUrl/$logo' title='' alt='' style='vertical-align:middle;' /></div>";
    }

    $str .= "\n<div class='elearning_exercise_name'>$name</div>";

    $str .= $this->renderDescription($description);

    $str .= $this->renderImage($elearningExercise->getImage());

    if ($introduction) {
      $str .= "\n<div class='elearning_exercise_introduction'>$introduction</div>";
    }

    if ($this->displayLexiconList()) {
      $str .= $this->lexiconEntryUtils->renderLexiconTooltipsForPrintFromContent($introduction);
    }

    if ($introduction) {
      $str .= '<br /><br /><br /><br />';
    }

    $elearningExercisePages = $this->elearningExercisePageUtils->selectByExerciseId($elearningExerciseId);
    foreach ($elearningExercisePages as $elearningExercisePage) {
      $str .= $this->elearningExercisePageUtils->renderForPrint($elearningExercise, $elearningExercisePage, false);
    }

    if ($this->printSolutionsOnSeparatePage($elearningExerciseId)) {
      $str .= "\n<p style='page-break-before:always;'></p>";

      foreach ($elearningExercisePages as $elearningExercisePage) {
        $str .= $this->elearningExercisePageUtils->renderSolutionsPageForPrint($elearningExercisePage);
      }
    }

    if ($this->displayCopyright()) {
      $str .= $this->renderCopyright();
    }

    if ($this->displayWebsiteAddress()) {
      $str .= $this->renderAddress();
    }

    $str .= "\n</div>";

    return($str);
  }

  // Get the time left to do the exercise expressed in seconds
  function getSecondsLeft($exerciseCurrentTime, $exerciseStartTime, $maxDuration) {
    if ($exerciseCurrentTime < $exerciseStartTime) {
      $exerciseCurrentTime = $exerciseStartTime;
    }

    $seconds = ($maxDuration * 60) - ($exerciseCurrentTime - $exerciseStartTime);

    if ($seconds < 0) {
      $seconds = 0;
    }

    return($seconds);
  }

  // Get the remaining time left to a participant in mn and seconds
  function getTimeLeft($exerciseCurrentTime, $exerciseStartTime, $maxDuration) {
    $secondsLeft = $this->getSecondsLeft($exerciseCurrentTime, $exerciseStartTime, $maxDuration);

    $minutes = floor($secondsLeft / 60);

    $seconds = $secondsLeft - ($minutes * 60);

    return(array($minutes, $seconds));
  }

  // Render the remaining time left to a participant doing an exercise
  function renderTimeLeft($elearningExerciseId, $exerciseCurrentTime, $exerciseStartTime, $maxDuration) {
    list($minutes, $seconds) = $this->getTimeLeft($exerciseCurrentTime, $exerciseStartTime, $maxDuration);

    if (strlen($seconds) < 2) {
      $seconds = '0' . $seconds;
    }

    $str = "<span class='elearning_exercise_time_minsec'><span id='countdownMn'>" . $minutes . "</span></span>mn<span class='elearning_exercise_time_minsec'><span id='countdownSec'>" . $seconds . "</span></span>s.";

    $str .= $this->checkForTimeOut($elearningExerciseId, $exerciseCurrentTime, $exerciseStartTime, $maxDuration);

    return($str);
  }

  // Check if the exercise has been abandoned
  // This is to avoid an exercise been deemed timed out when in fact it was abandoned long ago
  // Otherwise, being timed out when doing anew an exercise, could be confusing to the participant
  function hadBeenAbandoned($exerciseCurrentTime, $exerciseStartTime, $maxDuration) {
    $elapsedSeconds = $exerciseCurrentTime - $exerciseStartTime;

    if ($elapsedSeconds > ($maxDuration + $this->getAbandonedAfterDuration()) * 60) {
      return(true);
    }

    return(false);
  }

  // Check if the exercise has timed out
  function checkForTimeOut($elearningExerciseId, $exerciseCurrentTime, $exerciseStartTime, $maxDuration) {
    global $gElearningUrl;

    $abandoned = $this->hadBeenAbandoned($exerciseCurrentTime, $exerciseStartTime, $maxDuration);
    if ($abandoned) {
      $exerciseStartTime = $this->resetStartTime($elearningExerciseId);
    }

    list($minutes, $seconds) = $this->getTimeLeft($exerciseCurrentTime, $exerciseStartTime, $maxDuration);

    $str = "<input type='hidden' name='exerciseTimedOut' value='' />";

    if ($this->interruptTimedOutExercise()) {
      $str .= <<<HEREDOC
<script type='text/javascript'>
  var timeOutFn = function() {
    document.exercise_form.action = '$gElearningUrl/exercise/last_exercise_page_controller.php';
    document.exercise_form.exerciseTimedOut.value = 1;
    document.exercise_form.submit();
  }
</script>
HEREDOC;
    } else {
      $str .= <<<HEREDOC
<script type='text/javascript'>
  var timeOutFn = function() {
  }
</script>
HEREDOC;
    }

    $str .= <<<HEREDOC
<script type='text/javascript'>
  updateCountdownTimer($minutes, 'countdownMn', $seconds, 'countdownSec', timeOutFn);
</script>
HEREDOC;

    return($str);
  }

  // Get the elapsed time in seconds
  function retrieveElapsedSeconds($elearningExerciseId) {
    $exerciseStartTime = LibSession::getSessionValue(ELEARNING_SESSION_EXERCISE_START_TIME . $elearningExerciseId);

    // Prevent the loss of the exercise start time if the http session expires
    if (!$exerciseStartTime) {
      $exerciseStartTime = LibCookie::getCookie(ELEARNING_SESSION_EXERCISE_START_TIME . $elearningExerciseId);
    }

    $exerciseEndTime = LibSession::getSessionValue(ELEARNING_SESSION_EXERCISE_END_TIME . $elearningExerciseId);

    if ($exerciseStartTime > 0 && $exerciseEndTime > $exerciseStartTime) {
      $elapsedSeconds = $exerciseEndTime - $exerciseStartTime;
    } else {
      $elapsedSeconds = 0;
    }

    return($elapsedSeconds);
  }

  // Render the elapsed time by a participant doing an exercise
  function renderElapsedTime($exerciseCurrentTime, $exerciseStartTime, $maxDuration) {
    if ($exerciseCurrentTime < $exerciseStartTime) {
      $exerciseCurrentTime = $exerciseStartTime;
    }

    $elapsedSeconds = $exerciseCurrentTime - $exerciseStartTime;
    if ($elapsedSeconds < 0) {
      $elapsedSeconds = 0;
    }

    // The elapsed time cannot be greater than the maximum duration if any
    if ($maxDuration && $elapsedSeconds > ($maxDuration * 60)) {
      $elapsedSeconds = $maxDuration * 60;
    }

    $minutes = floor($elapsedSeconds / 60);

    $seconds = $elapsedSeconds - ($minutes * 60);

    if (strlen($seconds) < 2) {
      $seconds = '0' . $seconds;
    }

    $str = "<span style='white-space: nowrap;'><span class='elearning_exercise_time_minsec'>" . $minutes . "</span>mn<span class='elearning_exercise_time_minsec'> " . $seconds . "</span>s</span>";

    return($str);
  }

  // Check if the audio files should be played automatically
  // The audio player can start playing automatically. The audio file is then played automatically without the need for the participant to press the 'Play' button. If the audio file has just been played, then the player does not start automatically any longer.
  function autoStartAudioPlayer() {
    $autoStartAudioPlayer = $this->preferenceUtils->getValue("ELEARNING_PLAYER_AUTOSTART");

    return($autoStartAudioPlayer);
  }

  // Check if the website address should be displayed
  function displayWebsiteAddress() {
    $displayWebsiteAddress = $this->preferenceUtils->getValue("ELEARNING_DISPLAY_ADDRESS");

    return($displayWebsiteAddress);
  }

  // Check if the logo of the website should be displayed
  function displayWebsiteLogo() {
    $displayWebsiteLogo = $this->preferenceUtils->getValue("ELEARNING_DISPLAY_LOGO");

    return($displayWebsiteLogo);
  }

  // Check if the website copyright should be displayed
  function displayCopyright() {
    $display = $this->preferenceUtils->getValue("ELEARNING_DISPLAY_COPYRIGHT");

    return($display);
  }

  // By default a multiple choices question can offer only one possible correct answer. But it is possible to have for each multiple choices question several possible correct answers.
  function acceptMultipleAnswers() {
    $acceptMultipleAnswers = $this->preferenceUtils->getValue("ELEARNING_MULTIPLE_ANSWERS");

    return($acceptMultipleAnswers);
  }

  // By default, when doing an exercise, the correction is only displayed in the results page, at the end of the exercise.\n\nBut it is possible to display an instant correction.\n\nIn that case, a message is displayed immediately when a question is given a wrong answer.
  function hasInstantCorrection() {
    $instantCorrection = $this->preferenceUtils->getValue("ELEARNING_INSTANT_CORRECTION");

    return($instantCorrection);
  }

  // By default, the graph of results displays the results of all the exercises.\n\nBut it is possible to display a graph composed only of exercises that do not show instant corrections (nor congratulations).\n\nIt is also possible to show a graph composed only of exercises that show instant corrections (or congratulations).
  function getGraphFilter() {
      return($this->preferenceUtils->getValue("ELEARNING_GRAPH_FILTER"));
  }

  // If a text contains some with lexicon words, then it is possible to display the lexicon explanations in a list under the text.
  function displayLexiconList() {
    $lexiconList = $this->preferenceUtils->getValue("ELEARNING_DISPLAY_LEXICON_LIST");

    return($lexiconList);
  }

  // Check if the selected html editor is the CKEditor
  function useHtmlEditorCKEditor() {
    $result = false;

    $htmlEditor = $this->preferenceUtils->getValue("ELEARNING_HTML_EDITOR");

    if ($htmlEditor == 'HTML_EDITOR_CKEDITOR') {
      $result = true;
    }

    return($result);
  }

  // Check if the results must be hidden if no email has been provided by the user.
  // By default, when a participant has done an exercise and has not left any email address, the results of the exercise are still displayed to him. But the results can be hidden and will not be displayed to the participant if no email address has been provided. This is to entice unknow participants to leave an email address.
  function hideResultsIfNoEmail() {
    $hideResults = $this->preferenceUtils->getValue("ELEARNING_HIDE_RESULT_IF_NO_EMAIL");

    return($hideResults);
  }

  // Check if a contact page should be displayed before displaying the exercise results
  // When an unidentified participant does an exercise a contact page can be displayed before displaying the exercise results. This is to offer a chance to the participant to leave an email address so as to identify himself. This is especially interesting if the results are not displayed to unidentified participants. The results of the exercise are then displayed after the participant has left his email address. Note that the participant can also leave a message to a teacher. By providing an email address the exercise results are also saved in the system. Note that a participant that is already identified with a login name and password does not need to leave anything to see his exercise results.
  function displayContactPageBeforeResults($elearningExerciseId) {
    $contactPage = $this->preferenceUtils->getValue("ELEARNING_CONTACT_PAGE");

    if (!$contactPage) {
      if ($elearningExercise = $this->selectById($elearningExerciseId)) {
        $contactPage = $elearningExercise->getContactPage();
      }
    }

    return($contactPage);
  }

  // When an unidentified participant does an exercise, if the contact page has not yet been displayed, then a button redirecting to the contact page is displayed at the bottom of the exercise results page. But it is possible not to display this button.
  function displayButtonToContactPage() {
    $display = $this->preferenceUtils->getValue("ELEARNING_DISPLAY_CONTACT_PAGE_BUTTON");

    return($display);
  }

  // Duration after which a participant who does not answer a question is considered as being inactive
  function getInactiveDuration() {
    $duration = $this->preferenceUtils->getValue("ELEARNING_INACTIVE_DURATION") * 60;

    return($duration);
  }

  // Duration after which a participant who does not answer a question is considered as being absent
  function getAbsentDuration() {
    $duration = $this->preferenceUtils->getValue("ELEARNING_ABSENT_DURATION") * 60 * 15;

    return($duration);
  }

  // By default the school will receive the results of the exercise and a message from the participant if any. But it is possible not to contact the school and send it the exercise results if the participant has not written any message. In that case, the exercise results will be saved but the school will not receive any email with the exercise results.
  function contactSchoolOnMessageOnly() {
    $contact = $this->preferenceUtils->getValue("ELEARNING_CONTACT_ON_MESSAGE");

    return($contact);
  }

  // A thanks message is displayed at the end of the exercise to thank the participant for his collaboration. There are three different thanks messages. One is displayed if the participant is identified. Another one is displayed if he could identify himself but did not. And yet another one is displayed if he had no way to identify himself.
  function getContactThanksAsUnidentified() {
    $thanks = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_THANKS_UNIDENTIFIED");

    return($thanks);
  }

  function getContactThanksAsIdentified() {
    $thanks = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_THANKS_IDENTIFIED");

    return($thanks);
  }

  function getContactThanksAsCannotIdentify() {
    $thanks = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_THANKS_CANNOT_IDENTIFY");

    return($thanks);
  }

  // A time out message is displayed if the participant has exceeded the maximum time allowed to do the exercise.
  function getTimeOutMessage() {
    $value = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_TIMEOUT");

    return($value);
  }

  // The duration after which an exercise is deemed as having been abandoned by the participant
  function getAbandonedAfterDuration() {
    $value = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_ABANDONED");

    return($value);
  }

  // By default, if a question's solution contains an explanation, then the explanation is displayed in the detailled correction but not in the page of exercise's results. But it is possible to display the explanation in the page of exercise's results as well.
  function displayExplanation() {
    $displayExplanation = $this->preferenceUtils->getValue("ELEARNING_DISPLAY_EXPLANATION");

    return($displayExplanation);
  }

  // By default, the maximum time allowed to do an exercise, if any, and the time left to do the exercise, are displayed at the top of each page of questions. But it is possible to hide these times.
  function hideExerciseTimes() {
    $hideExerciseTimes = $this->preferenceUtils->getValue("ELEARNING_HIDE_TIMES");

    return($hideExerciseTimes);
  }

  // A participant can leave his email address at the end of an exercise. This email address is then displayed in the exercise results so as to be able to contact the participant. But this email address can also be added to the list of email addresses used in the mailings. This allows the list of email addresses used in the mailings, to grow with the email addresses obtained during the exercises done by the participants.
  function registerEmailAddress() {
    $registerEmailAddress = $this->preferenceUtils->getValue("ELEARNING_REGISTER_EMAIL");

    return($registerEmailAddress);
  }

  // Check if the exercise results should be automatically sent to the participant.
  // When a participant has done the exercise and has left his email address, the results of the exercise can be automatically sent to him by email.
  function sendExerciseResultToParticipant() {
    $send = $this->preferenceUtils->getValue("ELEARNING_SEND_RESULT");

    return($send);
  }

  // A message can be displayed at the top of the contact page. This can be used to customize the contact page.
  function getHeaderMessageInContactPage() {
    $message = $this->preferenceUtils->getValue("ELEARNING_CONTACT_PAGE_MESSAGE_EMAIL_WISHED");

    return($message);
  }

  // In the contact page, the participant can leave his email address in order to have the exercise results registered in the platform and be contacted later on. It is possible to require the email address. In that case, additional input fields are displayed alongside the email input field, to ask the participant to also type in his firstname and lastname.
  function contactPageInfoRequired() {
    $additionalInfo = $this->preferenceUtils->getValue("ELEARNING_CONTACT_PAGE_FIELD_REQUIRED");

    return($additionalInfo);
  }

  // By default, the courses cannot be imported by other websites. But it is possible to offer all the courses for export. In that case, a website that has the authorisation to import content will be able to import all the courses.
  function coursesCanBeImported() {
    $coursesCanBeImported = $this->preferenceUtils->getValue("ELEARNING_COURSE_IMPORT");

    return($coursesCanBeImported);
  }

  // Check if a user login is required to access the exercise
  function requireUserLogin($elearningExercise, $elearningSubscription) {
    $secured = $this->preferenceUtils->getValue("ELEARNING_SECURED");

    if (!$secured) {
      if ($elearningSubscription) {
        $elearningCourseId = $elearningSubscription->getCourseId();
        if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
          $secured = $elearningCourse->getSecured();
        }
      }
    }

    if (!$secured) {
      $secured = $elearningExercise->getSecured();
    }

    return($secured);
  }

  // Check if the text of the exercise introduction is to be hidden
  // and replaced by a button to display it
  function hideIntroduction($elearningExerciseId) {
    $hide = false;

    if ($elearningExercise = $this->selectById($elearningExerciseId)) {
      $hide = $elearningExercise->getHideIntroduction();
    }

    return($hide);
  }

  // Check if the introduction to the exercise is to be skipped
  // and the first page of questions displayed directly instead
  function skipExerciseIntroduction($elearningExerciseId) {
    $skipExerciseIntroduction = false;

    if ($elearningExercise = $this->selectById($elearningExerciseId)) {
      $skipExerciseIntroduction = $elearningExercise->getSkipExerciseIntroduction();
    }

    return($skipExerciseIntroduction);
  }

  // Get the maximum duration for the exercise, if any
  function getMaximumDuration($elearningExerciseId, $elearningSubscriptionId = '') {
    $maxDuration = 0;

    if ($elearningExercise = $this->selectById($elearningExerciseId)) {
      $maxDuration = $elearningExercise->getMaxDuration();
    }

    return($maxDuration);
  }

  // Check if an exercise that has timed out must be interrupted
  function interruptTimedOutExercise($elearningSubscriptionId = '') {
    $interrupt = false;

    if ($elearningSubscriptionId) {
      if ($elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
        $elearningCourseId = $elearningSubscription->getCourseId();
        if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
          $interrupt = $elearningCourse->getInterruptTimedOutExercise();
        }
      }
    }

    if (!$interrupt) {
      $interrupt = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_INTERRUPT");
    }

    return($interrupt);
  }

  // By default, during an exercise, a horizontal graphical bar is displayed at the top of each page of questions to show the progression of the exercise. It indicates how many pages of questions have been done and how many are left to do. It is possible to hide that graphical bar.
  function hideProgressionBar($elearningExercise) {
    $hide = $elearningExercise->getHideProgressionBar();

    return($hide);
  }

  // When printing the exercise, by default the answers to the questions are printed below their questions. But it is possible to print the answers on a separate page, so as to print the text and the questions on a page without the answers.
  function printSolutionsOnSeparatePage($elearningExerciseId) {
    $separatePage = $this->preferenceUtils->getValue("ELEARNING_PRINT_SOLUTION_PAGE");

    return($separatePage);
  }

  // By default, when displaying a lesson or an exercise, the level is displayed.\n\nBut it is possible not to display it.
  function hideLevel() {
    $hide = $this->preferenceUtils->getValue("ELEARNING_HIDE_LEVEL");

    return($hide);
  }

  // By default, when an exercise has been done, the participant answers are kept in the questions, so that when the participant does the exercise again, the answers previously given appear in the questions. But it is possible to reset the participant answers when the exercise results have been displayed. In that case, when the participant does the exercise again, the answers previously given will not appear in the questions.
  function checkResetExerciseAnswers($elearningSubscriptionId = '') {
    $resetExerciseAnswers = false;

    if ($elearningSubscriptionId) {
      if ($elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
        $elearningCourseId = $elearningSubscription->getCourseId();
        if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
          $resetExerciseAnswers = $elearningCourse->getResetExerciseAnswers();
        }
      }
    }

    if (!$resetExerciseAnswers) {
      $resetExerciseAnswers = $this->preferenceUtils->getValue("ELEARNING_RESET_ANSWERS");
    }

    return($resetExerciseAnswers);
  }

  // An exercise can have several pages of questions, each displayed on a separate page. By default, a bar of tabs to navigate between the pages of the exercise is displayed on top of each page. Each tab is the name of a page of questions.
  function hidePageTabs($elearningExercise) {
    $hide = $elearningExercise->getHidePageTabs();

    return($hide);
  }

  // By default, all the tabs are enabled. But it is possible to disable the tabs following the current tab It is then impossible to use the tabs to go to the next pages of the exercise. The exercise must then be done in the sequential order of the pages of questions.
  function disableNextPageTabs($elearningExercise) {
    $disable = $elearningExercise->getDisableNextPageTabs();

    return($disable);
  }

  // By default, the exercise tabs are displayed using the names of the pages of questions. But it is possible to display the tabs as page numbers.
  function pageTabsIsNumbers($elearningExercise) {
    if ($elearningExercise->getNumberPageTabs() == ELEARNING_PAGE_TAB_IS_NUMBER) {
      return(true);
    } else if ($this->preferenceUtils->getValue("ELEARNING_EXERCISE_PAGE_TAB") == 'ELEARNING_PAGE_TAB_IS_NUMBER') {
      return(true);
    } else {
      return(false);
    }
  }

  // By default, the exercise tabs are displayed using the names of the pages of questions. But it is possible to display the tabs with page numbers as prefix to their name.
  function pageTabsWithNumbers($elearningExercise) {
    if ($elearningExercise->getNumberPageTabs() == ELEARNING_PAGE_TAB_WITH_NUMBER) {
      return(true);
    } else if ($this->preferenceUtils->getValue("ELEARNING_EXERCISE_PAGE_TAB") == 'ELEARNING_PAGE_TAB_WITH_NUMBER') {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if an exercise can be done only once
  // By default, an exercise can be done several times. This allows the participant to practice. However, it is possible to prevent a participant from doing an exercise that already has some results
  function exerciseOnlyOnce($elearningSubscriptionId) {
    $once = false;

    if ($elearningSubscriptionId) {
      if ($elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
        $elearningCourseId = $elearningSubscription->getCourseId();
        if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
          $once = $elearningCourse->getExerciseOnlyOnce();
        }
      }
    }

    return($once);
  }

  // Allow in any order
  // By default, a participant can only do the next exercise of the course, that is, the first exercise of the course that has not yet been done. However, it is possible to allow the participant to do the exercisex of the course in any order. nThe participant will then be able to do the exercises of the course in the order of his choice
  function exerciseAnyOrder($elearningSubscriptionId) {
    $anyOrder = false;

    if ($elearningSubscriptionId) {
      if ($elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
        $elearningCourseId = $elearningSubscription->getCourseId();
        if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
          $anyOrder = $elearningCourse->getExerciseAnyOrder();
        }
      }
    }

    return($anyOrder);
  }

  // Check when to save the exercise results
  function getSaveResultOption($elearningSubscriptionId = '') {
    $saveResult = '';

    if ($elearningSubscriptionId) {
      if ($elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
        $elearningCourseId = $elearningSubscription->getCourseId();
        if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
          $saveResult = $elearningCourse->getSaveResult();
        }
      }
    }

    if (!$saveResult) {
      $saveResult = $this->preferenceUtils->getValue("ELEARNING_SAVE_RESULT");
    }

    return($saveResult);
  }

  // Check if the exercise results are to be saved when watched live by a teacher
  function saveResultIfWatchedLive($elearningSubscriptionId, $elearningExerciseId) {
    $saveResult = false;

    if ($elearningAssignment = $this->elearningAssignmentUtils->selectBySubscriptionIdAndExerciseId($elearningSubscriptionId, $elearningExerciseId)) {
      $saveResult = true;
    } else if ($this->preferenceUtils->getValue("ELEARNING_SAVE_RESULT_WATCHED_LIVE")) {
      if ($elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
        $watchLive = $elearningSubscription->getWatchLive();
        if ($watchLive) {
          $saveResult = true;
        }
      }
    }

    return($saveResult);
  }

  // Check if the exercise results are to be saved only the first time the exercise is done
  function saveResultFirstTime($elearningSubscriptionId = '') {
    $saveResult = $this->getSaveResultOption($elearningSubscriptionId);

    if ($saveResult == 'ELEARNING_SAVE_RESULT_FIRST') {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the exercise results are to be saved every time the exercise is done
  // keeping (not deleting) any existing result for the exercise and the participant
  function saveResultEveryTime($elearningSubscriptionId = '') {
    $saveResult = $this->getSaveResultOption($elearningSubscriptionId);

    if ($saveResult == 'ELEARNING_SAVE_RESULT_EVERY_TIME') {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the exercise results are to be saved every time the exercise is done
  // replacing (deleting) any existing result for the exercise and the participant
  function saveResultLastOnly($elearningSubscriptionId = '') {
    $saveResult = $this->getSaveResultOption($elearningSubscriptionId);

    if ($saveResult == 'ELEARNING_SAVE_RESULT_LAST_ONLY') {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the exercise results are to be saved only if it is better than the previous results
  function saveResultIfBetter($elearningSubscriptionId = '') {
    $saveResult = $this->getSaveResultOption($elearningSubscriptionId);

    if ($saveResult == 'ELEARNING_SAVE_RESULT_BETTER') {
      return(true);
    } else {
      return(false);
    }
  }

  // By default, the results grades are displayed on a scale of 10.\n\nBut it is possible to change the scale.
  function resultGradeScale() {
    $scale = $this->preferenceUtils->getValue("ELEARNING_RESULT_GRADE_SCALE");

    return($scale);
  }

  // By default, the audio file is played by a media player. But it can also be downloaded by the participant. This allows the participant to keep the audio file.
  function displayDownloadAudioFileIcon() {
    $displayDownloadAudioFileIcon = $this->preferenceUtils->getValue("ELEARNING_DISPLAY_AUDIO_DOWNLOAD");

    return($displayDownloadAudioFileIcon);
  }

  // A short instructions message is displayed at the beginning of the exercise to present and explain the exercise to the participant.
  function exerciseStartInstructions() {
    $exerciseStartInstructions = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_INSTRUCTIONS_START");

    return($exerciseStartInstructions);
  }

  // A short instructions message is displayed at the end of the exercise to give further instructions to the participant.
  function exerciseEndInstructions() {
    $exerciseEndInstructions = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_INSTRUCTIONS_END");

    return($exerciseEndInstructions);
  }

  // Render the results of an exercise when it is being done
  // The number of right answers are not calculated
  // from the stored results as the results may not yet be stored at all
  // Another render result method exists but it renders past results
  // from exercises previously done
  function renderResult($elearningExerciseId, $elearningSubscriptionId, $email) {
    global $gImagesUserUrl;
    global $gElearningUrl;
    global $gDataPath;
    global $gDataUrl;
    global $gUtilsUrl;
    global $gIsPhoneClient;

    if (!$elearningExercise = $this->selectById($elearningExerciseId)) {
      return;
    }

    $this->loadLanguageTexts();

    $hideSolutions = $elearningExercise->getHideSolutions();

    // Get the time when the exercise started
    $exerciseStartTime = $this->getExerciseStartTime($elearningExerciseId);

    // Get the time when the exercise ended
    // The end time may have been already stored in the session if displaying the contact page
    $exerciseEndTime = LibSession::getSessionValue(ELEARNING_SESSION_EXERCISE_END_TIME . $elearningExerciseId);
    if (!$exerciseEndTime) {
      $exerciseEndTime = $this->clockUtils->getLocalTimeStamp();
      LibSession::putSessionValue(ELEARNING_SESSION_EXERCISE_END_TIME . $elearningExerciseId, $exerciseEndTime);
    }

    $str = "\n<div class='elearning_result'><div class='elearning_result_title'>" . $this->websiteText[178] . "</div>";

    if ($elearningExercise->getSocialConnect()) {
      $str .= $this->publishSocialNotification($elearningExerciseId);
    }

    if (!$email) {
      $email = $this->userUtils->getUserEmail();
    }

    $contactPageBeforeResults = $this->displayContactPageBeforeResults($elearningExerciseId);

    if (!$email && $this->hideResultsIfNoEmail()) {
      if ($contactPageBeforeResults) {
        $strThanks = $this->getContactThanksAsUnidentified();
      } else {
        $strThanks = $this->getContactThanksAsCannotIdentify();
      }
      $str .= "\n<div class='elearning_exercise_comment'>$strThanks</div>";
    } else {
      if ($email) {
        $strThanks = $this->getContactThanksAsIdentified();
      } else {
        if ($contactPageBeforeResults) {
          $strThanks = $this->getContactThanksAsUnidentified();
        } else {
          $strThanks = $this->getContactThanksAsCannotIdentify();
        }
      }
      $str .= "\n<div class='elearning_exercise_comment'>$strThanks</div>";

      $exerciseTimedOut = LibSession::getSessionValue(ELEARNING_SESSION_EXERCISE_TIME_OUT . $elearningExerciseId);
      if ($exerciseTimedOut) {
        $timeOut = $this->getTimeOutMessage();
        $str .= "\n<div class='elearning_result_timeout'>" . $timeOut . "</div>";
        $this->resetExerciseTimes($elearningExerciseId);
      }

      $nbQuestions = 0;
      $nbCorrectAnswers = 0;
      $nbIncorrectAnswers = 0;
      $nbNotAnswered = 0;
      $strExercisePages = '';

      // Retrieve all the answers from the session
      $participantQuestionAnswers = array();
      if ($elearningExercisePages = $this->elearningExercisePageUtils->selectByExerciseId($elearningExerciseId)) {
        foreach ($elearningExercisePages as $elearningExercisePage) {
          $elearningExercisePageId = $elearningExercisePage->getId();
          $participantQuestionAnswers[$elearningExercisePageId] = $this->elearningExercisePageUtils->sessionRetrieveParticipantQuestionsAnswers($elearningExercisePage);
        }
      }

      // Render the results for all the exercise pages of questions
      foreach ($elearningExercisePages as $elearningExercisePage) {
        $elearningExercisePageId = $elearningExercisePage->getId();
        if (isset($participantQuestionAnswers[$elearningExercisePageId])) {
          $exercisePageParticipantAnswers = $participantQuestionAnswers[$elearningExercisePageId];
        } else {
          $exercisePageParticipantAnswers = array();
        }

        if ($elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId)) {
          $name = $elearningExercisePage->getName();
          $strExercisePages .= "\n<div class='elearning_exercise_page'>";
          if ($name) {
            $strExercisePages .= "\n<div class='elearning_exercise_page_name'>$name</div>";
          }
          $description = $elearningExercisePage->getDescription();
          if ($description) {
            $strExercisePages .= "\n<div class='elearning_exercise_page_description'>$description</div>";
          }

          // Get the questions of the question exercise page
          $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePageId);
          foreach ($elearningQuestions as $elearningQuestion) {
            $elearningQuestionId = $elearningQuestion->getId();
            if (isset($exercisePageParticipantAnswers[$elearningQuestionId])) {
              $participantAnswer = $exercisePageParticipantAnswers[$elearningQuestionId];
            } else {
              $participantAnswer = '';
            }
            if (!$participantAnswer) {
              $nbNotAnswered++;
            }

            $isCorrectlyAnswered = $this->elearningExercisePageUtils->isCorrectlyAnswered($elearningQuestionId, $participantAnswer);

            $strExercisePages .= $this->elearningExercisePageUtils->renderQuestionResult($elearningExercisePage, $elearningQuestion, $participantAnswer, $isCorrectlyAnswered, $hideSolutions);

            $nbQuestions++;
            if ($isCorrectlyAnswered) {
              $nbCorrectAnswers++;
            } else {
              if ($participantAnswer) {
                $nbIncorrectAnswers++;
              }
              if ($this->displayExplanation()) {
                if (!$hideSolutions) {
                  $strExercisePages .= $this->elearningExercisePageUtils->renderResultsExplanation($elearningQuestionId, $participantAnswer);
                }
              }
            }
          }

          $strExercisePages .= "\n</div>";
        }
      }

      if (!$this->hideExerciseTimes()) {
        // Get the maximum duration for the exercise
        $maxDuration = $this->getMaximumDuration($elearningExerciseId);

        if ($maxDuration > 0) {
          $str .= "\n<div class='elearning_result_max_duration'>" . $this->websiteText[54] . " <span class='elearning_result_max_duration_min'>" . $maxDuration . '</span>mn.' . '</div>';
        }

        if (($exerciseEndTime - $exerciseStartTime) > 0) {
          $elapsedTime = $this->renderElapsedTime($exerciseEndTime, $exerciseStartTime, $maxDuration);

          $str .= "\n<div class='elearning_result_elapsed_time'>" . $this->websiteText[55] . ' ' . $elapsedTime . '.</div>';
        }
      }

      // Save the number of correct answers
      // This may later be used to determine if the results are to be saved or not
      LibSession::putSessionValue(ELEARNING_SESSION_EXERCISE_NB_CORRECT_ANSWERS . $elearningExerciseId, $nbCorrectAnswers);

      // Display the number of correct answers
      $str .= "\n<div class='elearning_result_totals'>"
        . $this->websiteText[15] . " <span class='elearning_result_total_correct_answers'>$nbCorrectAnswers</span> "
        . $this->websiteText[21] . " <span class='elearning_result_nb_questions'>$nbQuestions</span> " . $this->websiteText[27]
        . "</div>";

      if ($nbNotAnswered > 0) {
        $nbWasAnswered = $nbQuestions - $nbNotAnswered;
        $str .= "\n<div class='elearning_result_totals'>"
          . $this->websiteText[207] . " <span class='elearning_result_nb_questions'>" . $nbWasAnswered . "</span> " . $this->websiteText[27]
          . "</div>";

        $str .= "\n<div class='elearning_result_totals'>"
          . $this->websiteText[206] . " <span class='elearning_result_nb_questions'>" . $nbNotAnswered . "</span> " . $this->websiteText[27]
          . "</div>";
      }

      // Display a scoring text
      if ($this->exerciseHasScoring($elearningExerciseId)) {
        $str .= $this->renderScoring($elearningExerciseId, $nbQuestions, $nbCorrectAnswers);
      }

      // Display the name and description
      $name = $elearningExercise->getName();
      $description = $elearningExercise->getDescription();

      $str .= "\n<div class='elearning_exercise_name'>$name</div>";

      $str .= $this->renderDescription($description);

      // Display the exercise pages of questions
      $str .= $strExercisePages;

      // Store the content for print
      $strPrintContent = $str;
    }

    // Display the link to a web page if any
    $webpageId = $elearningExercise->getWebpageId();
    if ($webpageId) {
      $url = $this->templateUtils->renderPageUrl($webpageId);

      $str .= "\n<div class='elearning_exercise_button'><a href='$url'><img src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' class='no_style_image_icon' title='" . $this->websiteText[152] . " 'alt='' style='vertical-align:middle;' /> " . $this->websiteText[152] . "</a></div>";
    }

    // If the user is not identified and no contact page is displayed
    // before displaying the results then check if a button to go to the contact page
    // is to be displayed
    if (!$email && !$contactPageBeforeResults && $this->displayButtonToContactPage()) {
      $str .= "\n<div class='elearning_exercise_button'>"
        . "<a href='$gElearningUrl/exercise/display_contact_page.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_EMAIL . "' class='no_style_image_icon' title='" . $this->websiteText[154] . "' style='vertical-align:middle;' /> " . $this->websiteText[154] . "</a>"
        . "</div>";
    }

    // Do not offer the detailled correction if the solutions are not to be displayed
    // Do not offer to do the exercise again as well
    if (!$hideSolutions) {
      $str .= "\n<form name='displayCorrection' action='$gElearningUrl/exercise/display_correction.php' method='post'>"
        . "\n<div class='elearning_exercise_button'>" . "<input type='image' src='$gImagesUserUrl/"
        .  IMAGE_ELEARNING_EXERCISE_CORRECTION . "' class='no_style_image_icon' title='"
        . $this->websiteText[181] . "' style='vertical-align:middle;' />"
        . " <a href='#' onclick=\"document.forms['displayCorrection'].submit(); return false;\" style='text-decoration:none;'>" . $this->websiteText[181] . "</a>"
        . "</div>"
        . "\n<div><input type='hidden' name='elearningExerciseId' value='$elearningExerciseId' /></div>"
        . "\n<input type='hidden' name='elearningSubscriptionId' value='$elearningSubscriptionId' />"
        . "\n</form>";

      $str .= "\n<form name='startOverExercise' action='$gElearningUrl/exercise/exercise_controller.php' method='post'>"
        . "\n<div class='elearning_exercise_button'>" . "<input type='image' src='$gImagesUserUrl/"
        .  IMAGE_ELEARNING_EXERCISE_START_OVER . "' class='no_style_image_icon' title='"
        . $this->websiteText[163] . "' style='vertical-align:middle;' />"
        . " <a href='#' onclick=\"document.forms['startOverExercise'].submit(); return false;\" style='text-decoration:none;'>" . $this->websiteText[163] . "</a>"
        . "</div>"
        . "\n<div><input type='hidden' name='elearningExerciseId' value='$elearningExerciseId' /></div>"
        . "\n<div><input type='hidden' name='elearningSubscriptionId' value='$elearningSubscriptionId' /></div>"
        . "\n</form>";
    }

    if (!$gIsPhoneClient && isset($strPrintContent)) {
      $strPrintContent .= "\n<script type='text/javascript'>printPage();</script>";

      $str .= "\n<div class='elearning_exercise_icons'>";

      $str .= $this->popupUtils->getUserPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_PRINTER . "' class='no_style_image_icon' title='" . $this->websiteText[10] . " 'alt='' style='vertical-align:middle;' />", $strPrintContent, 600, 600);

      $str .= '</div>';

      if ($this->websiteUtils->isCurrentWebsiteOption('OPTION_AFFILIATE')) {
        $str .= "<div class='elearning_exercise_icons'>" . $this->commonUtils->renderPoweredByLearnInTouch() . "</div>";
      }

      if (!$this->preferenceUtils->getValue("ELEARNING_HIDE_SOCIAL_BUTTONS")) {
        $strLink = "$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId";
        $str .= "<div class='elearning_social_buttons'>";
        $str .= $this->commonUtils->renderSocialNetworksButtons($name, $strLink);
        $str .= " </div>";
      }
    }

    $str .= "\n</div>"
      . "\n</div>";

    if (LibEmail::validate($email)) {
      // Store the visitor email in a cookie
      // The visitor will not need to type it in again next time an exercise is done
      LibCookie::putCookie($this->cookieVisitorEmail, $email, $this->cookieDuration);
    }

    // Now the exercise answers may be reset, if configured so, since the exercise is completed
    $this->allowResetExercise($elearningExerciseId);

    return($str);
  }

  // Publish on the social networks a message stating that the user has done an exercise
  function publishSocialNotification($elearningExerciseId) {
    global $gElearningUrl;
    global $gContactUrl;

    $websiteName = $this->profileUtils->getProfileValue("website.name");

    if ($elearningExerciseId) {
      $message = $this->websiteText[32] . ' ' . $websiteName;
      $url = "$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId";
    } else {
      $message = '';
      $url = '';
    }

    $caption = $this->websiteText[33];

    $actionLinks = array(array($this->websiteText[38], "$gContactUrl/post.php"));

    $str = $this->socialUserUtils->publishNotification($message, $url, $caption, $actionLinks);

    return($str);
  }

  // Check if no previous results for the exercise already exist
  // That is, if the exercise has not already been done
  function noPreviousResult($elearningExerciseId, $elearningSubscriptionId, $email) {
    $noPrevious = true;

    if ($elearningResult = $this->elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $elearningExerciseId)) {
      $noPrevious = false;
    } else if ($elearningResults = $this->elearningResultUtils->selectByEmailAndExercise($email, $elearningExerciseId)) {
      $noPrevious = false;
    }

    return($noPrevious);
  }

  // Check if the results are better than the previous one
  function isBetterResult($elearningExerciseId, $elearningSubscriptionId, $email) {
    $isBetter = true;

    $currentNbCorrectAnswers = LibSession::getSessionValue(ELEARNING_SESSION_EXERCISE_NB_CORRECT_ANSWERS . $elearningExerciseId);

    $previousNbCorrectAnswers = 0;

    if ($elearningResult = $this->elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $elearningExerciseId)) {
      $elearningResultId = $elearningResult->getId();
      $resultTotals = $this->elearningResultUtils->getExerciseTotals($elearningExerciseId, $elearningResultId);
      $previousNbCorrectAnswers = $this->elearningResultUtils->getResultNbCorrectAnswers($resultTotals);
    } else if ($elearningResults = $this->elearningResultUtils->selectByEmailAndExercise($email, $elearningExerciseId)) {
      foreach ($elearningResults as $elearningResult) {
        $elearningResultId = $elearningResult->getId();
        $resultTotals = $this->elearningResultUtils->getExerciseTotals($elearningExerciseId, $elearningResultId);
        $nbCorrectAnswers = $this->elearningResultUtils->getResultNbCorrectAnswers($resultTotals);
        if ($nbCorrectAnswers > $previousNbCorrectAnswers) {
          $previousNbCorrectAnswers = $nbCorrectAnswers;
        }
      }
    }

    if ($currentNbCorrectAnswers <= $previousNbCorrectAnswers) {
      $isBetter = false;
    }

    return($isBetter);
  }

  // Delete some results for an exercise that has already been done
  function deleteExistingResult($elearningExerciseId, $elearningSubscriptionId, $email) {
    if ($elearningResult = $this->elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $elearningExerciseId)) {
      $elearningResultId = $elearningResult->getId();
      $this->elearningResultUtils->deleteResult($elearningResultId);
    } else if ($elearningResults = $this->elearningResultUtils->selectByEmailAndExercise($email, $elearningExerciseId)) {
      foreach ($elearningResults as $elearningResult) {
        $elearningResultId = $elearningResult->getId();
        $this->elearningResultUtils->deleteResult($elearningResultId);
      }
    }
  }

  // Save the results of an exercise
  // First check if the results can be saved, that is, if the required information is provided
  // Then check if the results must be saved, that is, if the policy deems it appropriate
  function saveExerciseResults($elearningExerciseId, $elearningSubscriptionId, $email, $firstname, $lastname, $message) {
    // Check if the exercise is done by a registered user
    $userId = $this->userUtils->getLoggedUserId();

    // Init the variables
    $elearningResultId = '';
    $resultCanBeSaved = false;
    $resultMustBeSaved = false;

    // The results are stored for an identified user that has subscribed to a course
    // In that case the results are stored with the subscription id
    if ($elearningSubscriptionId && $userId) {
      // Check that the specified subscription belongs to the user and is not a phony one
      if ($elearningSubscription = $this->elearningSubscriptionUtils->selectByUserIdAndSubscriptionId($userId, $elearningSubscriptionId)) {
        $elearningSubscriptionId = $elearningSubscription->getId();
        // Check that the exercise is part of the subscription
        $elearningSessionId = $elearningSubscription->getSessionId();
        $elearningCourseId = $elearningSubscription->getCourseId();
        // If the subscription is for a course then check that the exercise belongs to the course
        if ($elearningCourseId) {
          if ($elearningCourseItem = $this->elearningCourseItemUtils->selectByCourseIdAndExerciseId($elearningCourseId, $elearningExerciseId)) {
            // The exercise is directly part of a course
            $resultCanBeSaved = true;
          } else if ($elearningCourseItem = $this->elearningCourseItemUtils->selectByCourseIdAndLessonExerciseId($elearningCourseId, $elearningExerciseId)) {
            // The exercise is not directly part of a course, but is part of it through a lesson
            $resultCanBeSaved = true;
          }
        } else {
          // The subscription is not for a particular course and allows any exercises
          $resultCanBeSaved = true;
        }
        if ($elearningAssignment = $this->elearningAssignmentUtils->selectBySubscriptionIdAndExerciseId($elearningSubscriptionId, $elearningExerciseId)) {
          // For an assignment, only save the results the fist time
          if (!$elearningResult = $this->elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $elearningExerciseId)) {
            $resultCanBeSaved = true;
          }
        }
      }
    } else {
      if (!$elearningSubscriptionId && ($user = $this->userUtils->selectById($userId))) {
        // The results are stored for a logged in user even if he has not subscribed to a course
        // In that case, the results are stored with his email address, just like an unidentified user
        $email = $user->getEmail();
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $resultCanBeSaved = true;
      } else if (!$userId) {
        // The results are store for an unidentified user provided he has given an email address
        // The email address is required to register the results of users that have not subscribed to a course
        if (LibEmail::validate($email)) {
          $resultCanBeSaved = true;
        }
      }
    }

    if ($resultCanBeSaved) {
      // Check if the result must be saved
      if ($this->saveResultEveryTime()) {
        // Saving the result even if the exercise had already been done
        $resultMustBeSaved = true;
      } else if ($this->saveResultLastOnly()) {
        // Saving the result even if the exercise had already been done
        // and deleting any previously existing result of the exercise for the participant
        // But do not delete any existing result if doing the exercise live
        $elearningResultId = LibSession::getSessionValue(ELEARNING_SESSION_RESULT_ID);
        if (!$elearningResultId) {
          $this->deleteExistingResult($elearningExerciseId, $elearningSubscriptionId, $email);
        }
        $resultMustBeSaved = true;
      } else if ($this->saveResultIfBetter()) {
        // Saving the result only if it is better than the existing one if any
        if ($this->isBetterResult($elearningExerciseId, $elearningSubscriptionId, $email)) {
          // But do not delete any existing result if doing the exercise live
          $elearningResultId = LibSession::getSessionValue(ELEARNING_SESSION_RESULT_ID);
          if (!$elearningResultId) {
            $this->deleteExistingResult($elearningExerciseId, $elearningSubscriptionId, $email);
          }
          $resultMustBeSaved = true;
        }
      } else if ($this->saveResultFirstTime()) {
        // Saving the result only if the exercise was done for the first time
        if ($this->noPreviousResult($elearningExerciseId, $elearningSubscriptionId, $email)) {
          $resultMustBeSaved = true;
        }
      } else {
        // Again, saving the result only if the exercise was done for the first time
        if ($this->noPreviousResult($elearningExerciseId, $elearningSubscriptionId, $email)) {
          $resultMustBeSaved = true;
        }
      }

      if ($this->saveResultIfWatchedLive($elearningSubscriptionId, $elearningExerciseId)) {
        $resultMustBeSaved = true;
      }
    }

    if ($resultMustBeSaved) {
      $elearningResultId = LibSession::getSessionValue(ELEARNING_SESSION_RESULT_ID);
      if ($elearningResultId) {
        LibSession::putSessionValue(ELEARNING_SESSION_RESULT_ID, '');
      } else {
        $elearningResult = new ElearningResult();
        $elearningResult->setSubscriptionId($elearningSubscriptionId);
        $elearningResult->setElearningExerciseId($elearningExerciseId);
        $systemDateTime = $this->clockUtils->getSystemDateTime();
        $elearningResult->setExerciseDate($systemDateTime);
        $this->elearningResultUtils->insert($elearningResult);
        $elearningResultId = $this->elearningResultUtils->getLastInsertId();
      }
      if ($elearningResult = $this->elearningResultUtils->selectById($elearningResultId)) {
        $elearningResult->setFirstname($firstname);
        $elearningResult->setLastname($lastname);
        $elearningResult->setEmail($email);
        $elearningResult->setMessage($message);
        $exerciseElapsedTime = $this->retrieveElapsedSeconds($elearningExerciseId);
        $elearningResult->setExerciseElapsedTime($exerciseElapsedTime);
        $this->elearningResultUtils->update($elearningResult);

        // Retrieve the answers from the user
        $participantQuestionAnswers = array();
        $elearningExercisePages = $this->elearningExercisePageUtils->selectByExerciseId($elearningExerciseId);
        foreach ($elearningExercisePages as $elearningExercisePage) {
          $elearningExercisePageId = $elearningExercisePage->getId();
          $participantQuestionAnswers[$elearningExercisePageId] = $this->elearningExercisePageUtils->sessionRetrieveParticipantQuestionsAnswers($elearningExercisePage);
        }

        if ($elearningResultId) {
          foreach ($elearningExercisePages as $elearningExercisePage) {
            $elearningExercisePageId = $elearningExercisePage->getId();
            $exercisePageParticipantAnswers = $participantQuestionAnswers[$elearningExercisePageId];
            foreach ($exercisePageParticipantAnswers as $elearningQuestionId => $participantAnswer) {
              if ($elearningQuestionId && $participantAnswer) {
                $elearningQuestion = $this->elearningQuestionUtils->selectById($elearningQuestionId);
                if ($this->elearningExercisePageUtils->isWrittenAnswer($elearningExercisePage)) {
                  if (!$elearningQuestionResults = $this->elearningQuestionResultUtils->selectByResultAndQuestion($elearningResultId, $elearningQuestionId)) {
                    $elearningQuestionResult = new ElearningQuestionResult();
                    $elearningQuestionResult->setElearningResult($elearningResultId);
                    $elearningQuestionResult->setElearningQuestion($elearningQuestionId);
                    $elearningQuestionResult->setElearningAnswerText($participantAnswer);
                    $this->elearningQuestionResultUtils->insert($elearningQuestionResult);
                    $elearningQuestionResultId = $this->elearningQuestionResultUtils->getLastInsertId();
                  }
                } else if ($this->elearningExercisePageUtils->answerIsArrayOfAnswers($participantAnswer)) {
                  $dragAndDropOrder = 0;
                  foreach ($participantAnswer as $participantAnswerId) {
                    if ($participantAnswerId) {
                      if (!$elearningQuestionResult = $this->elearningQuestionResultUtils->selectByResultAndQuestionAndAnswerId($elearningResultId, $elearningQuestionId, $participantAnswerId)) {
                        $elearningQuestionResult = new ElearningQuestionResult();
                        $elearningQuestionResult->setElearningResult($elearningResultId);
                        $elearningQuestionResult->setElearningQuestion($elearningQuestionId);
                        $elearningQuestionResult->setElearningAnswerId($participantAnswerId);
                        if ($this->elearningExercisePageUtils->typeIsDragAndDropOrderSentence($elearningExercisePage)) {
                          $dragAndDropOrder++;
                          $elearningQuestionResult->setElearningAnswerOrder($dragAndDropOrder);
                        }
                        $this->elearningQuestionResultUtils->insert($elearningQuestionResult);
                        $elearningQuestionResultId = $this->elearningQuestionResultUtils->getLastInsertId();
                      }
                    }
                  }
                } else {
                  $elearningQuestionResult = new ElearningQuestionResult();
                  $elearningQuestionResult->setElearningResult($elearningResultId);
                  $elearningQuestionResult->setElearningQuestion($elearningQuestionId);
                  $elearningQuestionResult->setElearningAnswerId($participantAnswer);
                  $this->elearningQuestionResultUtils->insert($elearningQuestionResult);
                  $elearningQuestionResultId = $this->elearningQuestionResultUtils->getLastInsertId();
                }
              }
            }
          }
        }
      }
    }

    return($elearningResultId);
  }

  // Send the results of an exercise
  function sendExerciseResults($elearningResultId, $elearningSubscriptionId, $elearningExerciseId, $email, $message) {
    global $gElearningUrl;
    global $gAccountPath;
    global $gSetupPath;

    // Send the exercise results to the participant
    if ($this->sendExerciseResultToParticipant()) {
      $scriptFile = $gElearningUrl . "/result/sendBatch.php?elearningResultId=$elearningResultId";
      $this->commonUtils->execlCLIwget($scriptFile);
    }

    // Check if the exercise is done by a registered user
    $userId = $this->userUtils->getLoggedUserId();

    if ($userId) {
      // Send an email alert for a participant who has subscribed to a course

      // Use the email of the logged in user
      $email = $this->userUtils->getUserEmail();

      $automaticAlert = $this->preferenceUtils->getValue("ELEARNING_AUTOMATIC_ALERT");
      if ($automaticAlert == 'ELEARNING_AUTOMATIC_ALERT_ASSIGNMENT') {
        if ($elearningAssignment = $this->elearningAssignmentUtils->selectBySubscriptionIdAndExerciseId($elearningSubscriptionId, $elearningExerciseId)) {
          $scriptFile = $gElearningUrl . "/result/sendExerciseAlertBatch.php?elearningResultId=$elearningResultId";
          $this->commonUtils->execlCLIwget($scriptFile);
        }
      } else if ($automaticAlert == 'ELEARNING_AUTOMATIC_ALERT_EXERCISE_TEXT') {
        if ($this->elearningResultUtils->containsWrittenText($elearningResultId)) {
          $scriptFile = $gElearningUrl . "/result/sendExerciseAlertBatch.php?elearningResultId=$elearningResultId";
          $this->commonUtils->execlCLIwget($scriptFile);
        }
      } else if ($automaticAlert == 'ELEARNING_AUTOMATIC_ALERT_EXERCISE') {
        $scriptFile = $gElearningUrl . "/result/sendExerciseAlertBatch.php?elearningResultId=$elearningResultId";
        $this->commonUtils->execlCLIwget($scriptFile);
      }
    } else {
      // Send an email alert for a visitor who has NOT subscribed to a course
      if ($email) {
        if (!$this->contactSchoolOnMessageOnly() || $message) {
          $scriptFile = $gElearningUrl . "/result/sendExerciseAlertBatch.php?elearningResultId=$elearningResultId";
          $this->commonUtils->execlCLIwget($scriptFile);
        }

        // Register the new email address in the mailing list
        if ($this->registerEmailAddress() && LibEmail::validate($email)) {
          $this->mailAddressUtils->subscribe($email);
        }
      }
    }
  }

  // Get the width of the image
  function getImageWidth() {
    $width = $this->preferenceUtils->getValue("ELEARNING_EXERCISE_IMAGE_WIDTH");

    return $width;
  }

  // Render the image of the exercise
  function renderImage($image, $emailFormat = false) {
    global $gDataPath;
    global $gDataUrl;
    global $gUtilsUrl;
    global $gIsPhoneClient;

    $imagePath = $this->imageFilePath;
    $imageUrl = $this->imageFileUrl;

    $str = '';

    if ($image && file_exists($imagePath . $image)) {
      $str .= "<div class='elearning_exercise_image'>";

      if (LibImage::isImage($imagePath . $image)) {
        // Check if the images are to be rendered in an email format
        // If so the image file path will be replaced bi 'cid' sequences
        // and no on-the-fly image resizing should take place
        if ($emailFormat) {
          $url = $imageUrl . '/' . $image;
        } else {
          if ($gIsPhoneClient && !$this->fileUploadUtils->isGifImage($imagePath . $image)) {
            // The image is created on the fly
            $width = $this->preferenceUtils->getValue("ELEARNING_PHONE_EXERCISE_IMAGE_WIDTH");
            $filename = urlencode($imagePath . $image);
            $url = $gUtilsUrl . "/printImage.php?filename=" . $filename
              . "&amp;width=" . $width . "&amp;height=";
          } else {
            $url = $imageUrl . '/' . $image;
          }
        }

        $str .= "<img class='elearning_exercise_image_file' src='$url' title='' alt='' style='vertical-align:middle;' />";
      } else {
        $libFlash = new LibFlash();
        if ($libFlash->isFlashFile($image)) {
          $str .= $libFlash->renderObject("$imageUrl/$image");
        }
      }
      $str .= "</div>";
    }

    return($str);
  }

  // Render the contact acknowledgement page
  function renderContactThanks($email) {
    if (LibEmail::validate($email)) {
      // Thank the participant for having identified himself
      $str = $this->getContactThanksAsIdentified();
    } else {
      // Tell the participant he could identify himself the next time he does an exercise
      $str = $this->getContactThanksAsUnidentified();
    }

    return($str);
  }

  // Render the scoring page
  function renderScoring($elearningExerciseId, $nbQuestions, $nbCorrectAnswers) {
    $this->loadLanguageTexts();

    $str = '';

    if ($elearningExercise = $this->selectById($elearningExerciseId)) {
      $scoringId = $elearningExercise->getScoringId();
      if ($elearningScoring = $this->elearningScoringUtils->selectById($scoringId)) {
        $requiredScore = $elearningScoring->getRequiredScore();

        $resultScore = 0;
        if ($nbQuestions > 0 && $nbCorrectAnswers > 0) {
          $resultScore = round($nbCorrectAnswers * 100 / $nbQuestions);
        }

        if ($requiredScore) {
          if ($resultScore >= $requiredScore) {
            $scoreMessage = $this->websiteText[225] . " " . $this->websiteText[226] . " <span class='elearning_result_scoring_required'>" . $requiredScore . "</span>% " . $this->websiteText[227] . " <span class='elearning_result_scoring_actual'>" . $resultScore . "</span>%.";
          } else {
            $scoreMessage = $this->websiteText[228] . " <span class='elearning_result_scoring_required'>" . $requiredScore . "</span>% " . $this->websiteText[229] . " <span class='elearning_result_scoring_actual'>" . $resultScore . "</span>%.";
          }
        } else {
          $scoreMessage = $this->websiteText[230] . " <span class='elearning_result_scoring_actual'>" . $resultScore . "</span>%.";
        }

        $str .= "<div class='elearning_result_scoring'>";
        $str .= "<div class='elearning_result_scoring_result'>" . $scoreMessage . "</div>";
        if ($elearningScoringRange = $this->elearningScoringUtils->getScoringMatch($scoringId, $resultScore)) {
          $currentLanguageCode = $this->languageUtils->getCurrentLanguageCode();
          $score = $this->languageUtils->getTextForLanguage($elearningScoringRange->getScore(), $currentLanguageCode);
          $advice = $this->languageUtils->getTextForLanguage($elearningScoringRange->getAdvice(), $currentLanguageCode);
          $proposal = $this->languageUtils->getTextForLanguage($elearningScoringRange->getProposal(), $currentLanguageCode);
          $linkText = $elearningScoringRange->getLinkText();
          $linkUrl = $elearningScoringRange->getLinkUrl();

          if ($score) {
            $str .= "<div class='elearning_result_scoring_level'>$score</div>";
          }
          if ($advice) {
            $str .= "<div class='elearning_result_scoring_advice'>$advice</div>";
          }
          if ($proposal) {
            $str .= "<div class='elearning_result_scoring_proposal'>$proposal</div>";
          }

          if ($linkUrl) {
            if (!$linkText) {
              $linkText = $this->websiteText[173];
            }
            $strUrl = $this->templateUtils->renderPageUrl($linkUrl);
            $str .= "<div class='elearning_result_scoring_link'>"
              . "<a href='$strUrl' title=''>$linkText</a>"
              . "</div>";
          }
        }
        $str .= "</div>";
      }
    }

    return($str);
  }

  // Allow the reset of an exercise
  function allowResetExercise($elearningExerciseId) {
    LibSession::putSessionValue(ELEARNING_QUESTION_RESET_ID . $elearningExerciseId, true);
  }

  // Check for the reset of an exercise exercise
  function checkResetExercise($elearningExerciseId) {
    $reset = LibSession::getSessionValue(ELEARNING_QUESTION_RESET_ID . $elearningExerciseId);

    if ($reset) {
      $this->resetExercise($elearningExerciseId);
      LibSession::delSessionValue(ELEARNING_QUESTION_RESET_ID . $elearningExerciseId);
    }
  }

  // Reset an exercise
  function resetExercise($elearningExerciseId) {
    // Reset the exercise start and end times
    $this->resetExerciseTimes($elearningExerciseId);

    // Reset the exercise answers
    if ($this->checkResetExerciseAnswers()) {
      $this->resetExerciseAnswers($elearningExerciseId);
    }
  }

  // Reset, that is, delete, the start and end times of an exercise
  function resetExerciseTimes($elearningExerciseId) {
    LibSession::delSessionValue(ELEARNING_SESSION_EXERCISE_START_TIME . $elearningExerciseId);
    LibCookie::deleteCookie(ELEARNING_SESSION_EXERCISE_START_TIME . $elearningExerciseId);
    LibSession::delSessionValue(ELEARNING_SESSION_EXERCISE_END_TIME . $elearningExerciseId);
  }

  // Get the start time of an exercise
  function getExerciseStartTime($elearningExerciseId) {
    $exerciseStartTime = LibSession::getSessionValue(ELEARNING_SESSION_EXERCISE_START_TIME . $elearningExerciseId);

    // Prevent the loss of the exercise start time if the http session expires
    if (!$exerciseStartTime) {
      $exerciseStartTime = LibCookie::getCookie(ELEARNING_SESSION_EXERCISE_START_TIME . $elearningExerciseId);
    }

    if (!$exerciseStartTime) {
      $exerciseStartTime = $this->resetStartTime($elearningExerciseId);
    }

    return($exerciseStartTime);
  }

  // Reset the exercise start time to now
  function resetStartTime($elearningExerciseId) {
    $exerciseStartTime = $this->clockUtils->getLocalTimeStamp();

    LibSession::putSessionValue(ELEARNING_SESSION_EXERCISE_START_TIME . $elearningExerciseId, $exerciseStartTime);
    LibCookie::putCookie(ELEARNING_SESSION_EXERCISE_START_TIME . $elearningExerciseId, $exerciseStartTime, (24 * 3600));

    return($exerciseStartTime);
  }

  // Reset the answers of the exercise
  function resetExerciseAnswers($elearningExerciseId) {
    if ($elearningQuestions = $this->elearningQuestionUtils->selectByExercise($elearningExerciseId)) {
      foreach ($elearningQuestions as $elearningQuestion) {
        $elearningQuestionId = $elearningQuestion->getId();
        $this->elearningQuestionUtils->resetAnswers($elearningQuestionId);
      }
    }
  }

  // Render the exercise page names in a tab bar
  function renderExercisePageTabs($elearningSubscriptionId, $elearningExercise, $currentElearningExercisePage) {
    global $gElearningUrl;
    global $gJsUrl;
    global $gIsPhoneClient;

    $str = "\n<div class='elearning_exercise_page_tabs'>";

    $str .= "\n<div class='slider'>";

    if ($elearningExercisePages = $this->elearningExercisePageUtils->selectByExerciseId($elearningExercise->getId())) {
      $disableNextTabs = $this->disableNextPageTabs($elearningExercise);
      $numberPageTabs = $this->pageTabsIsNumbers($elearningExercise);
      $numberWithPageTabs = $this->pageTabsWithNumbers($elearningExercise);
      $tabNumberName = 1;
      $currentTabListOrder = $currentElearningExercisePage->getListOrder();
      foreach ($elearningExercisePages as $elearningExercisePage) {
        $elearningExercisePageId = $elearningExercisePage->getId();
        $listOrder = $elearningExercisePage->getListOrder();
        if ($numberPageTabs) {
          $name = $tabNumberName;
        } else if ($numberWithPageTabs) {
          $name = $tabNumberName . '. ' . $elearningExercisePage->getName();
        } else {
          $name = $elearningExercisePage->getName();
        }
        $strTitle = $this->websiteText[164] . ' ' . $name;
        if ($listOrder < $currentTabListOrder || !$disableNextTabs) {
          // Submit the form instead of using a regular link so as to save the answers typed in
          // if the participant clicks on the next tab instead of the form submit button to the next page of questions
          $strTab = "<a href='javascript:void(0)' elearningExercisePageId='$elearningExercisePageId' title='$strTitle' alt=''>" . $name . "</a>";
        } else {
          $strTab = $name;
        }
        $strTab = "<span style='white-space:nowrap;' class='elearning_exercise_page_tab_name'>" . $strTab . "</span>";
        if ($listOrder == $currentTabListOrder) {
          $strTab = "<span class='elearning_exercise_page_tab_current'>" . $strTab . "</span>";
        } else if ($listOrder > $currentTabListOrder && $disableNextTabs) {
          $strTab = "<span class='elearning_exercise_page_tab_disabled'>" . $strTab . "</span>";
        }
        $str .= " <div class='elearning_exercise_page_tab'>" . $strTab . "</div>";
        $tabNumberName++;
      }
    }

    $str .= "</div>";

    $str .= "</div>";

    $str .= <<<HEREDOC
<script>
function selectPageTab(elearningExercisePageId) {
  if ('undefined' != typeof elearningSocket) {
    elearningSocket.emit('updateTab', {'elearningSubscriptionId': '$elearningSubscriptionId', 'elearningExercisePageId': $(this).find('a').attr('elearningExercisePageId')});
  }

  var url = "$gElearningUrl/exercise/exercise_controller.php";
  document.exercise_form.action = url;
  document.exercise_form.elearningExercisePageId.value = elearningExercisePageId;
  document.exercise_form.submit();
}

$(document).ready(function() {
  $('.elearning_exercise_page_tab').click(function(){
    selectPageTab($(this).find('a').attr('elearningExercisePageId'));
  });
});
</script>
HEREDOC;

    if (count($elearningExercisePages) > 2) {
      $str .= <<<HEREDOC
<style type="text/css">
.elearning_exercise_page_tabs {
  position: relative;
  top: 0;
  left: 0;
  overflow: hidden;
  height: 40px;
}
.elearning_exercise_page_tabs .elearning_exercise_page_tab {
  float: left;
}
</style>
HEREDOC;
      if ($gIsPhoneClient) {
        $str .= <<<HEREDOC
<style type="text/css">
.elearning_exercise_page_tabs .slider {
  width: 100%;
  height: 100%;
}
</style>
<script src='$gJsUrl/jquery/jquery.easing.1.3.js' type='text/javascript'></script>
<script type = 'text/javascript' src = '$gJsUrl/jquery/iosSlider-0.9.4.3b/jquery.iosslider.min.js'></script>
<script>
$(document).ready(function() {
  $('.elearning_exercise_page_tabs').iosSlider({
    snapToChildren: true,
    desktopClickDrag: true,
    startAtSlide: '$currentTabListOrder' - 1,
//    infiniteSlider: true,
    onSlideComplete: function(args) {
//      selectPageTab(args['currentSlideObject'].find('a').attr('elearningExercisePageId'));
    }
  });
});
</script>
HEREDOC;
      }
    }

    return($str);
  }

  // Check that the user is logged in
  function checkUserLogin() {
    $this->userUtils->checkValidUserLogin();
  }

  // Check if the exercise requires a login and if user is logged in
  function checkUserLoginForExercise($elearningExercise, $elearningSubscription) {
    if ($this->requireUserLogin($elearningExercise, $elearningSubscription)) {
      // Check for a login if required and if the exercise is not marked as public
      // A public exercise escapes the login procedure
      if (!$elearningExercise->getPublicAccess()) {
        if (!$this->isFreeSample($elearningExercise, $elearningSubscription)) {
          $this->checkUserLogin();
        }
      }
    }
  }

  // Check if the exercise is a free sample
  // A free sample is a course item that is offered for free and does not require a user login
  function isFreeSample($elearningExercise, $elearningSubscription) {
    $freeSample = false;

    if ($elearningSubscription) {
      $elearningCourseId = $elearningSubscription->getCourseId();
      if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
        $freeSamples = $elearningCourse->getFreeSamples();
        if ($freeSamples > 0) {
          if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByCourseId($elearningCourseId)) {
            $i = 1;
            foreach ($elearningCourseItems as $elearningCourseItem) {
              if ($elearningCourseItem->getElearningExerciseId() == $elearningExercise->getId()) {
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

  // Render the introduction of the exercise
  function renderExerciseIntroduction($elearningExerciseId, $elearningSubscriptionId = '') {
    global $gUserUrl;
    global $gElearningUrl;
    global $gImagesUserUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $elearningExercise = $this->selectById($elearningExerciseId);

    $name = $elearningExercise->getName();
    $description = $elearningExercise->getDescription();
    $introduction = $elearningExercise->getIntroduction();
    $audio = $elearningExercise->getAudio();
    $autostart = $elearningExercise->getAutostart();
    $public = $elearningExercise->getPublicAccess();

    $str = '';

    $str .= "\n<div class='elearning_exercise'>";

    $str .= "\n<div class='elearning_exercise_name'>$name</div>";

    $str .= $this->renderDescription($description);

    if (!$this->hideLevel()) {
      $elearningLevelId = $elearningExercise->getLevelId();
      $str .= $this->elearningLevelUtils->render($elearningLevelId);
    }

    $str .= $this->renderImage($elearningExercise->getImage());

    $str .= $this->renderPlayer($audio, $autostart);

    if ($elearningSubscriptionId) {
      $str .= $this->renderWhiteboard($elearningSubscriptionId);
    }

    $str .= "<div class='elearning_exercise_introduction'>";
    if ($this->hideIntroduction($elearningExerciseId)) {
      $str .= <<<HEREDOC
<script type="text/javascript">
function toggleIntroductionText() {
  var text = document.getElementById('introduction_text');
  if (text.style.display == 'none') {
    text.style.display = 'block';
  } else {
    text.style.display = 'none';
  }
}
</script>
HEREDOC;
      $str .= "<a href='#' onclick='toggleIntroductionText(); return false;' class='no_style_image_icon'>"
        . "<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_TEXT . "' class='no_style_image_icon' title='" .  $this->websiteText[11] . "' alt='" . $this->websiteText[11] . "' style='border-width:0px; vertical-align:middle; margin-right:4px;' /> <span style='text-decoration:none;'>". $this->websiteText[11] . "</span>"
        . "</a>"
        . "<div id='introduction_text' style='display:none;' class='no_style_image_icon'>"
        . $introduction
        . '</div>';
    } else {
      $str .= $introduction;
    }
    $str .= "</div>";

    $str .= $this->renderStartInstructions($elearningExercise);

    $elearningExercisePageId = $this->elearningExercisePageUtils->getFirstExercisePage($elearningExercise);

    $strTitle = $this->websiteText[174];
    $str .= "\n<div class='elearning_exercise_button'>"
      . "<a href='$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId&elearningExercisePageId=$elearningExercisePageId&elearningSubscriptionId=$elearningSubscriptionId' style='text-decoration:none;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' class='no_style_image_icon' title='$strTitle' style='vertical-align:middle;' /> $strTitle</a>"
      . "</div>";

    if (!$gIsPhoneClient) {
      $str .= "\n<div class='elearning_exercise_icons'>";

      $str .= ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_PRINTER . "' class='no_style_image_icon' title='" .  $this->websiteText[80] . " 'alt='' />", "$gElearningUrl/exercise/print_introduction.php?elearningExerciseId=$elearningExerciseId", 600, 600);

      $str .= ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_PRINTER . "' class='no_style_image_icon' title='" .  $this->websiteText[177] . " 'alt='' />", "$gElearningUrl/exercise/print_exercise.php?elearningExerciseId=$elearningExerciseId", 600, 600);

      $str .= " <a href='$gElearningUrl/exercise/pdf.php?elearningExerciseId=$elearningExerciseId'><img src='$gImagesUserUrl/" . IMAGE_COMMON_PDF . "' class='no_style_image_icon' title='" .  $this->websiteText[177] . " 'alt='' /></a>";

      $str .= ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_EMAIL_FRIEND . "' class='no_style_image_icon' title='" .  $this->websiteText[95] .  "' alt='' />", "$gElearningUrl/exercise/send.php?elearningExerciseId=$elearningExerciseId", 600, 600);

      $str .= "\n</div>";

      if ($this->websiteUtils->isCurrentWebsiteOption('OPTION_AFFILIATE')) {
        $str .= "<div class='elearning_exercise_icons'>" . $this->commonUtils->renderPoweredByLearnInTouch() . "</div>";
      }

      if (!$this->preferenceUtils->getValue("ELEARNING_HIDE_SOCIAL_BUTTONS")) {
        $strLink = "$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId";
        $str .= "<div class='elearning_social_buttons'>";
        $str .= $this->commonUtils->renderSocialNetworksButtons($name, $strLink);
        $str .= " </div>";
      }
    }

    $str .= "\n</div>";

    return($str);
  }

  // Get some instructions at the start of the exercise
  function getStartInstructions($elearningExercise) {
    $instructions = $elearningExercise->getInstructions();
    $currentLanguageCode = $this->languageUtils->getCurrentLanguageCode();
    $instructions = $this->languageUtils->getTextForLanguage($instructions, $currentLanguageCode);
    if (!$instructions) {
      $instructions = $this->exerciseStartInstructions();
    }

    return($instructions );
  }

  // Render some instructions at the start of the exercise
  function renderStartInstructions($elearningExercise) {
    $instructions = $this->getStartInstructions($elearningExercise);

    if ($instructions) {
      $instructions = "<div class='elearning_exercise_instruction'>$instructions</div>";
    }

    return($instructions );
  }

  // Get some instructions at the end of the exercise
  function getEndInstructions() {
    $instructions = $this->exerciseEndInstructions();

    return($instructions );
  }

  // Render some instructions at the end of the exercise
  function renderEndInstructions() {
    $instructions = $this->getEndInstructions();

    if ($instructions) {
      $instructions = "<div class='elearning_exercise_instruction'>$instructions</div>";
    }

    return($instructions );
  }

  // Render the whiteboard
  function renderWhiteboard($elearningSubscriptionId, $elearningClassId = '') {
    global $gElearningUrl;
    global $gImagesUserUrl;
    global $gJsUrl;
    global $gSocketHostname;

    $this->loadLanguageTexts();

    $whiteboard = '';
    if (!$elearningClassId) {
      if ($elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
        $whiteboard = $elearningSubscription->getWhiteboard();
        $elearningClassId = $elearningSubscription->getClassId();
      }
    }

    $labelClear = $this->websiteText[122];
    $labelClearTheWhiteboard = $this->websiteText[137];
    $labelPrint = $this->websiteText[170];
    $labelPrintTheWhiteboard = $this->websiteText[145];
    $labelMax = $this->websiteText[191];
    $labelMaxTheWhiteboard = $this->websiteText[239];

    $firstname = '';
    $adminId = $this->adminUtils->getLoggedAdminId();
    if ($adminId) {
      if ($admin = $this->adminUtils->selectById($adminId)) {
        $firstname = $admin->getFirstname();
      }
    } else {
      $userId = $this->userUtils->getLoggedUserId();
      if ($userId) {
        if ($user = $this->userUtils->selectById($userId)) {
          $firstname = $user->getFirstname();
        }
      }
    }

    $NODEJS_SOCKET_PORT = NODEJS_SOCKET_PORT;

    $whiteboardDisplayState = LibCookie::getCookie(ELEARNING_WHITEBOARD_DISPLAY_STATE);
    if ($whiteboardDisplayState) {
      $display = "block";
    } else {
      $display = "none";
    }

    $str = "<div id='subscriptionWhiteboard' style='display: $display;'><br />"
      . "<div class='elearning_whiteboard'>"
      . "<div class='elearning_whiteboard_buttons'>"
      . " <span class='elearning_whiteboard_clear' id='whiteboard_clear' title='$labelClearTheWhiteboard'>$labelClear</span>"
      . " <span class='elearning_whiteboard_print' id='whiteboard_print' title='$labelPrintTheWhiteboard'>$labelPrint</span>"
      . " <span class='elearning_whiteboard_print' id='whiteboard_max' title='$labelMaxTheWhiteboard'>$labelMax</span>"
      . "</div>"
      . "<div class='elearning_whiteboard_output' id='whiteboard_output'>$whiteboard</div>"
      . "<textarea class='elearning_whiteboard_input textarea_max' name='whiteboard_input' id='whiteboard_input' rows='1'></textarea>"
      . "<div id='whiteboard_loading' style='display:none;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_LOADING . "' title='" . $this->websiteText[2] . "' alt='' /></div>"
      . "<div id='whiteboard_warning' style='display:none;'></div>"
      . "<div id='whiteboard_url_content' style='display:none;'>"
      . "<iframe id='whiteboard_url_iframe' name='whiteboard_url_iframe' type='text/html' frameborder='0' width='370' height='300'></iframe>"
      . "</div>" 
      . "</div>"
      . "</div>";

    $ELEARNING_WHITEBOARD_DISPLAY_STATE = ELEARNING_WHITEBOARD_DISPLAY_STATE;

    $str .= <<<HEREDOC
<style type="text/css">
.elearning_whiteboard_buttons {
  width: 99% !important;
  cursor: pointer;
  text-align: right;
}
.elearning_whiteboard_buttons span:hover {
  text-decoration: underline;
}
.elearning_whiteboard_output {
  width: 99% !important;
  min-height: 100px;
  height: 100px;
  overflow-y: auto;
  background-color: #556B2F;
  color: white;
  border-style: solid;
  border-width: 3px;
  border-color: #000000;
  text-align: left;
  padding-left: 4px;
}
.elearning_whiteboard_input {
  width: 99% !important;
  text-align: left;
}
</style>
<script src='$gJsUrl/jquery/jquery.autogrowtextarea.js' type='text/javascript'></script>
<script type="text/javascript">
var whiteboadDisplayStatusCookieDuration = 24 * 360;

var elearningSocket;
var isAdmin = false;
var WHITEBOARD_HEIGHT_MAX = 500;
var WHITEBOARD_HEIGHT_NORMAL = 100;

$(function() {
  if ('undefined' != typeof io && 'undefined' == typeof elearningSocket) {
    console.log("Creating a socket on $gSocketHostname:$NODEJS_SOCKET_PORT/elearning");
    elearningSocket = io.connect('$gSocketHostname:$NODEJS_SOCKET_PORT/elearning');
  }
  if ('undefined' != typeof elearningSocket) {
    console.log("A socket on $gSocketHostname:$NODEJS_SOCKET_PORT/elearning exists");
    elearningSocket.on('connect', function() {
      console.log("The elearning namespace socket connected");
      elearningSocket.emit('watchLiveCopilot', {'elearningSubscriptionId': '$elearningSubscriptionId', 'elearningClassId': '$elearningClassId'});
    });
    elearningSocket.on('postLogin', function(data) {
      isAdmin = data.admin;
    });
    elearningSocket.on('message', function(message) {
      console.log(message);
    });
  }
});

function sendWhiteboardContent(content) {
  if ('undefined' != typeof elearningSocket) {
    // Publish the content locally
    $('#whiteboard_output').append(content);

    // Send the content
    elearningSocket.emit('updateWhiteboard', {'elearningSubscriptionId': '$elearningSubscriptionId', 'elearningClassId': '$elearningClassId', 'whiteboard': content});

    // Save the content
    saveWhiteboardContent($('#whiteboard_output').html());
  }
}

function clearOtherWhiteboardContent() {
  if ('undefined' != typeof elearningSocket) {
    // Send the content
    elearningSocket.emit('clearWhiteboard', {'elearningSubscriptionId': '$elearningSubscriptionId', 'elearningClassId': '$elearningClassId'});
  }
}

function saveWhiteboardContent(content) {
  content = encodeURIComponent(content);
  var url = "$gElearningUrl/subscription/save_whiteboard_live.php";
  var params = []; params["elearningSubscriptionId"] = '$elearningSubscriptionId'; params["whiteboard"] = content;
  ajaxAsynchronousPOSTRequest(url, params, postSaveWhiteboardLive);
}

function postSaveWhiteboardLive(responseText) {
}

function hideLocalParticipantWhiteboard() {
  $('#subscriptionWhiteboard').slideUp('fast');
  setCookie("$ELEARNING_WHITEBOARD_DISPLAY_STATE", 0, whiteboadDisplayStatusCookieDuration);
}

function showLocalParticipantWhiteboard() {
  $('#subscriptionWhiteboard').slideDown('fast');
  setCookie("$ELEARNING_WHITEBOARD_DISPLAY_STATE", 1, whiteboadDisplayStatusCookieDuration);
}

function hideOtherParticipantWhiteboard() {
  if ('undefined' != typeof elearningSocket) {
    $('#subscriptionWhiteboard').slideUp('fast');
    elearningSocket.emit('hideParticipantWhiteboard', {'elearningSubscriptionId': '$elearningSubscriptionId', 'elearningClassId': '$elearningClassId'});
  }
}

function showParticipantWhiteboard() {
  if ('undefined' != typeof elearningSocket) {
    $('#subscriptionWhiteboard').slideDown('fast');
    elearningSocket.emit('showParticipantWhiteboard', {'elearningSubscriptionId': '$elearningSubscriptionId', 'elearningClassId': '$elearningClassId'});
  }
}

function toggleParticipantWhiteboard() {
  if ($('#subscriptionWhiteboard').is(':visible')) {
    hideLocalParticipantWhiteboard();
    hideOtherParticipantWhiteboard();
  } else {
    showLocalParticipantWhiteboard();
    showParticipantWhiteboard();
  }
}

function refreshLocalWhiteboard(content) {
  $('#whiteboard_output').append(content);
}

function clearLocalWhiteboard() {
  unmaxWhiteboard();

  $('#whiteboard_url_iframe').attr('src', '');
  $('#whiteboard_output').html('');
  $('#whiteboard_input').val('');
  $('#whiteboard_url_content').fadeOut('slow'); 

  saveWhiteboardContent('');

  $('#whiteboard_input').focus();
}

function parseWhiteboardContentUrl(content) {
  if (content.indexOf('http') != -1) {
    var media = testUrlForMedia(content);
    if (media.type == 'youtube') {
      $('#whiteboard_url_iframe').attr('class', 'youtube-player');
      $('#whiteboard_url_iframe').attr('src', renderYouTubeVideoUrl(media.id));
      $('#whiteboard_url_content').fadeIn('slow'); 
    } else if (media.type == 'vimeo') {
      $('#whiteboard_url_iframe').attr('src', renderVimeoVideoUrl(media.id));
      $('#whiteboard_url_content').fadeIn('slow'); 
    } else {
      $('#whiteboard_url_content').fadeOut('slow'); 
    }
  } else {
    $('#whiteboard_url_content').fadeOut('slow'); 
  }
}

function maxWhiteboard() {
  var height = $('#whiteboard_output').css('height').replace(/[^-\d\.]/g, '');
  if (height == WHITEBOARD_HEIGHT_MAX) {
    $('#whiteboard_output').css({height: WHITEBOARD_HEIGHT_NORMAL});
  } else {
    $('#whiteboard_output').css({height: WHITEBOARD_HEIGHT_MAX});
  }
}

function unmaxWhiteboard() {
  $('#whiteboard_output').css({height: WHITEBOARD_HEIGHT_NORMAL});
}

$(document).ready(function() {

$("#whiteboard").autoGrow();

$("#whiteboard_clear").click(function() {
  clearLocalWhiteboard();
  clearOtherWhiteboardContent();
});

$("#whiteboard_print").click(function() {
  var printer = new Printer($('#whiteboard_output').html());
  printer.print();

  $('#whiteboard_input').focus();
});

$('#whiteboard_input').bind("keyup click", function (event) {
  // Update only on additional words
  if (event.which == 13) {
    var content =  "$firstname: " + $('#whiteboard_input').val() + '<br/>';
    parseWhiteboardContentUrl(content);
    sendWhiteboardContent(content);
    $('#whiteboard_input').val('');
  }
});

$("#whiteboard_max").click(function(e) {
  maxWhiteboard();
});

if ('undefined' != typeof elearningSocket) {
  elearningSocket.on('updateWhiteboard', function(data) {
    if (isAdmin || data.admin == true) {
      refreshLocalWhiteboard(data.whiteboard);
    }
  });
  elearningSocket.on('clearWhiteboard', function(data) {
    if (data.admin == true) {
      clearLocalWhiteboard();
    }
  });
  elearningSocket.on('showParticipantWhiteboard', function(data) {
    showLocalParticipantWhiteboard();
  });
  elearningSocket.on('hideParticipantWhiteboard', function(data) {
    hideLocalParticipantWhiteboard();
  });
}
});
</script>
HEREDOC;
    return($str);
  }

  // Render the exercise
  function renderExercise($elearningExercise, $elearningExercisePageId, $elearningSubscriptionId) {
    global $gJsUrl;
    global $gUserUrl;
    global $gElearningUrl;
    global $gImagesUserUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $elearningExerciseId = $elearningExercise->getId();
    $exerciseName = $elearningExercise->getName();
    $elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId);
    $elearningExercisePages = $this->elearningExercisePageUtils->selectByExerciseId($elearningExerciseId);

    $str = '';

    $str .= <<<HEREDOC
<script type="text/javascript">
var elearningSocket;
</script>
HEREDOC;

    // Get the maximum duration if any and the time left to the participant
    $maxDuration = $this->getMaximumDuration($elearningExerciseId);

    $str .= "\n<div class='elearning_exercise'>";

    $elearningSubscription = '';
    if ($elearningSubscriptionId) {
      $str .= $this->renderWhiteboard($elearningSubscriptionId);

      $elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId);
    }

    $isLast = $this->elearningExercisePageUtils->isLastExercisePage($elearningExercisePages, $elearningExercisePageId);
    if ($isLast) {
      $str .= "<form name='exercise_form' id='exercise_form' action='$gElearningUrl/exercise/last_exercise_page_controller.php' method='post'>";
    } else {
      $str .= "<form name='exercise_form' id='exercise_form' action='$gElearningUrl/exercise/exercise_controller.php' method='post'>";
    }

    $str .= "<input type='hidden' name='elearningExerciseId' value='$elearningExerciseId' />";
    $str .= "<input type='hidden' name='elearningSubscriptionId' value='$elearningSubscriptionId' />";
    $str .= "<input type='hidden' name='elearningPreviousExercisePageId' value='$elearningExercisePageId' />";

    // Check if a maximum duration is specified
    if ($maxDuration > 0) {
      // Render the duration the participant has for himself, to do the exercise
      if ($this->elearningExercisePageUtils->isFirstExercisePage($elearningExercisePages, $elearningExercisePageId)) {
        if (!$this->hideExerciseTimes()) {
          $str .= "<div class='elearning_exercise_max_duration'>" . $this->websiteText[128] . " <span class='elearning_exercise_max_duration_min'>" . $maxDuration . '</span>mn.' . '</div>';
        }
      }

      $currentTime = $this->clockUtils->getLocalTimeStamp();
      $exerciseStartTime = $this->getExerciseStartTime($elearningExerciseId);
      if (!$this->hideExerciseTimes()) {
        $strTimeLeft = $this->renderTimeLeft($elearningExerciseId, $currentTime, $exerciseStartTime, $maxDuration);
        $str .= "<div class='elearning_exercise_time_left'>" . $this->websiteText[129] . ' ' . $strTimeLeft . '</div>';
      }
    }

    if (!$this->hideProgressionBar($elearningExercise)) {
      if (count($elearningExercisePages) > 1) {
        $str .= $this->elearningExercisePageUtils->renderProgressionBar($elearningExercisePage);
      }
    }

    // Render a tab bar displaying all the exercise page names
    if (!$this->hidePageTabs($elearningExercise)) {
      if (count($elearningExercisePages) > 1) {
        $str .= $this->renderExercisePageTabs($elearningSubscriptionId, $elearningExercise, $elearningExercisePage);
      }
    }

    // Render the exercise page of questions
    $str .= $this->elearningExercisePageUtils->render($elearningExercise, $elearningExercisePage, $elearningSubscription);

    if (!$isLast) {
      $strTitle = $this->websiteText[52];
    } else {
      $str .= $this->renderEndInstructions();

      $strTitle = $this->websiteText[53];
    }

    // Check if the exercise pages are displayed in a fixed order or if no exercise page is yet displayed
    // Display the button to the next exercise page
    $elearningNextExercisePageId = $this->elearningExercisePageUtils->getNextPageOfQuestion($elearningExercisePageId);
    $str .= "<input type='hidden' name='elearningExercisePageId' value='$elearningNextExercisePageId' />";

    $str .= "<div class='elearning_exercise_button'>"
      . "<input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY
      . "' class='no_style_image_icon' title='$strTitle' style='vertical-align:middle;' /> "
      . " <a href='#' onclick=\"document.forms['exercise_form'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>"
      . $strTitle . "</a>"
      . "</div>";

    $str .= "</form>";

    $str .= "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'><tr><td>"
      . "\n<form name='startOverExercise' action='$gElearningUrl/exercise/exercise_controller.php' method='post'>"
      . "\n<div class='elearning_exercise_button'>"
      . "<input type='image' src='$gImagesUserUrl/" . IMAGE_ELEARNING_EXERCISE_START_OVER . "' class='no_style_image_icon' title='" . $this->websiteText[88] . "' style='vertical-align:middle;' />"
      . " <a href='#' onclick=\"document.forms['startOverExercise'].submit(); return false;\" style='text-decoration:none;'>" . $this->websiteText[88] . "</a>"
      . "</div>"
      . "<input type='hidden' name='elearningExerciseId' value='$elearningExerciseId' />"
      . "<input type='hidden' name='elearningSubscriptionId' value='$elearningSubscriptionId' />"
      . "</form>"
      . "</td></tr></table>";

    if (!$gIsPhoneClient) {
      $str .= "\n<div class='elearning_exercise_icons'>"
        . ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_PRINTER . "' class='no_style_image_icon' title='" .  $this->websiteText[112] . " 'alt='' />", "$gElearningUrl/exercise_page/print.php?elearningExercisePageId=$elearningExercisePageId", 600, 600)
        . " <a href='$gElearningUrl/exercise_page/pdf.php?elearningExercisePageId=$elearningExercisePageId'><img src='$gImagesUserUrl/" . IMAGE_COMMON_PDF . "' class='no_style_image_icon' title='" .  $this->websiteText[112] . " 'alt='' /></a>"
        . ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_PRINTER . "' class='no_style_image_icon' title='" .  $this->websiteText[148] . " 'alt='' />", "$gElearningUrl/exercise_page/print_page_questions.php?elearningExercisePageId=$elearningExercisePageId", 600, 600)
        . ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_EMAIL_FRIEND . "' class='no_style_image_icon' title='" .  $this->websiteText[95] .  "' alt='' />", "$gElearningUrl/exercise/send.php?elearningExerciseId=$elearningExerciseId", 600, 600)
        . "</div>";

      if ($this->websiteUtils->isCurrentWebsiteOption('OPTION_AFFILIATE')) {
        $str .= "<div class='elearning_exercise_icons'>" . $this->commonUtils->renderPoweredByLearnInTouch() . "</div>";
      }

      if (!$this->preferenceUtils->getValue("ELEARNING_HIDE_SOCIAL_BUTTONS")) {
        $strLink = "$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId";
        $str .= "<div class='elearning_social_buttons'>";
        $str .= $this->commonUtils->renderSocialNetworksButtons($exerciseName, $strLink);
        $str .= " </div>";
      }
    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the correction of an exercise
  function renderCorrection($elearningExercise, $elearningExercisePageId, $elearningSubscriptionId) {
    global $gElearningUrl;
    global $gImagesUserUrl;

    $this->loadLanguageTexts();

    $str = "<div class='elearning_result'>";

    $str .= "<div class='elearning_result_title'>" . $this->websiteText[125] . "</div>";

    $str .= $this->elearningExercisePageUtils->renderCorrection($elearningExercisePageId);

    $elearningNextExercisePageId = $this->elearningExercisePageUtils->getNextPageOfQuestion($elearningExercisePageId);
    $elearningExercisePages = $this->elearningExercisePageUtils->selectByExerciseId($elearningExercise->getId());
    $isLast = $this->elearningExercisePageUtils->isLastExercisePage($elearningExercisePages, $elearningExercisePageId);
    if (!$isLast) {
      $str .= "\n<form name='displayCorrection' action='$gElearningUrl/exercise/display_correction.php' method='post'>"
        . "<input type='hidden' name='elearningExerciseId' value='" . $elearningExercise->getId() . "' />"
        . "<input type='hidden' name='elearningSubscriptionId' value='$elearningSubscriptionId' />"
        . "<input type='hidden' name='elearningExercisePageId' value='$elearningNextExercisePageId' />"
        . "\n<div class='elearning_exercise_button'>"
        . "<input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' class='no_style_image_icon' title='" . $this->websiteText[155] . "' style='vertical-align:middle;' />"
        . " <a href='#' onclick=\"document.forms['displayCorrection'].submit(); return false;\" style='text-decoration:none;'>" . $this->websiteText[155] . "</a>"
        . "</div>"
        . "</form>";
    }

    $str .= "\n<form name='startOverExercise' action='$gElearningUrl/exercise/exercise_controller.php' method='post'>"
      . "\n<div class='elearning_exercise_button'>"
      . "<input type='image' src='$gImagesUserUrl/" . IMAGE_ELEARNING_EXERCISE_START_OVER . "' class='no_style_image_icon' title='" . $this->websiteText[88] . "' style='vertical-align:middle;' />"
      . " <a href='#' onclick=\"document.forms['startOverExercise'].submit(); return false;\" style='text-decoration:none;'>" . $this->websiteText[88] . "</a>"
      . "</div>"
      . "<input type='hidden' name='elearningExerciseId' value='" . $elearningExercise->getId() . "' />"
      . "<input type='hidden' name='elearningSubscriptionId' value='$elearningSubscriptionId' />"
      . "</form>";

    $str .= '</div>';

    $str .= '</div>';

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForCourse() {
    $str = "\n<div class='elearning_course'>The courses of a participant"
      . "<div class='elearning_course_page_title'>The title of the page</div>"
      . "<div class='elearning_course_title'>The title of a course</div>"
      . "<div class='elearning_course_info'>The course information"
      . "<div class='elearning_course_info_headline'>The information headline</div>"
      . "<div class='elearning_course_info_text'>The information text</div>"
      . "</div>"
      . "<div class='elearning_course_message'>A message</div>"
      . "<div class='elearning_course_header'>A column header</div>"
      . "<div class='elearning_course_cell'>A column cell</div>"
      . "<div class='elearning_course_results'>The results</div>"
      . "<div class='elearning_course_points'>The points</div>"
      . "<div class='elearning_course_icons'>The icons</div>"
      . "</div>";

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForExercise() {
    global $gImagesUserUrl;
    global $gStylingImage;

    $str = "\n<div class='elearning_exercise'>An exercise"
      . "<div class='elearning_whiteboard'>The whiteboard"
      . "<div class='elearning_whiteboard_buttons'>The buttons"
      . "</div>"
      . "<div class='elearning_whiteboard_output'>The border of the whiteboard output</div>"
      . "<div class='elearning_whiteboard_input'>The border of the whiteboard input</div>"
      . "</div>"
      . "<div class='elearning_exercise_name'>The name of the exercise</div>"
      . "<div class='elearning_exercise_description'>The description of the exercise</div>"
      . "<div class='elearning_level'>The level of the exercise"
      . "<span class='elearning_level_labelled_name'>The label and name of the level"
      . "<span class='elearning_level_name'>The name of the level</span>"
      . "</span>"
      . "</div>"
      . "<div class='elearning_exercise_image'>The image of the exercise"
      . "<img class='elearning_exercise_image_file' src='$gStylingImage' title='The border of the image of the exercise' alt='' />"
      . "</div>"
      . "<div class='elearning_exercise_player'>"
      . "<input type='image' src='$gImagesUserUrl/" . IMAGE_PLAYER_AUDIO . "' class='no_style_image_icon' title='A download icon' />"
      . "The audio player of the exercise"
      . "</div>"
      . "<div class='elearning_exercise_introduction'>The introduction of the exercise</div>"
      . "<div class='elearning_exercise_instruction'>The instructions for the exercise</div>"
      . "<div class='elearning_exercise_max_duration'>The maximum duration is <span class='elearning_exercise_max_duration_min'>XX</span>mn.</div>"
      . "<div class='elearning_exercise_time_left'>Your time left is <span class='elearning_exercise_time_minsec'>XX</span>mn<span class='elearning_exercise_time_minsec'>XX</span>s.</div>"
      . "<div class='elearning_exercise_page_progression_bar'>The progression bar</div>"
      . "<div class='elearning_exercise_page_tabs'>The pages tabs"
      . "<div class='elearning_exercise_page_tab'>A page tab"
      . "<div class='elearning_exercise_page_tab_name'>The name of a page tab</div>"
      . "<div class='elearning_exercise_page_tab_current'>The current page tab</div>"
      . "<div class='elearning_exercise_page_tab_disabled'>A disabled page tab</div>"
      . "</div>"
      . "</div>"
      . "<div class='elearning_exercise_page'>A page of questions"
      . "<div class='elearning_exercise_page_name'>The name of a page of questions</div>"
      . "<div class='elearning_exercise_page_description'>The description of a page of questions</div>"
      . "<div class='elearning_exercise_page_image'>The image of a page of questions"
      . "<img class='elearning_exercise_page_image_file' src='$gStylingImage' title='The border of the image of a page of questions' alt='' />"
      . "</div>"
      . "<div class='elearning_exercise_page_video'>The video of a page of questions</div>"
      . "<div class='elearning_exercise_page_player'>"
      . "<input type='image' src='$gImagesUserUrl/" . IMAGE_PLAYER_AUDIO . "' class='no_style_image_icon' title='A download icon' />"
      . "The audio player of a page of questions"
      . "</div>"
      . "<div class='elearning_exercise_page_instruction'>The instructions at the top of page of questions</div>"
      . "<div class='elearning_exercise_page_text'>The text of a page of questions</div>"
      . "<div class='elearning_exercise_page_keyboard'>The keyboard of special letters</div>"
      . "<div class='elearning_exercise_page_question'>A question"
      . "<div class='elearning_question_image'>The image of a question"
      . "<img class='elearning_question_image_file' src='$gStylingImage' title='The border of the image of a question' alt='' />"
      . "</div>"
      . "<div class='elearning_exercise_page_question_sentence'>The question sentence"
      . "<div class='elearning_question_hint'>(<img src='$gImagesUserUrl/" . IMAGE_COMMON_HINT . "' class='no_style_image_icon' title='A hint to help answer the question' alt='' /> A hint to help answer the question)</div>"
      . "<div class='elearning_question_hint_tooltip'>The question hint tooltip</div>"
      . "</div>"
      . "<div class='elearning_question_right_answer'>A right answer</div>"
      . "<div class='elearning_question_wrong_answer'>A wrong answer</div>"
      . "<div class='elearning_question_player'>"
      . "<input type='image' src='$gImagesUserUrl/" . IMAGE_PLAYER_AUDIO . "' class='no_style_image_icon' title='A download icon' />"
      . "The audio player of a question"
      . "</div>"
      . "<div class='elearning_question_solution'>The solution to a question</div>"
      . "<div class='elearning_question_instant_correction'>The instantaneous correction to an incorrect answer</div>"
      . "<div class='elearning_question_instant_explanation'>The instantaneous explanation to an incorrect answer</div>"
      . "<div class='elearning_question_instant_solution'>The instantaneous solution to an incorrect answer</div>"
      . "<div class='elearning_question_congratulation'>The instantaneous congratulation to a correct answer</div>"
      . "<div class='elearning_question_droppable'>A question in which answers can be dragged into</div>"
      . "<div class='elearning_question_answer'>An answer"
      . "<div class='elearning_question_answer_image'>The image of an answer"
      . "<img class='elearning_question_answer_image_file' src='$gStylingImage' title='The border of the image of an answer' alt='' />"
      . "</div>"
      . "<div class='elearning_question_answer_draggable'>An answer which can be dragged into a question</div>"
      . "<input class='elearning_question_answer_field' type='text' value='The input field of a written answer' size='25' maxlength='255' />"
      . "<div class='elearning_question_text_answer_field'>An answer as typed in text</div>"
      . "<div class='elearning_question_text_words_progress'>The message about the number of words"
      . " <span class='elearning_question_text_nb_words'>The number of typed in words</span>"
      . " <span class='elearning_question_text_max_nb_words'>The expected number of words</span>"
      . "</div>"
      . "<div class='elearning_question_text_progressbar'>The number of words progress bar</div>"
      . "</div>"
      . "</div>"
      . "</div>"
      . "<div class='elearning_exercise_copyright'>The copyright notice</div>"
      . "<div class='elearning_exercise_address'>The address</div>"
      . "<div class='elearning_exercise_button'>"
      . "<input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' class='no_style_image_icon' title='A button' />"
      . "</div>"
      . "<div class='elearning_exercise_icons'>"
      . "<input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_PRINTER . "' class='no_style_image_icon' title='An icon' />"
      . "</div>"
      . "<div class='elearning_social_buttons'>The social networks buttons</div>"
      . "</div>";

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForContactPage() {
    global $gImagesUserUrl;

    $str = "\n<div class='elearning_exercise'>"
      . "<div class='elearning_exercise_comment'>A comment line</div>"
      . "<span class='elearning_exercise_label'>A field label</span>"
      . " <span class='elearning_exercise_field'><input class='elearning_input' type='text' value='A field value' size='25' maxlength='255' /></span>"
      . "<div class='elearning_exercise_button'><img src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' class='no_style_image_icon' title='A button' alt='' /></div>"
      . "</div>";

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForResults() {
    global $gImagesUserUrl;

    $str = "\n<div class='elearning_result'>The results of an exercise"
      . "<div class='elearning_result_title'>The title of the page</div>"
      . "<div class='elearning_exercise_comment'>A comment on the exercise</div>"
      . "<div class='elearning_result_timeout'>The time out message</div>"
      . "<div class='elearning_result_max_duration'>The exercise had a duration of <span class='elearning_result_max_duration_min'>XX</span>mn.</div>"
      . "<div class='elearning_result_elapsed_time'>The exercise was completed in <span class='elearning_exercise_time_minsec'>XX</span>mn<span class='elearning_exercise_time_minsec'>XX</span>s.</div>"
      . "<div class='elearning_result_totals'>The results"
      . " <span class='elearning_result_total_correct_answers'>number</span>"
      . " of correct answers</div>"
      . "<div class='elearning_result_totals'>The results"
      . " <span class='elearning_result_nb_questions'>number</span>"
      . " of questions (answered and unanswered)</div>"
      . "<div class='elearning_result_scoring'>The scoring"
      . "<div class='elearning_result_scoring_result'>The scoring"
      . " <span class='elearning_result_scoring_required'>required percentage</span>"
      . " and"
      . " <span class='elearning_result_scoring_actual'>actual percentage</span>"
      . " result</div>"
      . "<div class='elearning_result_scoring_level'>The participant level</div>"
      . "<div class='elearning_result_scoring_advice'>The school advice</div>"
      . "<div class='elearning_result_scoring_proposal'>The course proposal</div>"
      . "<div class='elearning_result_scoring_link'>The link to a web page</div>"
      . "</div>"
      . "<div class='elearning_result_explanation'>The explanation to an incorrect answer</div>"
      . "<div class='elearning_social_buttons'>The social networks buttons</div>"
      . "</div>";

    return($str);
  }

  // Display a popup window for a label tip
  function getTipPopup($content, $width, $height) {
    global $gImagesUserUrl;

    $str = $this->popupUtils->getUserTipPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_TIP . "' class='no_style_image_icon' title='' alt='' />", $content, $width, $height);

    return($str);
  }

}

?>
