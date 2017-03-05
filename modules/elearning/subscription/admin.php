<?php

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
$elearningTeacherId = LibEnv::getEnvHttpPOST("elearningTeacherId");
$elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
$elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");
$elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");

if (!$elearningTeacherId) {
  $elearningTeacherId = LibSession::getSessionValue(ELEARNING_SESSION_TEACHER);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_TEACHER, $elearningTeacherId);
}

if (!$elearningCourseId) {
  $elearningCourseId = LibSession::getSessionValue(ELEARNING_SESSION_COURSE);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_COURSE, $elearningCourseId);
}

if (!$elearningSessionId) {
  $elearningSessionId = LibSession::getSessionValue(ELEARNING_SESSION_SESSION);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_SESSION, $elearningSessionId);
}

if (!$elearningClassId) {
  $elearningClassId = LibEnv::getEnvHttpGET("elearningClassId");
  if (!$elearningClassId) {
    $elearningClassId = LibSession::getSessionValue(ELEARNING_SESSION_CLASS);
  }
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_CLASS, $elearningClassId);
}

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(ELEARNING_SESSION_SUBSCRIPTION_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_SUBSCRIPTION_SEARCH_PATTERN, $searchPattern);
}

$searchPattern = LibString::cleanString($searchPattern);

if ($searchPattern) {
  $elearningTeacherId = '';
  $elearningSessionId = '';
  $elearningCourseId = '';
  $elearningClassId = '';
  LibSession::putSessionValue(ELEARNING_SESSION_TEACHER, '');
  LibSession::putSessionValue(ELEARNING_SESSION_COURSE, '');
  LibSession::putSessionValue(ELEARNING_SESSION_SESSION, '');
  LibSession::putSessionValue(ELEARNING_SESSION_CLASS, '');
}

$courseName = '';
if ($course = $elearningCourseUtils->selectById($elearningCourseId)) {
  $courseName = $course->getName();
}

$sessionName = '';
if ($elearningSession = $elearningSessionUtils->selectById($elearningSessionId)) {
  $sessionName = $elearningSession->getName();
}

$className = '';
if ($class = $elearningClassUtils->selectById($elearningClassId)) {
  $className = $class->getName();
}

$teacherName = '';
if ($elearningTeacher = $elearningTeacherUtils->selectById($elearningTeacherId)) {
  $teacherUserId = $elearningTeacher->getUserId();
  if ($teacherUser = $userUtils->selectById($teacherUserId)) {
    $teacherName = $teacherUser->getFirstname() . ' ' . $teacherUser->getLastname();
  }
}

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[29], 300, 500);
$panelUtils->setHelp($help);

$labelSearch = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . $panelUtils->getTinyOk()
  . "</form>";

$strCommand = ''
  . " <a href='$gElearningUrl/assignment/results.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageClassAssignment' title='$mlText[36]'></a>"
  . " <a href='$gElearningUrl/result/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCheckList' title='$mlText[38]'></a>"
  . " <a href='$gElearningUrl/lesson/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageLesson' title='$mlText[53]'></a>"
  . " <a href='$gElearningUrl/exercise/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageExercise' title='$mlText[22]'></a>"
  . " <a href='$gElearningUrl/course/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCourse' title='$mlText[37]'></a>"
  . " <a href='$gElearningUrl/session/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSession' title='$mlText[34]'></a>"
  . " <a href='$gElearningUrl/class/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImagePeople' title='$mlText[32]'></a>";

$adminLogin = $adminUtils->checkAdminLogin();
if ($adminUtils->isSuperAdmin($adminLogin)) {
  $strCommand .= ''
    . " <a href='$gElearningUrl/teacher/admin.php' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImagePerson' title='$mlText[18]'></a>";
}

$strCommand .= ''
  . " <a href='$gElearningUrl/result/range/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageResultRange' title='$mlText[42]'></a>"
  . " <a href='$gElearningUrl/scoring/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageScoring' title='$mlText[27]'></a>"
  . " <a href='$gElearningUrl/exercise/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[49]'></a>";

$panelUtils->addLine($panelUtils->addCell($labelSearch, "nbr"), $panelUtils->addCell($strSearch, "n"), '', '', $panelUtils->addCell($strCommand, "nr"));

if ($elearningSubscriptionId || $elearningClassId) {
  $strCommand = " <a href=\"javascript: toggleParticipantWhiteboard(); void(0);\">"
    . "<img src='$gCommonImagesUrl/$gImageWhiteboard' class='no_style_image_icon' title='$mlText[43]' alt='' style='vertical-align:middle;' /></a>";
  $panelUtils->addLine("", "", '', '', $panelUtils->addCell($strCommand, "nr"));
}

$listStep = $preferenceUtils->getValue("ELEARNING_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

$elearningSubscriptions = array();
if ($searchPattern) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else if ($elearningSessionId > 0 && $elearningCourseId > 0 && $elearningClassId > 0 && $elearningTeacherId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectBySessionIdAndCourseAndClassIdAndTeacherId($elearningSessionId, $elearningCourseId, $elearningClassId, $elearningTeacherId, $listIndex, $listStep);
} else if ($elearningCourseId > 0 && $elearningClassId > 0 && $elearningTeacherId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectByCourseIdAndClassIdAndTeacherId($elearningCourseId, $elearningClassId, $elearningTeacherId, $listIndex, $listStep);
} else if ($elearningSessionId > 0 && $elearningClassId > 0 && $elearningTeacherId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectBySessionIdAndClassIdAndTeacherId($elearningSessionId, $elearningClassId, $elearningTeacherId, $listIndex, $listStep);
} else if ($elearningSessionId > 0 && $elearningCourseId > 0 && $elearningTeacherId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectBySessionIdAndCourseAndTeacherId($elearningSessionId, $elearningCourseId, $elearningTeacherId, $listIndex, $listStep);
} else if ($elearningSessionId > 0 && $elearningCourseId > 0 && $elearningClassId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectBySessionIdAndCourseIdAndClassId($elearningSessionId, $elearningCourseId, $elearningClassId, $listIndex, $listStep);
} else if ($elearningSessionId > 0 && $elearningCourseId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectBySessionIdAndCourseId($elearningSessionId, $elearningCourseId, $listIndex, $listStep);
} else if ($elearningSessionId > 0 && $elearningClassId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectBySessionIdAndClassId($elearningSessionId, $elearningClassId, $listIndex, $listStep);
} else if ($elearningCourseId > 0 && $elearningTeacherId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectByCourseIdAndTeacherId($elearningCourseId, $elearningTeacherId, $listIndex, $listStep);
} else if ($elearningClassId > 0 && $elearningTeacherId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectByClassIdAndTeacherId($elearningClassId, $elearningTeacherId, $listIndex, $listStep);
} else if ($elearningSessionId > 0 && $elearningTeacherId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectBySessionIdAndTeacherId($elearningSessionId, $elearningTeacherId, $listIndex, $listStep);
} else if ($elearningSessionId == -1) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectByNoSessionId($listIndex, $listStep);
} else if ($elearningSessionId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectBySessionId($elearningSessionId, $listIndex, $listStep);
} else if ($elearningCourseId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectByCourseId($elearningCourseId, $listIndex, $listStep);
} else if ($elearningClassId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectByClassId($elearningClassId, $listIndex, $listStep);
} else if ($elearningTeacherId > 0) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectByTeacherId($elearningTeacherId, $listIndex, $listStep);
} else if (!$preferenceUtils->getValue("ELEARNING_LIST_DEFAULT_EMPTY")) {
  $elearningSubscriptions = $elearningSubscriptionUtils->selectAll($listIndex, $listStep);
} else {
  $elearningSubscriptions = array();
}

if ($elearningSubscriptionId || $elearningClassId) {
  $strWhiteboard = $elearningExerciseUtils->renderWhiteboard($elearningSubscriptionId, $elearningClassId);
  $panelUtils->addLine($panelUtils->addCell($strWhiteboard, ""));
}

$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[11], "nb"), $panelUtils->addCell($mlText[12], "nb"), '', '', '');
$strJsSuggestCourse = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/course/suggest.php", "courseName", "elearningCourseId");
$panelUtils->addContent($strJsSuggestCourse);
$panelUtils->addHiddenField('elearningCourseId', $elearningCourseId);
$strJsSuggestSession = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/session/suggest.php", "sessionName", "elearningSessionId");
$panelUtils->addContent($strJsSuggestSession);
$panelUtils->addHiddenField('elearningSessionId', $elearningSessionId);
$panelUtils->addLine($panelUtils->addCell("<input type='text' id='courseName' name='$courseName' value='$courseName' /> " . $panelUtils->getTinyOk(), "n"), $panelUtils->addCell("<input type='text' id='sessionName' name='$sessionName' value='$sessionName' /> " . $panelUtils->getTinyOk(), "n"), '', '', '');
$panelUtils->addLine($panelUtils->addCell($mlText[14], "nb"), $panelUtils->addCell($mlText[33], "nb"), '', '', '');
$strJsSuggestClass = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/class/suggest.php", "className", "elearningClassId");
$panelUtils->addContent($strJsSuggestClass);
$panelUtils->addHiddenField('elearningClassId', $elearningClassId);
$strSuggestTeacher = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/teacher/suggest.php", "teacherName", "elearningTeacherId");
$panelUtils->addContent($strSuggestTeacher);
$panelUtils->addHiddenField('elearningTeacherId', $elearningTeacherId);
$panelUtils->addLine($panelUtils->addCell("<input type='text' id='className' name='$className' value='$className' /> " . $panelUtils->getTinyOk(), "n"), $panelUtils->addCell("<input type='text' id='teacherName' value='$teacherName' /> " . $panelUtils->getTinyOk(), "n"), '', '', '');
$panelUtils->closeForm();
$panelUtils->addLine();

$strCommand = "<a href='$gElearningUrl/subscription/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
if ($elearningClassId) {
  $strCommand .= " <a href='$gElearningUrl/subscription/send.php?elearningSessionId=$elearningSessionId&elearningClassId=$elearningClassId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[20]'></a>"
    . " <a href='$gElearningUrl/subscription/sms.php?elearningSessionId=$elearningSessionId&elearningClassId=$elearningClassId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageSms' title='$mlText[23]'></a>";
}

$strLiveResultJs = $elearningResultUtils->renderLiveResultJs();
$panelUtils->addContent($strLiveResultJs);

$labelLastExercise = $popupUtils->getTipPopup($mlText[5], $mlText[26], 300, 300);
$labelLiveResults = $popupUtils->getTipPopup($mlText[17], $mlText[35], 300, 300);
$labelNextExercise = $popupUtils->getTipPopup($mlText[13], $mlText[39], 300, 300);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($labelLiveResults, "nb"), $panelUtils->addCell($labelLastExercise, "nb"), $panelUtils->addCell($labelNextExercise, "nb"), $panelUtils->addCell($strCommand, "nr"));

$listNbItems = $elearningSubscriptionUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($elearningSubscriptions as $elearningSubscription) {
  $elearningSubscriptionId = $elearningSubscription->getId();
  $userId = $elearningSubscription->getUserId();
  $teacherId = $elearningSubscription->getTeacherId();
  $sessionId = $elearningSubscription->getSessionId();
  $courseId = $elearningSubscription->getCourseId();
  $classId = $elearningSubscription->getClassId();
  $subscriptionDate = $elearningSubscription->getSubscriptionDate();
  $subscriptionDate = $clockUtils->systemToLocalNumericDate($subscriptionDate);
  $lastExerciseId = $elearningSubscription->getLastExerciseId();
  $watchLive = $elearningSubscription->getWatchLive();

  $sessionName = '';
  $sessionDate = '';
  if ($elearningSession = $elearningSessionUtils->selectById($sessionId)) {
    $sessionOpenDate = $elearningSession->getOpenDate();
    $sessionCloseDate = $elearningSession->getCloseDate();
    $sessionOpenDate = $clockUtils->systemToLocalNumericDate($sessionOpenDate);
    $sessionCloseDate = $clockUtils->systemToLocalNumericDate($sessionCloseDate);
    $sessionDate = $mlText[9] . ' ' . $sessionOpenDate;
    if ($sessionCloseDate) {
      $sessionDate .= ' ' . $mlText[21] . ' ' . $sessionCloseDate;
    }
    $sessionName = "<span title='$sessionDate'>" . $elearningSession->getName() . '</span>';
  }

  $courseName = '';
  if ($elearningCourse = $elearningCourseUtils->selectById($courseId)) {
    $courseName = $elearningCourse->getName();
  }

  $className = '';
  if ($elearningClass = $elearningClassUtils->selectById($classId)) {
    $className = $elearningClass->getName();
  }

  $teacherName = '';
  if ($teacher = $elearningTeacherUtils->selectById($teacherId)) {
    $firstname = $elearningTeacherUtils->getFirstname($teacherId);
    $lastname = $elearningTeacherUtils->getLastname($teacherId);
    $teacherName = $firstname . ' ' . $lastname;
  }

  $firstname = '';
  $lastname = '';
  $email = '';
  if ($user = $userUtils->selectById($userId)) {
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();
    $email = $user->getEmail();
  }

  $lastExerciseName = $elearningExerciseUtils->renderExerciseComposeLink($lastExerciseId, $mlText[16]);

  $strCommand = " <a href='$gElearningUrl/assignment/admin.php?elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAssignment' title='$mlText[31]'></a>";
  if ($courseName) {
    $title = $mlText[40] . ' ' . $courseName;
    $strCommand .= " <a href='$gElearningUrl/subscription/view.php?elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageCourse' title='$title'></a>";
  }

  $strCommand .= ''
    . " <a href='$gElearningUrl/result/admin.php?elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCheckList' title='$mlText[8]'></a>"
    . " <a href='$gElearningUrl/subscription/edit.php?elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gElearningUrl/subscription/end.php?elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageFinish' title='$mlText[10]'></a>"
    . " <a href='$gElearningUrl/subscription/send.php?elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[19]'></a>";
  if ($elearningSubscriptionUtils->hasSmsSubscription($elearningSubscriptionId)) {
    $strCommand .= ''
      . " <a href='$gElearningUrl/subscription/sms.php?elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageSms' title='$mlText[24]'></a>";
  }

  $strCommand .= " <a href='$gElearningUrl/subscription/delete.php?elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $strName = "<span title='$mlText[28] $subscriptionDate'>" . $firstname . ' ' . $lastname . '</span>';

  $strName = "<a href='mailto:$email'>$strName</a>";

  $nextExerciseName = '';
  $nextExerciseId = $elearningSubscriptionUtils->getNextExercise($elearningSubscription);
  if ($nextElearningExercise = $elearningExerciseUtils->selectById($nextExerciseId)) {
    $nextExerciseName = $elearningExerciseUtils->renderExerciseComposeLink($nextExerciseId, $mlText[16]);
  }

  $strLiveResults = '';
  if ($watchLive) {
    if ($elearningResult = $elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $lastExerciseId)) {
      $elearningResultId = $elearningResult->getId();
      if ($elearningExercise = $elearningExerciseUtils->selectById($lastExerciseId)) {
        $elearningExerciseId = $elearningExercise->getId();
        $exerciseName = $elearningExercise->getName();
        $strExerciseName = "<a href='$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$lastExerciseId&elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus title='" . $mlText[41] . "' target='_blank'>" . $exerciseName . " <img src='$gCommonImagesUrl/$gImageExercise' class='no_style_image_icon' title='' alt='' style='vertical-align:middle;' />" . "</a>";
        $resultTotals = $elearningResultUtils->getExerciseTotals($lastExerciseId, $elearningResultId);
        $nbQuestions = $elearningResultUtils->getResultNbQuestions($resultTotals);
        $nbIncorrectAnswers = $elearningResultUtils->getResultNbIncorrectAnswers($resultTotals);
        $nbCorrectAnswers = $elearningResultUtils->getResultNbCorrectAnswers($resultTotals);
        $nbAnswers = $nbCorrectAnswers + $nbIncorrectAnswers;
        $strLiveResults = $elearningResultUtils->renderExerciseResultsGraph($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true, true, '')
          . " <img id='" . ELEARNING_DOM_ID_INACTIVE . $elearningSubscriptionId . "_" . $lastExerciseId . "' src='$gCommonImagesUrl/$gImageLightOrangeSmallBlink' title='' alt='' style='display: none;' />";
      }
    }
  }

  $panelUtils->addLine($panelUtils->addCell($strName, 'n'), $panelUtils->addCell($strLiveResults, 'nm'), $panelUtils->addCell($lastExerciseName, 'n'), $panelUtils->addCell($nextExerciseName, 'n'), $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("elearning_subscription_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
