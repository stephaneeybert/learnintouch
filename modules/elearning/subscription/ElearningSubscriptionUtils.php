<?

class ElearningSubscriptionUtils extends ElearningSubscriptionDB {

  var $websiteText;

  var $languageUtils;
  var $popupUtils;
  var $clockUtils;
  var $userUtils;
  var $elearningSessionUtils;
  var $elearningResultUtils;
  var $elearningTeacherUtils;
  var $elearningCourseUtils;
  var $elearningClassUtils;
  var $elearningCourseItemUtils;
  var $elearningLessonParagraphUtils;
  var $elearningAssignmentUtils;

  function ElearningSubscriptionUtils() {
    $this->ElearningSubscriptionDB();
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function isClosed($elearningSubscription) {
    $closed = false;

    $systemDate = $this->clockUtils->getSystemDate();

    if ($elearningSubscription) {
      $subscriptionClose = $elearningSubscription->getSubscriptionClose();
      if ($this->clockUtils->systemDateIsSet($subscriptionClose) && $this->clockUtils->systemDateIsGreater($systemDate, $subscriptionClose)) {
        $closed = $subscriptionClose;
      } else {
        $elearningSessionId = $elearningSubscription->getSessionId();
        if ($elearningSession = $this->elearningSessionUtils->selectById($elearningSessionId)) {
          $closed = $elearningSession->getClosed();
          if ($closed) {
            $closed = $systemDate;
          }
          $sessionClosedDate = $elearningSession->getCloseDate();
          if ($this->clockUtils->systemDateIsSet($sessionClosedDate) && $this->clockUtils->systemDateIsGreater($systemDate, $sessionClosedDate)) {
            $closed = $sessionClosedDate;
          }
        }
      }
    }

    return($closed);
  }

  // Check that a subscription belongs to a user
  function checkUserSubscription($userId, $elearningSubscription) {
    global $gElearningUrl;

    if (!$this->isUserSubscription($userId, $elearningSubscription)) {
      $str = LibHtml::urlRedirect($gElearningUrl . "/subscription/display_participant_subscriptions.php");
      printContent($str);
      exit;
    }
  }

  // Check that a subscription belongs to a user and that the subscription is opened
  function checkIsOpenedUserSubscription($userId, $elearningSubscription) {
    global $gElearningUrl;

    if ($userId && $elearningSubscription) {
      if (!$this->isUserSubscription($userId, $elearningSubscription) || $this->isClosed($elearningSubscription)) {
        $str = LibHtml::urlRedirect($gElearningUrl . "/subscription/display_participant_subscriptions.php");
        printContent($str);
        exit;
      }
    }
  }

  // Check that a subscription belongs to a user
  function isUserSubscription($userId, $elearningSubscription) {
    // A subscription is not always specified
    if (!$elearningSubscription) {
      return(true);
    }

    $elearningSubscriptionId = $elearningSubscription->getId();
    if ($elearningSubscriptions = $this->selectByUserId($userId)) {
      foreach ($elearningSubscriptions as $elearningSubscription) {
        if ($elearningSubscription->getId() == $elearningSubscriptionId) {
          return(true);
        }
      }
    }

    return(false);
  }

  // Check that a participant has a subscription with a teacher
  function isTeacherParticipant($participantUserId, $teacherUserId) {
    if ($elearningTeacher = $this->elearningTeacherUtils->selectByUserId($teacherUserId)) {
      $elearningTeacherId = $elearningTeacher->getId();
      if ($elearningSubscriptions = $this->selectByUserIdAndTeacherId($participantUserId, $elearningTeacherId)) {
        return(true);
      }
    }

    return(false);
  }

  // Check that a subscription belongs to a teacher
  function isTeacherSubscription($elearningSubscription, $teacherUserId) {
    if ($elearningTeacher = $this->elearningTeacherUtils->selectByUserId($teacherUserId)) {
      $elearningTeacherId = $elearningTeacher->getId();
      if ($elearningSubscription->getTeacherId() == $elearningTeacherId) {
        return(true);
      }
    }

    return(false);
  }

  // Save the current exercise being done
  function saveLastExerciseId($elearningSubscription, $elearningExerciseId) {
    $elearningSubscription->setLastExerciseId($elearningExerciseId);
    $this->update($elearningSubscription);
  }

  // Save the current page of question of an exercise being done
  function saveLastExercisePageId($elearningSubscription, $elearningExercisePageId) {
    $elearningSubscription->setLastExercisePageId($elearningExercisePageId);
    $this->update($elearningSubscription);
  }

  // Save the last time the participant was active
  function saveLastActive($elearningSubscription) {
    if ($elearningSubscription->getUserId() == $this->userUtils->getLoggedUserId()) {
      $systemDateTime = $this->clockUtils->getSystemDateTime();
      $elearningSubscription->setLastActive($systemDateTime);
      $this->update($elearningSubscription);
    }
  }

  // Get the next exercise, that is, the first exercise of the course, without any results
  function getNextExercise($elearningSubscription) {
    $elearningSubscriptionId = $elearningSubscription->getId();
    $courseId = $elearningSubscription->getCourseId();
    if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByCourseId($courseId)) {
      foreach ($elearningCourseItems as $elearningCourseItem) {
        $elearningExerciseId = $elearningCourseItem->getElearningExerciseId();
        $elearningLessonId = $elearningCourseItem->getElearningLessonId();
        if ($elearningExerciseId) {
          if (!$elearningResult = $this->elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $elearningExerciseId)) {
            return($elearningExerciseId);
          }
        } else if ($elearningLessonId) {
          // or the first exercise of the first lesson without results
          if ($elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonId($elearningLessonId)) {
            foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
              $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
              if ($elearningExerciseId) {
                if (!$elearningResult = $this->elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $elearningExerciseId)) {
                  return($elearningExerciseId);
                }
              }
            }
          }
        }
      }
    }
  }

  // Check if a participant has a mobile phone number and has subscribed to receive sms messages
  function hasSmsSubscription($elearningSubscriptionId) {
    if ($elearningSubscription = $this->selectById($elearningSubscriptionId)) {
      $userId = $elearningSubscription->getUserId();
      if ($user = $this->userUtils->selectById($userId)) {
        $mobilePhone = $user->getMobilePhone();
        $smsSubscribe = $user->getSmsSubscribe();
        if ($mobilePhone && $smsSubscribe) {
          return(true);
        }
      }
    }

    return(false);
  }

  // Check if a subscription has some results
  function hasResults($elearningSubscription) {
    if ($elearningSubscription) {
      $elearningSubscriptionId = $elearningSubscription->getId();
      if ($elearningResults = $this->elearningResultUtils->selectBySubscriptionId($elearningSubscriptionId)) {
        if (count($elearningResults) > 0) {
          return(true);
        }
      }
    }

    return(false);
  }

  // Check if an exercise has some results
  function exerciseHasResults($elearningSubscriptionId, $elearningExerciseId) {
    if ($elearningSubscription = $this->selectById($elearningSubscriptionId)) {
      if ($elearningResult = $this->elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $elearningExerciseId)) {
        return(true);
      }
    }

    return(false);
  }

  // Delete a subscription
  function deleteSubscription($elearningSubscriptionId) {
    if ($elearningResults = $this->elearningResultUtils->selectBySubscriptionId($elearningSubscriptionId)) {
      foreach ($elearningResults as $elearningResult) {
        $this->elearningResultUtils->deleteResult($elearningResult->getId());
      }
    }

    if ($elearningAssignments = $this->elearningAssignmentUtils->selectBySubscriptionId($elearningSubscriptionId)) {
      foreach ($elearningAssignments as $elearningAssignment) {
        $this->elearningAssignmentUtils->delete($elearningAssignment->getId());
      }
    }

    $this->delete($elearningSubscriptionId);
  }

  // Render the list of subscriptions of a teacher
  function renderTeacherSubscriptions() {
    global $gElearningUrl;
    global $gImagesUserUrl;
    global $gJSNoStatus;
    global $gElearningUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $userId = $this->userUtils->getLoggedUserId();

    $str = '';

    $str .= "\n<div class='elearning_subscription_list'>";

    if ($userId) {
      $systemDate = $this->clockUtils->getSystemDate();

      $elearningSessionId = LibEnv::getEnvHttpGET("elearningSessionId");
      if (!$elearningSessionId) {
        $elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");
      }

      $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");
      if (!$elearningCourseId) {
        $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
      }

      $elearningClassId = LibEnv::getEnvHttpGET("elearningClassId");
      if (!$elearningClassId) {
        $elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");
      }

      if ($elearningTeacher = $this->elearningTeacherUtils->selectByUserId($userId)) {
        $elearningTeacherId = $elearningTeacher->getId();
      } else {
        $elearningTeacherId = '';
      }

      $systemDate = $this->clockUtils->getSystemDate();
      $elearningSessions = $this->elearningSessionUtils->selectBySubscriptionWithTeacherId($elearningTeacherId);
      $elearningSessionList = Array('' => '');
      foreach ($elearningSessions as $elearningSession) {
        $wSessionId = $elearningSession->getId();
        $name = $elearningSession->getName();
        $openDate = $elearningSession->getOpenDate();
        $closeDate = $elearningSession->getCloseDate();
        $openDate = $this->clockUtils->systemToLocalNumericDate($openDate);
        $closeDate = $this->clockUtils->systemToLocalNumericDate($closeDate);
        $elearningSessionList[$wSessionId] = $name . ' (' . $openDate . ' / ' . $closeDate . ')';
      }
      $strSelectSession = LibHtml::getSelectList("elearningSessionId", $elearningSessionList, $elearningSessionId, true);

      $elearningCourses = $this->elearningCourseUtils->selectBySubscriptionWithTeacherId($elearningTeacherId);
      $elearningCourseList = Array('' => '');
      foreach ($elearningCourses as $elearningCourse) {
        $wCourseId = $elearningCourse->getId();
        $wName = $elearningCourse->getName();
        $elearningCourseList[$wCourseId] = $wName;
      }
      $strSelectCourse = LibHtml::getSelectList("elearningCourseId", $elearningCourseList, $elearningCourseId, true);

      $elearningClasses = $this->elearningClassUtils->selectBySubscriptionWithTeacherId($elearningTeacherId);
      $elearningClassList = Array('' => '');
      foreach ($elearningClasses as $elearningClass) {
        $wClassId = $elearningClass->getId();
        $name = $elearningClass->getName();
        $elearningClassList[$wClassId] = $name;
      }
      $strSelectClass = LibHtml::getSelectList("elearningClassId", $elearningClassList, $elearningClassId, true);

      $elearningSubscriptions = '';
      if ($elearningSessionId && $elearningCourseId && $elearningClassId && $elearningTeacherId) {
        $elearningSubscriptions = $this->selectBySessionIdAndCourseAndClassIdAndTeacherId($elearningSessionId, $elearningCourseId, $elearningClassId, $elearningTeacherId);
      } else if ($elearningCourseId && $elearningClassId && $elearningTeacherId) {
        $elearningSubscriptions = $this->selectByCourseIdAndClassIdAndTeacherId($elearningCourseId, $elearningClassId, $elearningTeacherId);
      } else if ($elearningSessionId && $elearningClassId && $elearningTeacherId) {
        $elearningSubscriptions = $this->selectBySessionIdAndClassIdAndTeacherId($elearningSessionId, $elearningClassId, $elearningTeacherId);
      } else if ($elearningSessionId && $elearningCourseId && $elearningTeacherId) {
        $elearningSubscriptions = $this->selectBySessionIdAndCourseAndTeacherId($elearningSessionId, $elearningCourseId, $elearningTeacherId);
      } else if ($elearningCourseId && $elearningTeacherId) {
        $elearningSubscriptions = $this->selectByCourseIdAndTeacherId($elearningCourseId, $elearningTeacherId);
      } else if ($elearningClassId && $elearningTeacherId) {
        $elearningSubscriptions = $this->selectByClassIdAndTeacherId($elearningClassId, $elearningTeacherId);
      } else if ($elearningSessionId && $elearningTeacherId) {
        $elearningSubscriptions = $this->selectBySessionIdAndTeacherId($elearningSessionId, $elearningTeacherId);
      } else if ($elearningTeacherId) {
        $elearningSubscriptions = $this->selectByTeacherId($elearningTeacherId);
      }

      $str .= "\n<div class='elearning_subscription_list_title'>"
        . $this->websiteText[0]
        . "</div>";

      $str .= "<form action='$gElearningUrl/subscription/display_teacher_subscriptions.php' method='post'>";
      $str .= "\n<table border='0' width='100%' cellspacing='2' cellpadding='2'>";

      if (count($elearningCourses) > 0) {
        $str .= "<tr><td>"
          . $this->websiteText[4]
          . "</td><td>"
          . $strSelectCourse
          . "</td></tr>";
      }

      if (count($elearningSessions) > 0) {
        $str .= "<tr><td>"
          . $this->websiteText[3]
          . "</td><td>"
          . $strSelectSession
          . "</td></tr>";
      }

      if (count($elearningClasses) > 0) {
        $str .= "<tr><td>"
          . $this->websiteText[5]
          . "</td><td>"
          . $strSelectClass
          . "</td></tr>";
      }

      $str .= "\n</table>";
      $str .= "</form>";

      if ($elearningSubscriptions) {
        $str .= "\n<table border='0' width='100%' cellspacing='0' cellpadding='0'>";

        foreach($elearningSubscriptions as $elearningSubscription) {
          $elearningSubscriptionId = $elearningSubscription->getId();
          if ($elearningSubscription = $this->selectById($elearningSubscriptionId)) {
            $elearningCourseId = $elearningSubscription->getCourseId();
            if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
              $courseName = $elearningCourse->getName();
            }

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
            }

            $userId = $elearningSubscription->getUserId();
            if ($user = $this->userUtils->selectById($userId)) {
              $firstname = $user->getFirstname();
              $lastname = $user->getLastname();
            }

            $subscriptionDate = $elearningSubscription->getSubscriptionDate();
            $strSubscriptionDate = $this->clockUtils->systemToLocalNumericDate($subscriptionDate);

            $strLessonsAndExercises = "<a href='$gElearningUrl/subscription/display_participant_subscriptions.php?userId=$userId' $gJSNoStatus title='" . $this->websiteText[6] . "'>" . "<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_EXERCISE . "' class='no_style_image_icon' title='' alt='' /></a>";

            $strDisplayGraph = ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_COURSE_GRAPH . "' class='no_style_image_icon' title='" .  $this->websiteText[1] . " 'alt='' />", "$gElearningUrl/result/display_graph.php?elearningSubscriptionId=$elearningSubscriptionId", 600, 600);
            $strName = "<a href='$gElearningUrl/subscription/display_participant_subscriptions.php?userId=$userId' $gJSNoStatus title='" . $this->websiteText[6] . "'>$firstname $lastname</a>";

            $str .= "\n<tr>";
            $str .= "\n<td class='no_style_list_line'>";
            $str .= "\n<div class='elearning_subscription_list_comment'>$strName</div>";
            $str .= "</td><td class='no_style_list_line'>";
            $str .= "\n<div style='text-align:right;'>$strLessonsAndExercises $strDisplayGraph</div>";
            $str .= "\n</tr>";
          }
        }

        $str .= "\n</table>";
      } else {
        $str .= "\n<div class='elearning_subscription_list_comment'>" . $this->websiteText[2] . "</div>";
      }
    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the live poll javascript
  function renderLivePollJs() {
    global $gElearningUrl;
    global $gCommonImagesUrl;
    global $gImageLightOrangeSmallBlink;
    global $gImageLightGreenSmall;
    global $gHostname;

    $ELEARNING_DOM_ID_NO_ANSWER = ELEARNING_DOM_ID_NO_ANSWER;
    $ELEARNING_DOM_ID_INCORRECT = ELEARNING_DOM_ID_INCORRECT;
    $ELEARNING_DOM_ID_CORRECT = ELEARNING_DOM_ID_CORRECT;
    $ELEARNING_DOM_ID_INACTIVE = ELEARNING_DOM_ID_INACTIVE;
    $ELEARNING_DOM_ID_LIVE_RESULT = ELEARNING_DOM_ID_LIVE_RESULT;
    $ELEARNING_DOM_ID_RESULT_GRADE = ELEARNING_DOM_ID_RESULT_GRADE;
    $ELEARNING_DOM_ID_RESULT_RATIO = ELEARNING_DOM_ID_RESULT_RATIO;
    $ELEARNING_DOM_ID_RESULT_ANSWER = ELEARNING_DOM_ID_RESULT_ANSWER;
    $ELEARNING_DOM_ID_RESULT_POINT = ELEARNING_DOM_ID_RESULT_POINT;
    $ELEARNING_DOM_ID_READING = ELEARNING_DOM_ID_READING;
    $ELEARNING_DOM_ID_WRITING = ELEARNING_DOM_ID_WRITING;
    $ELEARNING_DOM_ID_LISTENING = ELEARNING_DOM_ID_LISTENING;

    $this->loadLanguageTexts();

    $titleInactive = $this->mlText[38];
    $titleInactive = LibString::decodeHtmlspecialchars($titleInactive);
    $titleInactive = LibString::escapeQuotes($titleInactive);
    $titleCompleted = $this->mlText[39];
    $titleCompleted = LibString::decodeHtmlspecialchars($titleCompleted);
    $titleCompleted = LibString::escapeQuotes($titleCompleted);

    $NODEJS_SOCKET_PORT = NODEJS_SOCKET_PORT;

    $strLiveResultJs = <<<HEREDOC
<script type='text/javascript'>
function renderOneLiveResult(responseText) {
  var response = eval('(' + responseText + ')');
  renderLiveResult(response);
}

function renderLiveResult(liveResult) {
  var elearningResultId = liveResult.elearningResultId;
  if (elearningResultId > 0) {
    var nbQuestions = liveResult.nbQuestions;
    var nbNoAnswers = liveResult.nbNoAnswers;
    var nbCorrectAnswers = liveResult.nbCorrectAnswers;
    var nbCorrectReadingAnswers = liveResult.nbCorrectReadingAnswers;
    var nbCorrectWritingAnswers = liveResult.nbCorrectWritingAnswers;
    var nbCorrectListeningAnswers = liveResult.nbCorrectListeningAnswers;
    var nbIncorrectAnswers = liveResult.nbIncorrectAnswers;
    var nbIncorrectReadingAnswers = liveResult.nbIncorrectReadingAnswers;
    var nbIncorrectWritingAnswers = liveResult.nbIncorrectWritingAnswers;
    var nbIncorrectListeningAnswers = liveResult.nbIncorrectListeningAnswers;
    var nbAnswers = parseInt(nbCorrectAnswers) + parseInt(nbIncorrectAnswers);
    var nbPoints = liveResult.nbPoints;
    var nbReadingPoints = liveResult.nbReadingPoints;
    var nbWritingPoints = liveResult.nbWritingPoints;
    var nbListeningPoints = liveResult.nbListeningPoints;
    var grade = liveResult.grade;
    var graphImageUrlNoAnswer = liveResult.graphImageUrlNoAnswer;
    var graphImageUrlIncorrect = liveResult.graphImageUrlIncorrect;
    var graphImageUrlCorrect = liveResult.graphImageUrlCorrect;
    var graphTitle = liveResult.graphTitle;
    var subscription = liveResult.subscription;
    if (subscription) {
      var isAbsent = subscription.isAbsent;
      var graphDom = document.getElementById("$ELEARNING_DOM_ID_LIVE_RESULT" + elearningResultId);
      var elearningSubscriptionId = subscription.elearningSubscriptionId;
      var elearningExerciseId = subscription.elearningExerciseId;
      var imageDom = document.getElementById("$ELEARNING_DOM_ID_INACTIVE" + elearningSubscriptionId + "_" + elearningExerciseId);
      if (!isAbsent) {
        graphDom.style.display = 'inline';
        imageDom.style.visibility = 'visible';
        var gradeDom = $("#$ELEARNING_DOM_ID_RESULT_GRADE" + elearningResultId);
        if (gradeDom) {
          gradeDom.html(grade);
        }
        var ratioDom = $("#$ELEARNING_DOM_ID_RESULT_RATIO" + elearningResultId);
        if (ratioDom) {
          ratioDom.html(nbCorrectAnswers);
        }
        var ratioDom = $("#$ELEARNING_DOM_ID_RESULT_RATIO$ELEARNING_DOM_ID_READING" + elearningResultId);
        if (ratioDom) {
          ratioDom.html(nbCorrectReadingAnswers);
        }
        var ratioDom = $("#$ELEARNING_DOM_ID_RESULT_RATIO$ELEARNING_DOM_ID_WRITING" + elearningResultId);
        if (ratioDom) {
          ratioDom.html(nbCorrectWritingAnswers);
        }
        var ratioDom = $("#$ELEARNING_DOM_ID_RESULT_RATIO$ELEARNING_DOM_ID_LISTENING" + elearningResultId);
        if (ratioDom) {
          ratioDom.html(nbCorrectListeningAnswers);
        }
        var answersDom = $("#$ELEARNING_DOM_ID_RESULT_ANSWER" + elearningResultId);
        if (answersDom) {
          answersDom.find('#result_correct_answers').html(nbCorrectAnswers);
          answersDom.find('#result_incorrect_answers').html(nbIncorrectAnswers);
        }
        var answersDom = $("#$ELEARNING_DOM_ID_RESULT_ANSWER$ELEARNING_DOM_ID_READING" + elearningResultId);
        if (answersDom) {
          answersDom.find('#result_correct_answers').html(nbCorrectReadingAnswers);
          answersDom.find('#result_incorrect_answers').html(nbIncorrectReadingAnswers);
        }
        var answersDom = $("#$ELEARNING_DOM_ID_RESULT_ANSWER$ELEARNING_DOM_ID_WRITING" + elearningResultId);
        if (answersDom) {
          answersDom.find('#result_correct_answers').html(nbCorrectWritingAnswers);
          answersDom.find('#result_incorrect_answers').html(nbIncorrectWritingAnswers);
        }
        var answersDom = $("#$ELEARNING_DOM_ID_RESULT_ANSWER$ELEARNING_DOM_ID_LISTENING" + elearningResultId);
        if (answersDom) {
          answersDom.find('#result_correct_answers').html(nbCorrectListeningAnswers);
          answersDom.find('#result_incorrect_answers').html(nbIncorrectListeningAnswers);
        }
        var pointsDom = $("#$ELEARNING_DOM_ID_RESULT_POINT" + elearningResultId);
        if (pointsDom) {
          pointsDom.html(nbPoints);
        }
        var ratioDom = $("#$ELEARNING_DOM_ID_RESULT_POINT$ELEARNING_DOM_ID_READING" + elearningResultId);
        if (ratioDom) {
          ratioDom.html(nbReadingPoints);
        }
        var ratioDom = $("#$ELEARNING_DOM_ID_RESULT_POINT$ELEARNING_DOM_ID_WRITING" + elearningResultId);
        if (ratioDom) {
          ratioDom.html(nbWritingPoints);
        }
        var ratioDom = $("#$ELEARNING_DOM_ID_RESULT_POINT$ELEARNING_DOM_ID_LISTENING" + elearningResultId);
        if (ratioDom) {
          ratioDom.html(nbListeningPoints);
        }
        var graphImageDomIdNoAnswer = "$ELEARNING_DOM_ID_NO_ANSWER" + elearningResultId;
        var graphImageDomIdIncorrect = "$ELEARNING_DOM_ID_INCORRECT" + elearningResultId;
        var graphImageDomIdCorrect = "$ELEARNING_DOM_ID_CORRECT" + elearningResultId;
        var preventImageCaching = new Date();
        $("#"+graphImageDomIdNoAnswer).attr("src", graphImageUrlNoAnswer + "&" + preventImageCaching.getTime());
        $("#"+graphImageDomIdIncorrect).attr("src", graphImageUrlIncorrect + "&" + preventImageCaching.getTime());
        $("#"+graphImageDomIdCorrect).attr("src", graphImageUrlCorrect + "&" + preventImageCaching.getTime());
        $("#"+graphImageDomIdNoAnswer).attr("title", graphTitle);
        $("#"+graphImageDomIdIncorrect).attr("title", graphTitle);
        $("#"+graphImageDomIdCorrect).attr("title", graphTitle);
        renderInactiveParticipantImage(subscription);
      } else {
        graphDom.style.display = 'none';
        imageDom.style.display = 'none';
      }
    }
  }
}

var elearningSocket;
$(function() {
  if ('undefined' != typeof io) {
    elearningSocket = io.connect('$gHostname:$NODEJS_SOCKET_PORT/elearning');
    elearningSocket.on('connect', function() {
      elearningSocket.emit('watchLiveResult');
    });

    if ('undefined' != typeof elearningSocket) {
      elearningSocket.on('updateResult', function(data) {
        updateLiveResult(data.elearningResultId);
      });
    }
  }
});

function updateLiveResult(elearningResultId) {
  var url = '$gElearningUrl/result/get_live_result.php?elearningResultId=' + elearningResultId;
  ajaxAsynchronousRequest(url, renderOneLiveResult);
}

function renderInactiveParticipantImage(subscription) {
  var isInactive = subscription.isInactive;
  var completed = subscription.completed;
  var elearningSubscriptionId = subscription.elearningSubscriptionId;
  var elearningExerciseId = subscription.elearningExerciseId;
  var imageDom = document.getElementById("$ELEARNING_DOM_ID_INACTIVE" + elearningSubscriptionId + "_" + elearningExerciseId);
  if (imageDom) {
    if (isInactive) {
      imageDom.style.display = 'inline';
      imageDom.title = "$titleInactive";
      imageDom.src = "$gCommonImagesUrl/$gImageLightOrangeSmallBlink";
    } else if (completed) {
      imageDom.style.display = 'inline';
      imageDom.title = "$titleCompleted";
      imageDom.src = "$gCommonImagesUrl/$gImageLightGreenSmall";
    } else {
      imageDom.style.display = 'none';
      imageDom.title = '';
    }
  }
}

// Stop the refreshing after 15mn as most exercises are done by that time
// This is to avoid a refresh on results pages left opened
var intervalRefresh;
setTimeout(function(){
  clearInterval(intervalRefresh);
}, 900000);
</script>
HEREDOC;

    return($strLiveResultJs);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForList() {
    $str = "\n<div class='elearning_subscription_list'>The list of participants subscriptions"
      . "<div class='elearning_subscription_list_title'>The title of the page</div>"
      . "<div class='elearning_subscription_list_comment'>A text</div>"
      . "</div>";

    return($str);
  }

}

?>
