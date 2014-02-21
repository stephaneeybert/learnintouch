<?php

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
$elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");
$elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");
$duration = LibEnv::getEnvHttpPOST("duration");

if (!$elearningSubscriptionId) {
  $elearningSubscriptionId = LibSession::getSessionValue(ELEARNING_SESSION_SUBSCRIPTION);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_SUBSCRIPTION, $elearningSubscriptionId);
}

if (!$elearningClassId) {
  $elearningClassId = LibSession::getSessionValue(ELEARNING_SESSION_CLASS);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_CLASS, $elearningClassId);
}

if (!$elearningSessionId) {
  $elearningSessionId = LibSession::getSessionValue(ELEARNING_SESSION_SESSION);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_SESSION, $elearningSessionId);
}

if (!$duration) {
  $duration = LibSession::getSessionValue(ELEARNING_SESSION_DURATION);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_DURATION, $duration);
}

if ($elearningSubscriptionId > 0) {
  $elearningClassId = '';
  $elearningSessionId = '';
  $duration = '';
  LibSession::putSessionValue(ELEARNING_SESSION_CLASS, '');
  LibSession::putSessionValue(ELEARNING_SESSION_SESSION, '');
  LibSession::putSessionValue(ELEARNING_SESSION_DURATION, '');
} else if ($elearningSessionId > 0) {
  $duration = '';
  LibSession::putSessionValue(ELEARNING_SESSION_DURATION, '');
}

$className = '';
if ($class = $elearningClassUtils->selectById($elearningClassId)) {
  $className = $class->getName();
}

$sessionName = '';
if ($elearningSession = $elearningSessionUtils->selectById($elearningSessionId)) {
  $sessionName = $elearningSession->getName();
}

$participantName = '';
if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
  $userId = $elearningSubscription->getUserId();
  if ($user = $userUtils->selectById($userId)) {
    $participantName = $user->getFirstname() . ' ' . $user->getLastname();
  }
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
if ($duration > 0 && $elearningSessionId < 1) {
  $sinceDate = $clockUtils->incrementDays($systemDate, -1 * $duration);
}

$systemDate = $clockUtils->getSystemDate();

$resultGradeScale = $elearningExerciseUtils->resultGradeScale();

$totalCorrectAnswers = 0;
$totalIncorrectAnswers = 0;
$totalQuestions = 0;
$totalPoints = 0;
$generalTotalCorrectAnswers = 0;
$generalTotalQuestions = 0;
$generalTotalPoints = 0;
$wElearningSubscriptionId = '';

$strLiveResultJs = $elearningResultUtils->renderLiveResultJs();
$panelUtils->addContent($strLiveResultJs);

$strLiveResultIds = UTILS_URL_VALUE_SEPARATOR;

$listStep = $preferenceUtils->getValue("ELEARNING_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

$elearningAssignments = array();
if ($elearningSubscriptionId > 0) {
  $elearningAssignments = $elearningAssignmentUtils->selectByResultAndSubscriptionId($elearningSubscriptionId, $listIndex, $listStep);
} else if ($elearningClassId > 0 && $elearningSessionId) {
  $elearningAssignments = $elearningAssignmentUtils->selectByClassIdAndResultWithinSessionId($elearningClassId, $elearningSessionId, $listIndex, $listStep);
} else if ($elearningClassId > 0 && $sinceDate) {
  $elearningAssignments = $elearningAssignmentUtils->selectByClassIdAndResultSinceReleaseDate($elearningClassId, $sinceDate, $listIndex, $listStep);
} else if ($elearningClassId > 0) {
  $elearningAssignments = $elearningAssignmentUtils->selectByClassId($elearningClassId, $listIndex, $listStep);
} else if ($elearningSessionId > 0) {
  $elearningAssignments = $elearningAssignmentUtils->selectByResultWithinSessionId($elearningSessionId, $listIndex, $listStep);
} else if ($sinceDate) {
  $elearningAssignments = $elearningAssignmentUtils->selectByResultSinceReleaseDate($sinceDate, $listIndex, $listStep);
}

$elearningExerciseIds = array();
foreach ($elearningAssignments as $elearningAssignment) {
  $elearningResultId = $elearningAssignment->getElearningResultId();
  if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
    $elearningExerciseId = $elearningResult->getElearningExerciseId();
    array_push($elearningExerciseIds, $elearningExerciseId);
  }
}
$strCommand = '';
if ($elearningSubscriptionId > 0) {
  $strCommand .= " <a href=\"javascript: $('#subscriptionWhiteboard').slideToggle('fast'); void(0);\">"
    . "<img src='$gCommonImagesUrl/$gImageWhiteboard' class='no_style_image_icon' title='$mlText[48]' alt='' style='vertical-align:middle;' /></a>";
}
if ($elearningSubscriptionId > 0 && count($elearningExerciseIds) > 0) {
  $strCommand .= " <a href=\"javascript: $('#resultsGraph').slideToggle('fast'); void(0);\">"
    . "<img src='$gCommonImagesUrl/$gImageGraph' class='no_style_image_icon' title='$mlText[40]' alt='' style='vertical-align:middle;' /></a>";
}
$strCommand .= " <a href='$gElearningUrl/assignment/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageClassAssignment' title='$mlText[31]'></a>"
  . " <a href='$gElearningUrl/result/range/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageResultRange' title='$mlText[33]'></a>";

$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");
$help = $popupUtils->getHelpPopup($mlText[1], 300, 500);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
$strJsSuggest = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/subscription/suggest.php?displayClass=1", "participantName", "elearningSubscriptionId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningSubscriptionId', $elearningSubscriptionId);
$strSearch = "<input type='text' id='participantName' name='participantName' value='$participantName' /> " . $panelUtils->getTinyOk();
$strJsSuggestSession = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/session/suggest.php", "sessionName", "elearningSessionId");
$panelUtils->addContent($strJsSuggestSession);
$panelUtils->addHiddenField('elearningSessionId', $elearningSessionId);
$labelSession = $popupUtils->getTipPopup($mlText[29], $mlText[30], 300, 300);
$panelUtils->addLine($panelUtils->addCell($mlText[25], "nbr"), $strSearch, $panelUtils->addCell($labelSession, "nbr"), $panelUtils->addCell("<input type='text' id='sessionName' name='sessionName' value='$sessionName' /> " . $panelUtils->getTinyOk(), "n"), '', '', '', '', $panelUtils->addCell($strCommand, "nbr"));
$strJsSuggest = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/class/suggest.php", "className", "elearningClassId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningClassId', $elearningClassId);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $panelUtils->addCell("<input type='text' id='className' value='$className' /> " . $panelUtils->getTinyOk(), "n"), $panelUtils->addCell($mlText[32], "nbr"), $panelUtils->addCell($strSelectRelease, "n"), '', '', '', '', '');
$panelUtils->closeForm();
$panelUtils->addLine();

if ($elearningSubscriptionId > 0) {
  $strWhiteboard = "<div id='subscriptionWhiteboard' style='display: none;'><br />" . $elearningExerciseUtils->renderWhiteboard($elearningSubscriptionId) . "</div>";
  $panelUtils->addLine($panelUtils->addCell($strWhiteboard, ""));
}

if ($elearningSubscriptionId > 0 &&  count($elearningExerciseIds) > 0) {
  $resultsGraph = "<div id='resultsGraph' style='display: none;'><br />" . $elearningResultUtils->renderSubscriptionResultsGraph($elearningSubscriptionId, $elearningExerciseIds) . "</div>";
  $panelUtils->addLine($panelUtils->addCell($resultsGraph, ''));
}

$labelLiveResults = $popupUtils->getTipPopup($mlText[16], $mlText[17], 300, 300);
$labelDone = $popupUtils->getTipPopup($mlText[9], $mlText[28], 300, 300);
$labelGrade = $popupUtils->getTipPopup($mlText[19], $mlText[15], 300, 300);
$labelRatio = $userUtils->getTipPopup($mlText[36], $mlText[37], 300, 200);
$labelPoints = $userUtils->getTipPopup($mlText[34], $mlText[35], 300, 200);
$labelAnswers = $userUtils->getTipPopup($mlText[38], $mlText[39], 300, 200);
$panelUtils->addLine($panelUtils->addCell($mlText[18], "nb"), $panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($labelLiveResults, "nb"), $panelUtils->addCell($labelDone, "nbc"), $panelUtils->addCell($labelGrade, "cnb"), $panelUtils->addCell($labelRatio, "nbc"), $panelUtils->addCell($labelAnswers, "cnb"), $panelUtils->addCell($labelPoints, "cnb"), '');

$listNbItems = $elearningAssignmentUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($elearningAssignments as $elearningAssignment) {
  $elearningAssignmentId = $elearningAssignment->getId();
  $elearningExerciseId = $elearningAssignment->getElearningExerciseId();
  $previousSubscriptionId = $wElearningSubscriptionId;
  $wElearningSubscriptionId = $elearningAssignment->getElearningSubscriptionId();
  $elearningResultId = $elearningAssignment->getElearningResultId();

  if ($elearningResultId) {
    if ($previousSubscriptionId != $wElearningSubscriptionId && $previousSubscriptionId > 0) {
      $averageCorrectAnswers = $elearningResultUtils->calculateAverageCorrectAnswers($totalCorrectAnswers, $totalQuestions);
      $labelAverageResult = $popupUtils->getTipPopup($mlText[21], $mlText[41], 300, 200);
      $generalGrade = $elearningResultRangeUtils->calculateGrade($totalCorrectAnswers, $totalQuestions);
      $strResultGrades = $elearningResultUtils->renderResultGrades('', $generalGrade);
      $strResultRatio = $elearningResultUtils->renderResultRatio('', $averageCorrectAnswers, $resultGradeScale);
      $strResultPoints = $elearningResultUtils->renderResultPoints('', $totalPoints);
      $panelUtils->addLine('', '', '', $panelUtils->addCell($labelAverageResult, "nbr"), $panelUtils->addCell($strResultGrades, "nc"), $panelUtils->addCell($strResultRatio, "nc"), '', $panelUtils->addCell($strResultPoints, "nc"), '');
      $panelUtils->addLine();

      $totalCorrectAnswers = 0;
      $totalIncorrectAnswers = 0;
      $totalQuestions = 0;
      $totalPoints = 0;
    }
  }

  if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
    $exerciseDate = $elearningResult->getExerciseDate();
    $exerciseDate = $clockUtils->systemToLocalNumericDate($elearningResult->getExerciseDate());

    $exerciseName = '';
    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
      $exerciseName = $elearningExercise->getName();
    }

    $participantName = '';
    $firstname = '';
    $lastname = '';
    $email = '';
    if ($elearningSubscription = $elearningSubscriptionUtils->selectById($wElearningSubscriptionId)) {
      $userId = $elearningSubscription->getUserId();
      if ($user = $userUtils->selectById($userId)) {
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $email = $user->getEmail();
      }
    }

    if ($firstname || $lastname) {
      $participantName = $firstname . ' ' . $lastname;
    } else {
      $participantName = $email;
    }

    $resultTotals = $elearningResultUtils->getExerciseTotals($elearningExerciseId, $elearningResultId);
    $nbQuestions = $elearningResultUtils->getResultNbQuestions($resultTotals);
    $nbCorrectAnswers = $elearningResultUtils->getResultNbCorrectAnswers($resultTotals);
    $nbIncorrectAnswers = $elearningResultUtils->getResultNbIncorrectAnswers($resultTotals);
    $nbAnswers = $nbCorrectAnswers + $nbIncorrectAnswers;
    $points = $elearningResultUtils->getResultNbPoints($resultTotals);
    $grade = $elearningResultRangeUtils->calculateGrade($nbCorrectAnswers, $nbQuestions);

    if ($elearningResultId) {
      if ($elearningSubscriptionId < 1) {
        $generalTotalCorrectAnswers = $generalTotalCorrectAnswers + $nbCorrectAnswers;
        $generalTotalQuestions = $generalTotalQuestions + $nbQuestions;
        $generalTotalPoints = $generalTotalPoints + $points;
      }

      $totalCorrectAnswers = $totalCorrectAnswers + $nbCorrectAnswers;
      $totalIncorrectAnswers = $totalIncorrectAnswers + $nbIncorrectAnswers;
      $totalQuestions = $totalQuestions + $nbQuestions;
      $totalPoints = $totalPoints + $points;
    }

    $strResultGrades = '';
    $strResultAnswers = '';
    $strLiveResults = '';
    if ($nbQuestions) {
      $strResultGrades = $elearningResultUtils->renderResultGrades($elearningResultId, $grade, $nbCorrectAnswers, $nbQuestions, $points);
      $strResultRatio = $elearningResultUtils->renderResultRatio($elearningResultId, $nbCorrectAnswers, $nbQuestions);
      $strResultAnswers = $elearningResultUtils->renderResultAnswers($elearningResultId, $nbCorrectAnswers, $nbIncorrectAnswers, $nbQuestions);
      $strResultPoints = $elearningResultUtils->renderResultPoints($elearningResultId, $points);
      $strLiveResults = $elearningResultUtils->renderExerciseResultsGraph($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true, true, '')
        . " <img id='" . ELEARNING_DOM_ID_INACTIVE . $wElearningSubscriptionId . '_' . $elearningExerciseId . "' src='$gCommonImagesUrl/$gImageLightOrangeSmallBlink' title='' alt='' style='visibility: hidden;' />";

    }

    $strExercise = "<a href='$gElearningUrl/exercise/compose.php?elearningExerciseId=$elearningExerciseId' title='$mlText[23]' $gJSNoStatus target='_blank'>$exerciseName</a>";

    $strParticipant = "<a href='$gElearningUrl/subscription/view.php?elearningSubscriptionId=$wElearningSubscriptionId' title='$mlText[24]' $gJSNoStatus target='_blank'>$participantName</a>";

    $strCommand = '';
    if ($elearningResultId) {
      $strCommand .= " <a href='$gElearningUrl/result/view.php?elearningResultId=$elearningResultId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageCheckList' title='$mlText[20]'></a>";
    }
    $strCommand .= ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePeople' title='$mlText[26]'>", "$gElearningUrl/assignment/copilot.php?elearningAssignmentId=$elearningAssignmentId", 900, 800)
      . " <a href='$gElearningUrl/result/delete.php?elearningResultId=$elearningResultId' $gJSNoStatus target='_blank'>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[22]'></a>";

    $strExerciseDate = '';
    if ($elearningResultId) {
      $strExerciseDate = $popupUtils->getDialogPopup($exerciseDate, "$gElearningUrl/result/view.php?elearningResultId=$elearningResultId", 900, 800, $mlText[20]);
    }

    $panelUtils->addLine($panelUtils->addCell($strParticipant, 'n'), $panelUtils->addCell($strExercise, 'n'), $panelUtils->addCell($strLiveResults, 'n'), $panelUtils->addCell($strExerciseDate, 'nc'), $panelUtils->addCell($strResultGrades, 'cn'), $panelUtils->addCell($strResultRatio, "nc"), $panelUtils->addCell($strResultAnswers, 'cn'), $panelUtils->addCell($strResultPoints, "nc"), $panelUtils->addCell($strCommand, "mnr"));

    $strLiveResultIds .= UTILS_URL_VALUE_SEPARATOR . $elearningResultId;
  }
}
$panelUtils->closeList();

if (count($elearningAssignments) > 0) {
  $averageCorrectAnswers = $elearningResultUtils->calculateAverageCorrectAnswers($totalCorrectAnswers, $totalQuestions);
  $grade = $elearningResultRangeUtils->calculateGrade($totalCorrectAnswers, $totalQuestions);
  $strResultGrades = $elearningResultUtils->renderResultGrades('', $grade);
  $strResultRatio = $elearningResultUtils->renderResultRatio('', $averageCorrectAnswers, $resultGradeScale);
  $strResultPoints = $elearningResultUtils->renderResultPoints('', $totalPoints);
  $label = $popupUtils->getTipPopup($mlText[21], $mlText[41], 300, 200);
  $panelUtils->addLine();
  $panelUtils->addLine('', '', '', $panelUtils->addCell($label, "nbr"), $panelUtils->addCell($strResultGrades, 'cn'), $panelUtils->addCell($strResultRatio, "nc"), '', $panelUtils->addCell($strResultPoints, "nc"), '');

  if ($elearningSubscriptionId < 1) {
    $averageCorrectAnswers = $elearningResultUtils->calculateAverageCorrectAnswers($generalTotalCorrectAnswers, $generalTotalQuestions);
    $grade = $elearningResultRangeUtils->calculateGrade($generalTotalCorrectAnswers, $generalTotalQuestions);
    $strResultGrades = $elearningResultUtils->renderResultGrades('', $grade);
    $strResultRatio = $elearningResultUtils->renderResultRatio('', $averageCorrectAnswers, $resultGradeScale);
    $strResultPoints = $elearningResultUtils->renderResultPoints('', $generalTotalPoints);

    $label = $popupUtils->getTipPopup($mlText[42], $mlText[41], 300, 200);
    $panelUtils->addLine();
    $panelUtils->addLine('', '', '', $panelUtils->addCell($label, "nbr"), $panelUtils->addCell($strResultGrades, 'cn'), $panelUtils->addCell($strResultRatio, "nc"), '', $panelUtils->addCell($strResultPoints, "nc"), '');
  }
}

$strRememberScroll = LibJavaScript::rememberScroll("elearning_assignment_class_results_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
