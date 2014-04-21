<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
$elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
$elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
$elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
$elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");
$elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");
$elearningTeacherId = LibEnv::getEnvHttpPOST("elearningTeacherId");
$duration = LibEnv::getEnvHttpPOST("duration");

if (!$elearningSubscriptionId) {
  $elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");
}

if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(ELEARNING_SESSION_RESULT_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_RESULT_SEARCH_PATTERN, $searchPattern);
}

if (!$elearningSubscriptionId) {
  $elearningSubscriptionId = LibSession::getSessionValue(ELEARNING_SESSION_SUBSCRIPTION);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_SUBSCRIPTION, $elearningSubscriptionId);
}

if (!$elearningExerciseId) {
  $elearningExerciseId = LibSession::getSessionValue(ELEARNING_SESSION_EXERCISE);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_EXERCISE, $elearningExerciseId);
}

if (!$duration) {
  $duration = LibSession::getSessionValue(ELEARNING_SESSION_DURATION);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_DURATION, $duration);
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
  $elearningClassId = LibSession::getSessionValue(ELEARNING_SESSION_CLASS);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_CLASS, $elearningClassId);
}

if (!$elearningTeacherId) {
  $elearningTeacherId = LibSession::getSessionValue(ELEARNING_SESSION_TEACHER);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_TEACHER, $elearningTeacherId);
}

$searchPattern = LibString::cleanString($searchPattern);

if ($searchPattern) {
  $elearningSubscriptionId = '';
  $elearningExerciseId = '';
  $duration = '';
  $elearningCourseId = '';
  $elearningSessionId = '';
  $elearningClassId = '';
  $elearningTeacherId = '';
  LibSession::putSessionValue(ELEARNING_SESSION_SUBSCRIPTION, '');
  LibSession::putSessionValue(ELEARNING_SESSION_EXERCISE, '');
  LibSession::putSessionValue(ELEARNING_SESSION_DURATION, '');
  LibSession::putSessionValue(ELEARNING_SESSION_COURSE, '');
  LibSession::putSessionValue(ELEARNING_SESSION_SESSION, '');
  LibSession::putSessionValue(ELEARNING_SESSION_CLASS, '');
  LibSession::putSessionValue(ELEARNING_SESSION_TEACHER, '');
} else if ($elearningSubscriptionId > 0) {
  $elearningExerciseId = '';
  $duration = '';
  $elearningSessionId = '';
  $elearningClassId = '';
  $elearningTeacherId = '';
  LibSession::putSessionValue(ELEARNING_SESSION_EXERCISE, '');
  LibSession::putSessionValue(ELEARNING_SESSION_DURATION, '');
  LibSession::putSessionValue(ELEARNING_SESSION_SESSION, '');
  LibSession::putSessionValue(ELEARNING_SESSION_CLASS, '');
  LibSession::putSessionValue(ELEARNING_SESSION_TEACHER, '');
} else if ($elearningExerciseId > 0) {
  $duration = '';
  $elearningCourseId = '';
  $elearningSessionId = '';
  $elearningClassId = '';
  $elearningTeacherId = '';
  LibSession::putSessionValue(ELEARNING_SESSION_DURATION, '');
  LibSession::putSessionValue(ELEARNING_SESSION_COURSE, '');
  LibSession::putSessionValue(ELEARNING_SESSION_SESSION, '');
  LibSession::putSessionValue(ELEARNING_SESSION_CLASS, '');
  LibSession::putSessionValue(ELEARNING_SESSION_TEACHER, '');
} else if ($duration > 0) {
  $elearningCourseId = '';
  $elearningSessionId = '';
  $elearningClassId = '';
  $elearningTeacherId = '';
  LibSession::putSessionValue(ELEARNING_SESSION_COURSE, '');
  LibSession::putSessionValue(ELEARNING_SESSION_SESSION, '');
  LibSession::putSessionValue(ELEARNING_SESSION_CLASS, '');
  LibSession::putSessionValue(ELEARNING_SESSION_TEACHER, '');
}

$durationList = array(
  '-1' => '',
  7 => $mlText[43],
  30 => $mlText[44],
  90 => $mlText[45],
  180 => $mlText[46],
  365 => $mlText[47],
);
$strSelectRelease = LibHtml::getSelectList("duration", $durationList, $duration, true);

$systemDate = $clockUtils->getSystemDate();

$sinceDate = '';
if ($duration > 0 && !$elearningSessionId) {
  $sinceDate = $clockUtils->incrementDays($systemDate, -1 * $duration);
}

$participantName = '';
if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
  if ($elearningSubscriptionUtils->isClosed($elearningSubscription)) {
    array_push($warnings, $mlText[2]);
  }
  $userId = $elearningSubscription->getUserId();
  if ($user = $userUtils->selectById($userId)) {
    $participantName = $user->getFirstname() . ' ' . $user->getLastname();
  }
}

$exerciseName = '';
if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $exerciseName = $elearningExercise->getName();
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

$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");

$panelUtils->openForm($PHP_SELF);
$label = $popupUtils->getTipPopup($mlText[70], $mlText[71], 300, 300);
$strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . $panelUtils->getTinyOk()
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "</form>";

$resultGradeScale = $elearningExerciseUtils->resultGradeScale();

$strLiveResultJs = $elearningResultUtils->renderLiveResultJs();
$panelUtils->addContent($strLiveResultJs);

$totalCorrectAnswers = 0;
$totalIncorrectAnswers = 0;
$totalQuestions = 0;
$totalPoints = 0;
$generalTotalCorrectAnswers = 0;
$generalTotalQuestions = 0;
$generalTotalPoints = 0;

$preferenceUtils->init($elearningExerciseUtils->preferences);
$listStep = $preferenceUtils->getValue("ELEARNING_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $elearningResults = $elearningResultUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else if ($elearningSubscriptionId > 0 && $elearningCourseId > 0) {
  $elearningResults = $elearningResultUtils->selectBySubscriptionIdAndCourseId($elearningSubscriptionId, $elearningCourseId, $listIndex, $listStep);
} else if ($elearningSubscriptionId > 0) {
  $elearningResults = $elearningResultUtils->selectBySubscriptionId($elearningSubscriptionId, $listIndex, $listStep);
} else if ($elearningExerciseId> 0) {
  $elearningResults = $elearningResultUtils->selectByExerciseId($elearningExerciseId, $listIndex, $listStep);
} else if ($sinceDate) {
  $elearningResults = $elearningResultUtils->selectByReleaseDate($sinceDate, $listIndex, $listStep);
} else if ($elearningCourseId > 0 && $elearningSessionId > 0 && $elearningClassId > 0 && $elearningTeacherId > 0) {
  $elearningResults = $elearningResultUtils->selectBySessionIdAndCourseIdAndClassIdAndTeacherId($elearningSessionId, $elearningCourseId, $elearningClassId, $elearningTeacherId, $listIndex, $listStep);
} else if ($elearningSessionId > 0 && $elearningClassId > 0 && $elearningTeacherId > 0) {
  $elearningResults = $elearningResultUtils->selectBySessionIdAndClassIdAndTeacherId($elearningSessionId, $elearningClassId, $elearningTeacherId, $listIndex, $listStep);
} else if ($elearningCourseId > 0 && $elearningClassId > 0 && $elearningTeacherId > 0) {
  $elearningResults = $elearningResultUtils->selectByCourseIdAndClassIdAndTeacherId($elearningCourseId, $elearningClassId, $elearningTeacherId, $listIndex, $listStep);
} else if ($elearningCourseId > 0 && $elearningSessionId > 0 && $elearningTeacherId > 0) {
  $elearningResults = $elearningResultUtils->selectBySessionIdAndCourseIdAndTeacherId($elearningSessionId, $elearningCourseId, $elearningTeacherId, $listIndex, $listStep);
} else if ($elearningCourseId > 0 && $elearningSessionId > 0 && $elearningClassId > 0) {
  $elearningResults = $elearningResultUtils->selectBySessionIdAndCourseIdAndClassId($elearningSessionId, $elearningCourseId, $elearningClassId, $listIndex, $listStep);
} else if ($elearningSessionId > 0 && $elearningCourseId > 0) {
  $elearningResults = $elearningResultUtils->selectBySessionIdAndCourseId($elearningSessionId, $elearningCourseId, $listIndex, $listStep);
} else if ($elearningSessionId > 0 && $elearningClassId > 0) {
  $elearningResults = $elearningResultUtils->selectBySessionIdAndClassId($elearningSessionId, $elearningClassId, $listIndex, $listStep);
} else if ($elearningSessionId > 0 && $elearningTeacherId > 0) {
  $elearningResults = $elearningResultUtils->selectBySessionIdAndTeacherId($elearningSessionId, $elearningTeacherId, $listIndex, $listStep);
} else if ($elearningCourseId > 0 && $elearningClassId > 0) {
  $elearningResults = $elearningResultUtils->selectByCourseIdAndClassId($elearningCourseId, $elearningClassId, $listIndex, $listStep);
} else if ($elearningCourseId > 0 && $elearningTeacherId > 0) {
  $elearningResults = $elearningResultUtils->selectByCourseIdAndTeacherId($elearningCourseId, $elearningTeacherId, $listIndex, $listStep);
} else if ($elearningCourseId > 0) {
  $elearningResults = $elearningResultUtils->selectByCourseId($elearningCourseId, $listIndex, $listStep);
} else if ($elearningClassId > 0) {
  $elearningResults = $elearningResultUtils->selectByClassId($elearningClassId, $listIndex, $listStep);
} else if ($elearningSessionId > 0) {
  $elearningResults = $elearningResultUtils->selectBySessionId($elearningSessionId, $listIndex, $listStep);
} else if ($elearningTeacherId > 0) {
  $elearningResults = $elearningResultUtils->selectByTeacherId($elearningTeacherId, $listIndex, $listStep);
} else {
  $elearningResults = $elearningResultUtils->selectNonSubscriptions($listIndex, $listStep);
}

$strCommand = '';
if ($elearningSubscriptionId > 0) {
  if (count($elearningResults) > 0) {
    $strCommand .= " <a href=\"javascript: $('#resultsGraph').slideToggle('fast'); void(0);\">"
      . "<img src='$gCommonImagesUrl/$gImageGraph' class='no_style_image_icon' title='$mlText[37]' alt='' style='vertical-align:middle;' /></a>";
  }
}
$strCommand .= " <a href='$gElearningUrl/result/range/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageResultRange' title='$mlText[19]'></a>";

$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell($strSearch, "nb"), '', '', '', '', $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[12], "nb"), $panelUtils->addCell($mlText[9], "nb"), $panelUtils->addCell($mlText[8], "nb"), '', '', '', '');
$strJsSuggest = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/subscription/suggest.php", "participantName", "elearningSubscriptionId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningSubscriptionId', $elearningSubscriptionId);
$strJsSuggest = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/exercise/suggestExercises.php", "exerciseName", "elearningExerciseId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
$panelUtils->addLine($panelUtils->addCell("<input type='text' id='participantName' name='participantName' value='$participantName' /> " . $panelUtils->getTinyOk(), "n"), $panelUtils->addCell("<input type='text' id='exerciseName' name='exerciseName' value='$exerciseName' /> " . $panelUtils->getTinyOk(), "n"), $panelUtils->addCell($strSelectRelease, "n"), '', '', '', '');
$panelUtils->addLine($panelUtils->addCell($mlText[23], "nb"), $panelUtils->addCell($mlText[11], "nb"), '', '', '', '', '');
$strJsSuggestCourse = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/course/suggest.php", "courseName", "elearningCourseId");
$panelUtils->addContent($strJsSuggestCourse);
$panelUtils->addHiddenField('elearningCourseId', $elearningCourseId);
$strJsSuggestSession = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/session/suggest.php", "sessionName", "elearningSessionId");
$panelUtils->addContent($strJsSuggestSession);
$panelUtils->addHiddenField('elearningSessionId', $elearningSessionId);
$panelUtils->addLine($panelUtils->addCell("<input type='text' id='courseName' name='courseName' value='$courseName' /> " . $panelUtils->getTinyOk(), "n"), $panelUtils->addCell("<input type='text' id='sessionName' name='sessionName' value='$sessionName' /> " . $panelUtils->getTinyOk(), "n"), '', '', '', '', '');
$panelUtils->addLine($panelUtils->addCell($mlText[24], "nb"), $panelUtils->addCell($mlText[7], "nb"), '', '', '', '', '');
$strJsSuggestClass = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/class/suggest.php", "className", "elearningClassId");
$panelUtils->addContent($strJsSuggestClass);
$panelUtils->addHiddenField('elearningClassId', $elearningClassId);
$strSuggestTeacher = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/teacher/suggest.php", "teacherName", "elearningTeacherId");
$panelUtils->addContent($strSuggestTeacher);
$panelUtils->addHiddenField('elearningTeacherId', $elearningTeacherId);
$panelUtils->addLine($panelUtils->addCell("<input type='text' id='className' name='className' value='$className' /> " . $panelUtils->getTinyOk(), "n"), "<input type='text' id='teacherName' value='$teacherName' /> " . $panelUtils->getTinyOk(), '', '', '', '', '');
$panelUtils->closeForm();

$strCommand = '';
if ($elearningSubscriptionId > 0) {
  $strCommand .= " <a href='$gElearningUrl/subscription/view.php?elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCourse' title='$mlText[33]'></a>"
    . " <a href='$gElearningUrl/result/deleteSubscriptionAll.php?elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[10]'></a>";
} else if ($elearningExerciseId > 0) {
  $strCommand .= " <a href='$gElearningUrl/result/deleteExerciseAll.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[2]'></a>";
}

if ($elearningSubscriptionId > 0 && count($elearningResults) > 0) {
  $elearningExerciseIds = array();
  foreach ($elearningResults as $elearningResult) {
    $wElearningExerciseId = $elearningResult->getElearningExerciseId();
    array_push($elearningExerciseIds, $wElearningExerciseId);
  }
  $resultsGraph = "<div id='resultsGraph' style='display: none;'><br />" . $elearningResultUtils->renderSubscriptionResultsGraph($elearningSubscriptionId, $elearningExerciseIds) . "</div>";
  $panelUtils->addLine($panelUtils->addCell($resultsGraph, ""));
}

$panelUtils->addLine();
$labelGrade = $popupUtils->getTipPopup($mlText[1], $mlText[15], 300, 300);
$labelRatio = $popupUtils->getTipPopup($mlText[30], $mlText[34], 300, 300);
$labelAnswers = $popupUtils->getTipPopup($mlText[35], $mlText[36], 300, 300);
$labelPoints = $popupUtils->getTipPopup($mlText[27], $mlText[29], 300, 300);
$panelUtils->addLine($panelUtils->addCell($mlText[31], "nb"), $panelUtils->addCell($mlText[17], "nb"), $panelUtils->addCell($labelGrade, "nbc"), $panelUtils->addCell($labelRatio, "nbc"), $panelUtils->addCell($labelAnswers, "nbc"), $panelUtils->addCell($labelPoints, "nbc"), $panelUtils->addCell($strCommand, "nbr"));

$listNbItems = $elearningExerciseUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($elearningResults as $elearningResult) {
  $elearningResultId = $elearningResult->getId();
  $previousSubscriptionId = $elearningSubscriptionId;
  $elearningSubscriptionId = $elearningResult->getSubscriptionId();
  if ($previousSubscriptionId != $elearningSubscriptionId && $previousSubscriptionId > 0) {
    if ($elearningClassId > 0 || $elearningCourseId > 0 && $elearningExerciseId < 1) {
      $averageCorrectAnswers = $elearningResultUtils->calculateAverageCorrectAnswers($totalCorrectAnswers, $totalQuestions);
      $labelAverageResult = $popupUtils->getTipPopup($mlText[18], $mlText[21], 300, 200);
      $generalGrade = $elearningResultRangeUtils->calculateGrade($totalCorrectAnswers, $totalQuestions);
      $strResultGrades = $elearningResultUtils->renderResultGrades('', $generalGrade);
      $strResultRatio = $elearningResultUtils->renderResultRatio('', $averageCorrectAnswers, $resultGradeScale);
      $strResultPoints = $elearningResultUtils->renderResultPoints('', $totalPoints);
      $panelUtils->addLine('', '', $panelUtils->addCell($labelAverageResult, "nbr"), $panelUtils->addCell($strResultGrades, "nc"), $panelUtils->addCell($strResultRatio, "nc"), '', $panelUtils->addCell($strResultPoints, "nc"), '');
      $panelUtils->addLine();
    }

    $totalCorrectAnswers = 0;
    $totalIncorrectAnswers = 0;
    $totalQuestions = 0;
    $totalPoints = 0;
  }

  $elearningResultId = $elearningResult->getId();
  $exerciseId = $elearningResult->getElearningExerciseId();
  $exerciseDate = $clockUtils->systemToLocalNumericDate($elearningResult->getExerciseDate());
  $firstname = $elearningResult->getFirstname();
  $lastname = $elearningResult->getLastname();
  $email = $elearningResult->getEmail();
  if ($firstname || $lastname) {
    $name = $firstname . ' ' . $lastname;
  } else {
    $name = $email;
  }
  $strName = "<span title='$mlText[22]'>$name</span>";

  $strClass = '';
  $strCourse = '';
  $strExercise = '';
  $subscriptionDate = '';
  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    $userId = $elearningSubscription->getUserId();
    $sessionId = $elearningSubscription->getSessionId();
    $courseId = $elearningSubscription->getCourseId();
    $classId = $elearningSubscription->getClassId();
    $subscriptionDate = $elearningSubscription->getSubscriptionDate();
    $subscriptionDate = $clockUtils->systemToLocalNumericDate($subscriptionDate);

    if ($user = $userUtils->selectById($userId)) {
      $firstname = $user->getFirstname();
      $lastname = $user->getLastname();
      $email = $user->getEmail();
      $name = $firstname . ' ' . $lastname;
    } else if ($firstname || $lastname) {
      $name = $firstname . ' ' . $lastname;
    } else {
      $name = $email;
    }
    $strName = "<span title='$mlText[20] $subscriptionDate'>$name</span>";

    if ($elearningCourse = $elearningCourseUtils->selectById($courseId)) {
      $strCourse = $elearningCourse->getName();
    }

    if ($elearningClass = $elearningClassUtils->selectById($classId)) {
      $strClass = $elearningClass->getName();
    }
  }

  if ($elearningExercise = $elearningExerciseUtils->selectById($exerciseId)) {
    $name = $elearningExercise->getName();
    $strExercise = "<span title='$mlText[16] $exerciseDate'>$name</span>";
  }

  $resultTotals = $elearningResultUtils->getExerciseTotals($exerciseId, $elearningResultId);
  $nbQuestions = $elearningResultUtils->getResultNbQuestions($resultTotals);
  $nbCorrectAnswers = $elearningResultUtils->getResultNbCorrectAnswers($resultTotals);
  $nbIncorrectAnswers = $elearningResultUtils->getResultNbIncorrectAnswers($resultTotals);
  $points = $elearningResultUtils->getResultNbPoints($resultTotals);
  $grade = $elearningResultRangeUtils->calculateGrade($nbCorrectAnswers, $nbQuestions);

  if (($elearningSubscriptionId > 0 || $elearningClassId > 0 || $elearningCourseId > 0) && $elearningExerciseId < 1) {
    $generalTotalCorrectAnswers = $generalTotalCorrectAnswers + $nbCorrectAnswers;
    $generalTotalQuestions = $generalTotalQuestions + $nbQuestions;
    $generalTotalPoints = $generalTotalPoints + $points;
    if ($elearningClassId > 0 || $elearningCourseId > 0) {
      $totalCorrectAnswers = $totalCorrectAnswers + $nbCorrectAnswers;
      $totalIncorrectAnswers = $totalIncorrectAnswers + $nbIncorrectAnswers;
      $totalQuestions = $totalQuestions + $nbQuestions;
      $totalPoints = $totalPoints + $points;
    }
  }

  $strCommand = ''
    . " <a href='$gElearningUrl/result/view.php?elearningResultId=$elearningResultId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCheckList' title='$mlText[13]'></a>"
    . " <a href='$gElearningUrl/result/comment.php?elearningResultId=$elearningResultId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageNote' title='$mlText[14]'></a>"
    . " <a href='$gElearningUrl/result/send_by_admin.php?elearningResultId=$elearningResultId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[28]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$mlText[6]'>", "$gElearningUrl/result/adminPrint.php?elearningResultId=$elearningResultId", 600, 600)
    . " <a href='$gElearningUrl/result/delete.php?elearningResultId=$elearningResultId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $strResultGrades = $elearningResultUtils->renderResultGrades('', $grade);
  $strResultRatio = $elearningResultUtils->renderResultRatio('', $nbCorrectAnswers, $nbQuestions);
  $strResultAnswers = $elearningResultUtils->renderResultAnswers('', $nbCorrectAnswers, $nbIncorrectAnswers);
  $strResultPoints = $elearningResultUtils->renderResultPoints('', $points);
  $panelUtils->addLine($strName, $strExercise, $panelUtils->addCell($strResultGrades, "nc"), $panelUtils->addCell($strResultRatio, "nc"), $panelUtils->addCell($strResultAnswers, "nc"), $panelUtils->addCell($strResultPoints, "nc"), $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

if (count($elearningResults) > 0) {
  if (($elearningSubscriptionId > 0 || $elearningClassId > 0 || $elearningCourseId > 0) && $elearningExerciseId < 1) {
    if ($elearningClassId > 0 || $elearningCourseId > 0) {
      $averageCorrectAnswers = $elearningResultUtils->calculateAverageCorrectAnswers($totalCorrectAnswers, $totalQuestions);
      $grade = $elearningResultRangeUtils->calculateGrade($totalCorrectAnswers, $totalQuestions);
      $strResultGrades = $elearningResultUtils->renderResultGrades('', $grade);
      $strResultRatio = $elearningResultUtils->renderResultRatio('', $averageCorrectAnswers, $resultGradeScale);
      $strResultPoints = $elearningResultUtils->renderResultPoints('', $totalPoints);
      $label = $popupUtils->getTipPopup($mlText[18], $mlText[21], 300, 200);
      $panelUtils->addLine('', $panelUtils->addCell($label, "nbr"), $panelUtils->addCell($strResultGrades, "nc"), $panelUtils->addCell($strResultRatio, "nc"), '', $panelUtils->addCell($strResultPoints, "nc"), '');
    }

    $averageCorrectAnswers = $elearningResultUtils->calculateAverageCorrectAnswers($generalTotalCorrectAnswers, $generalTotalQuestions);
    $grade = $elearningResultRangeUtils->calculateGrade($generalTotalCorrectAnswers, $generalTotalQuestions);
    $strResultGrades = $elearningResultUtils->renderResultGrades('', $grade);
    $strResultRatio = $elearningResultUtils->renderResultRatio('', $averageCorrectAnswers, $resultGradeScale);
    $strResultPoints = $elearningResultUtils->renderResultPoints('', $generalTotalPoints);
    $label = $popupUtils->getTipPopup($mlText[26], $mlText[21], 300, 200);
    $panelUtils->addLine();
    $panelUtils->addLine('', $panelUtils->addCell($label, "nbr"), $panelUtils->addCell($strResultGrades, "nc"), $panelUtils->addCell($strResultRatio, "nc"), '', $panelUtils->addCell($strResultPoints, "nc"), '');
  }
}

$strRememberScroll = LibJavaScript::rememberScroll("elearning_result_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
