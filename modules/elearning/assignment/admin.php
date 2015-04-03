<?php

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
$elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");
$status = LibEnv::getEnvHttpPOST("status");

if (!$elearningSubscriptionId) {
  $elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");
}

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

if (!$status) {
  $status = LibSession::getSessionValue(ELEARNING_SESSION_STATUS);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_STATUS, $status);
}

if ($elearningSubscriptionId > 0) {
  $elearningClassId = '';
  $status = '';
  LibSession::putSessionValue(ELEARNING_SESSION_CLASS, '');
  LibSession::putSessionValue(ELEARNING_SESSION_STATUS, '');
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");
$help = $popupUtils->getHelpPopup($mlText[1], 300, 500);
$panelUtils->setHelp($help);

$warnings = array();

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

$className = '';
if ($elearningClass = $elearningClassUtils->selectById($elearningClassId)) {
  $className = $elearningClass->getName();
}

define('STATUS_OPENED', 1);
define('STATUS_DEFERRED', 2);
define('STATUS_CLOSED', 3);
$statusList = array(
  '-1' => '',
   STATUS_OPENED => $mlText[25],
   STATUS_DEFERRED => $mlText[26],
   STATUS_CLOSED => $mlText[27]
);
$strSelectStatus = LibHtml::getSelectList("status", $statusList, $status, true);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$strCommand = " <a href='$gElearningUrl/assignment/results.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageClassAssignment' title='$mlText[20]'></a>";
$panelUtils->openForm($PHP_SELF, "search");
$strJsSuggest = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/subscription/suggest.php", "participantName", "elearningSubscriptionId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningSubscriptionId', $elearningSubscriptionId);
$labelStatus = $popupUtils->getTipPopup($mlText[21], $mlText[22], 300, 300);
$panelUtils->addLine($panelUtils->addCell($mlText[18], "nbr"), $panelUtils->addCell("<input type='text' id='participantName' name='participantName' value='$participantName' /> " . $panelUtils->getTinyOk(), "n"), '', $panelUtils->addCell($labelStatus, "nbr"), $panelUtils->addCell($strSelectStatus, "n"), '', $panelUtils->addCell($strCommand, "nr"));
$strJsSuggest = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/class/suggest.php", "className", "elearningClassId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningClassId', $elearningClassId);
$panelUtils->addLine($panelUtils->addCell($mlText[19], "nbr"), "<input type='text' id='className' name='className' value='$className' /> " . $panelUtils->getTinyOk(), '', '', '', '', '');
$panelUtils->closeForm();
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine();

$systemDate = $clockUtils->getSystemDate();

$strCommand = "<a href='$gElearningUrl/assignment/add.php?elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[3]'></a>";
if ($elearningSubscriptionId) {
  $strCommand .= " <a href='$gElearningUrl/assignment/delete_closed.php?elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[23]'></a>";
}
$labelLiveResults = $popupUtils->getTipPopup($mlText[7], $mlText[8], 300, 300);
$labelDone = $popupUtils->getTipPopup($mlText[11], $mlText[15], 300, 300);
$labelOpeningDate = $popupUtils->getTipPopup($mlText[12], $mlText[16], 300, 300);
$labelClosingDate = $popupUtils->getTipPopup($mlText[13], $mlText[17], 300, 300);
$panelUtils->addLine($panelUtils->addCell($mlText[9], "nb"), $panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($labelLiveResults, "nb"), $panelUtils->addCell($labelDone, "nbc"), $panelUtils->addCell($labelOpeningDate, "nb"), $panelUtils->addCell($labelClosingDate, "nb"), $panelUtils->addCell($strCommand, "nr"));

$strLiveResultJs = $elearningResultUtils->renderLiveResultJs();
$panelUtils->addContent($strLiveResultJs);

$listStep = $preferenceUtils->getValue("ELEARNING_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

$elearningAssignments = array();
if ($elearningSubscriptionId > 0 && $status) {
  if ($status == STATUS_OPENED) {
    $elearningAssignments = $elearningAssignmentUtils->selectBySubscriptionIdAndOpened($elearningSubscriptionId, $systemDate, $listIndex, $listStep);
  } else if ($status == STATUS_DEFERRED) {
    $elearningAssignments = $elearningAssignmentUtils->selectBySubscriptionIdAndDeferred($elearningSubscriptionId, $systemDate, $listIndex, $listStep);
  } else if ($status == STATUS_CLOSED) {
    $elearningAssignments = $elearningAssignmentUtils->selectBySubscriptionIdAndClosed($elearningSubscriptionId, $systemDate, $listIndex, $listStep);
  }
} else if ($elearningSubscriptionId > 0) {
  $elearningAssignments = $elearningAssignmentUtils->selectBySubscriptionId($elearningSubscriptionId, $listIndex, $listStep);
} else if ($elearningClassId > 0 && $status > 0) {
  if ($status == STATUS_OPENED) {
    $elearningAssignments = $elearningAssignmentUtils->selectByClassIdAndOpened($elearningClassId, $systemDate, $listIndex, $listStep);
  } else if ($status == STATUS_DEFERRED) {
    $elearningAssignments = $elearningAssignmentUtils->selectByClassIdAndDeferred($elearningClassId, $systemDate, $listIndex, $listStep);
  } else if ($status == STATUS_CLOSED) {
    $elearningAssignments = $elearningAssignmentUtils->selectByClassIdAndClosed($elearningClassId, $systemDate, $listIndex, $listStep);
  }
} else if ($elearningClassId > 0) {
  $elearningAssignments = $elearningAssignmentUtils->selectByClassId($elearningClassId, $listIndex, $listStep);
} else if ($status) {
  if ($status == STATUS_OPENED) {
    $elearningAssignments = $elearningAssignmentUtils->selectOpened($systemDate, $listIndex, $listStep);
  } else if ($status == STATUS_DEFERRED) {
    $elearningAssignments = $elearningAssignmentUtils->selectDeferred($systemDate, $listIndex, $listStep);
  } else if ($status == STATUS_CLOSED) {
    $elearningAssignments = $elearningAssignmentUtils->selectClosed($systemDate, $listIndex, $listStep);
  }
}

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
  $elearningSubscriptionId = $elearningAssignment->getElearningSubscriptionId();
  $elearningExerciseId = $elearningAssignment->getElearningExerciseId();
  $elearningResultId = $elearningAssignment->getElearningResultId();
  $openingDate = $elearningAssignment->getOpeningDate();
  $closingDate = $elearningAssignment->getClosingDate();

  $openingDate = $clockUtils->systemToLocalNumericDate($openingDate);
  $closingDate = $clockUtils->systemToLocalNumericDate($closingDate);

  $participantName = '';
  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    $userId = $elearningSubscription->getUserId();
    if ($user = $userUtils->selectById($userId)) {
      $participantName = $user->getFirstname() . ' ' . $user->getLastname();
    }
  }
  
  $exerciseName = '';
  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $exerciseName = $elearningExercise->getName();
    $elearningLevelId = $elearningExercise->getLevelId();
    if ($elearningLevel = $elearningLevelUtils->selectById($elearningLevelId)) {
    }
  }

  $strLiveResults = '';
  $exerciseDate = '';
  if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
    $exerciseDate = $elearningResult->getExerciseDate();
    $exerciseDate = $clockUtils->systemToLocalNumericDate($elearningResult->getExerciseDate());

    $resultTotals = $elearningResultUtils->getExerciseTotals($elearningExerciseId, $elearningResultId);
    $nbQuestions = $elearningResultUtils->getResultNbQuestions($resultTotals);
    $nbCorrectAnswers = $elearningResultUtils->getResultNbCorrectAnswers($resultTotals);
    $nbIncorrectAnswers = $elearningResultUtils->getResultNbIncorrectAnswers($resultTotals);
    $strLiveResults = $elearningResultUtils->renderExerciseResultsGraph($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true, true, '')
      . " <img id='" . ELEARNING_DOM_ID_INACTIVE . $elearningSubscriptionId . '_' . $elearningExerciseId . "' src='$gCommonImagesUrl/$gImageLightOrangeSmallBlink' title='' alt='' style='display: none;' />";
  }

  $strCommand = '';
  if ($elearningResultId) {
    $strCommand .= ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageCheckList' title='$mlText[28]'>", "$gElearningUrl/result/view.php?elearningResultId=$elearningResultId", 900, 800);
  }
  $strCommand .=  ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePeople' title='$mlText[24]'>", "$gElearningUrl/assignment/copilot.php?elearningAssignmentId=$elearningAssignmentId", 900, 800);
  $strCommand .= " <a href='$gElearningUrl/assignment/edit.php?elearningAssignmentId=$elearningAssignmentId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[5]'></a>"
    . " <a href='$gElearningUrl/assignment/delete.php?elearningAssignmentId=$elearningAssignmentId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[6]'></a>";

  $strExerciseDate = '';
  if ($elearningResultId) {
    $strExerciseDate = ' ' . $popupUtils->getDialogPopup($exerciseDate, "$gElearningUrl/result/view.php?elearningResultId=$elearningResultId", 900, 800, $mlText[28]);
  }

  $panelUtils->addLine($panelUtils->addCell($participantName, 'n'), $panelUtils->addCell($exerciseName, 'n'),  $panelUtils->addCell($strLiveResults, 'nm'), $panelUtils->addCell($strExerciseDate, 'nc'), $panelUtils->addCell($openingDate, 'n'), $panelUtils->addCell($closingDate, 'n'), $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("elearning_assignment_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
