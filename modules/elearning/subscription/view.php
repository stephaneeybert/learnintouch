<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");

$firstname = '';
$lastname = '';
$email = '';
$subscriptionDate = '';
if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
  $userId = $elearningSubscription->getUserId();
  $subscriptionDate = $elearningSubscription->getSubscriptionDate();
  if ($user = $userUtils->selectById($userId)) {
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();
    $email = $user->getEmail();
  }
}

$subscriptionDate = $clockUtils->systemToLocalNumericDate($subscriptionDate);

$strIndent = "<img border='0' src='$gCommonImagesUrl/$gImageTransparent' title=''> ";

$totalCorrectAnswers = 0;
$totalIncorrectAnswers = 0;
$totalQuestions = 0;
$totalPoints = 0;

$strCommand = '';

$hasResults = $elearningSubscriptionUtils->hasResults($elearningSubscription);

$strCommand .= " <a href=\"javascript: $('#subscriptionWhiteboard').slideToggle('fast'); toggleParticipantWhiteboard(); void(0);\">"
    . "<img src='$gCommonImagesUrl/$gImageWhiteboard' class='no_style_image_icon' title='$mlText[26]' alt='' style='vertical-align:middle;' /></a>";

if ($hasResults) {
  $strCommand .= " <a href=\"javascript: $('#resultsGraph').slideToggle('fast'); void(0);\">"
    . "<img src='$gCommonImagesUrl/$gImageGraph' class='no_style_image_icon' title='$mlText[186]' alt='' style='vertical-align:middle;' /></a>";
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");
$strSubscription = "<a target='_blank' href='$gElearningUrl/subscription/edit.php?elearningSubscriptionId=$elearningSubscriptionId'>"
  . $firstname . ' ' . $lastname
  . "</a>";
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $panelUtils->addCell($strSubscription, "n"), '', '', '', '', '', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $panelUtils->addCell($subscriptionDate, "n"), '', '', '', '', '', '');
$subscriptionClose = $elearningSubscriptionUtils->isClosed($elearningSubscription);
if ($clockUtils->systemDateIsSet($subscriptionClose)) {
  $subscriptionClose = $clockUtils->systemToLocalNumericDate($subscriptionClose);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $panelUtils->addCell($subscriptionClose, "n"), '', '', '', '', '', '');
}

$strLiveResultJs = $elearningResultUtils->renderLiveResultJs();
$panelUtils->addContent($strLiveResultJs);

$strLiveResultIds = UTILS_URL_VALUE_SEPARATOR;

if ($elearningSubscription) {
  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    $elearningSessionId = $elearningSubscription->getSessionId();
    $elearningCourseId = $elearningSubscription->getCourseId();
    $lastActive = $elearningSubscription->getLastActive();
    $watchLive = $elearningSubscription->getWatchLive();

    $courseName = '';
    if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
      $courseName = $elearningCourse->getName();

      $str = "<a target='_blank' href='$gElearningUrl/course/edit.php?elearningCourseId=$elearningCourseId'>" . $courseName . "</a>";
      $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $panelUtils->addCell($str, "n"), '', '', '', '', '', '');

      if ($elearningSession = $elearningSessionUtils->selectById($elearningSessionId)) {
        $sessionName = $elearningSession->getName();
        $openDate = $elearningSession->getOpenDate();
        $closeDate = $elearningSession->getCloseDate();
        $strOpenDate = $clockUtils->systemToLocalNumericDate($openDate);
        if ($clockUtils->systemDateIsSet($closeDate)) {
          $strCloseDate = $clockUtils->systemToLocalNumericDate($closeDate);
        } else {
          $strCloseDate = '';
        }
        $str = $sessionName . " " . $mlText[71] . ' ' .  $strOpenDate;
        if ($closeDate) {
          $str .= ' ' . $mlText[72] . ' ' . $strCloseDate;
        }
        $str .= "</div>";
        $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $panelUtils->addCell($str, "n"), '', '', '', '', '', '');
      }

      $strWhiteboard = "<div id='subscriptionWhiteboard' style='display: none;'><br />" . $elearningExerciseUtils->renderWhiteboard($elearningSubscriptionId) . "</div>";
      $panelUtils->addLine($panelUtils->addCell($strWhiteboard, ""));

      if ($hasResults) {
        $elearningExerciseIds = $elearningCourseUtils->getCourseExercises($elearningCourseId);
        $resultsGraph = "<div id='resultsGraph' style='display: none;'><br />" . $elearningResultUtils->renderSubscriptionResultsGraph($elearningSubscriptionId, $elearningExerciseIds) . "</div>";
        $panelUtils->addLine($panelUtils->addCell($resultsGraph, ""));
      }

      $labelLiveResults = $userUtils->getTipPopup($mlText[6], $mlText[12], 300, 200);
      $labelDone = $popupUtils->getTipPopup($mlText[13], $mlText[14], 300, 300);
      $labelGrade = $userUtils->getTipPopup($mlText[15], $mlText[16], 300, 200);
      $labelRatio = $userUtils->getTipPopup($mlText[21], $mlText[22], 300, 200);
      $labelAnswers = $userUtils->getTipPopup($mlText[19], $mlText[20], 300, 200);
      $labelPoints = $userUtils->getTipPopup($mlText[17], $mlText[18], 300, 200);

      $panelUtils->addLine();
      $panelUtils->addLine('', $panelUtils->addCell($labelLiveResults, "nb"), $panelUtils->addCell($labelDone, "nbc"), $panelUtils->addCell($labelGrade, "nbc"), $panelUtils->addCell($labelRatio, "nbc"), $panelUtils->addCell($labelAnswers, "nbc"), $panelUtils->addCell($labelPoints, "nbc"), '');
      $panelUtils->addLine();

      $elearningCourseItems = $elearningCourseItemUtils->selectByCourseId($elearningCourseId);
      $panelUtils->openList();
      foreach ($elearningCourseItems as $elearningCourseItem) {
        $elearningExerciseId = $elearningCourseItem->getElearningExerciseId();
        $elearningLessonId = $elearningCourseItem->getElearningLessonId();

        if ($elearningExerciseId) {
          if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
            $elearningResultId = '';
            $exerciseDate = '';
            $points = '';
            $grade = '';
            $nbQuestions = '';
            $nbIncorrectAnswers = '';
            $nbCorrectAnswers = '';
            $lastExercisePageId = '';
            if ($elearningResult = $elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $elearningExerciseId)) {
              $elearningResultId = $elearningResult->getId();
              $exerciseDate = $elearningResult->getExerciseDate();
              $exerciseDate = $clockUtils->systemToLocalNumericDate($elearningResult->getExerciseDate());
              $lastExercisePageId = $elearningSubscription->getLastExercisePageId();
              $resultTotals = $elearningResultUtils->getExerciseTotals($elearningExerciseId, $elearningResultId);
              $nbQuestions = $elearningResultUtils->getResultNbQuestions($resultTotals);
              $nbCorrectAnswers = $elearningResultUtils->getResultNbCorrectAnswers($resultTotals);
              $nbIncorrectAnswers = $elearningResultUtils->getResultNbIncorrectAnswers($resultTotals);
              $points = $elearningResultUtils->getResultNbPoints($resultTotals);
              $grade = $elearningResultRangeUtils->calculateGrade($nbCorrectAnswers, $nbQuestions);

              $totalCorrectAnswers = $totalCorrectAnswers + $nbCorrectAnswers;
              $totalIncorrectAnswers = $totalIncorrectAnswers + $nbIncorrectAnswers;
              $totalQuestions = $totalQuestions + $nbQuestions;
              $totalPoints = $totalPoints + $points;
            }

            $exerciseName = $elearningExercise->getName();

            $exerciseIsAvailable = $elearningExerciseUtils->isParticipantExerciseAvailable($elearningSubscriptionId, $elearningExerciseId);

            if ($exerciseIsAvailable) {
              $strDoExercise = $popupUtils->getDialogPopup("<img src='$gCommonImagesUrl/$gImageExercise' class='no_style_image_icon' title='$mlText[91]' alt='' style='vertical-align:middle;' />", "$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId", 900, 800);
              $strExerciseName = $popupUtils->getDialogPopup($exerciseName . " <img src='$gCommonImagesUrl/$gImageExercise' class='no_style_image_icon' title='$mlText[91]' alt='' style='vertical-align:middle;' />", "$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId", 900, 800);
            } else {
              $strDoExercise = '';
              $strExerciseName = "<span title='" . $mlText[8] . "'>" . $exerciseName . '</span>';
            }

            $strEditExercise = "<a target='_blank' href='$gElearningUrl/exercise/edit.php?elearningExerciseId=$elearningExerciseId'>"
              . "<img src='$gCommonImagesUrl/$gImageEdit' class='no_style_image_icon' title='$mlText[9]' alt='' style='vertical-align:middle;' /></a>";

            $strComposeExercise = "<a target='_blank' href='$gElearningUrl/exercise/compose.php?elearningExerciseId=$elearningExerciseId'>"
              . "<img src='$gCommonImagesUrl/$gImageDesign' class='no_style_image_icon' title='$mlText[11]' alt='' style='vertical-align:middle;' /></a>";

            $strDisplayResult = '';
            $strSendResult = '';
            $strPrintResult = '';
            $strLiveResults = '';
            $strResultGrades = '';
            $strResultRatio = '';
            $strResultAnswers = '';
            $strResultPoints = '';
            if ($elearningSubscriptionUtils->exerciseHasResults($elearningSubscriptionId, $elearningExerciseId)) {
              $strDisplayResult = "<a href='$gElearningUrl/result/view.php?elearningResultId=$elearningResultId'>"
                . "<img src='$gCommonImagesUrl/$gImageCheckList' class='no_style_image_icon' title='" .  $mlText[94] . " 'alt='' style='vertical-align:middle;' /></a>";

              $strSendResult = ' ' . $popupUtils->getDialogPopup("<img src='$gCommonImagesUrl/$gImageEmail' class='no_style_image_icon' title='" .  $mlText[90] .  "' alt='' style='vertical-align:middle;' />", "$gElearningUrl/result/send.php?elearningResultId=$elearningResultId", 600, 600);

              $strPrintResult = ' ' . $popupUtils->getDialogPopup("<img src='$gCommonImagesUrl/$gImagePrinter' class='no_style_image_icon' title='" .  $mlText[10] . " 'alt='' style='vertical-align:middle;' />", "$gElearningUrl/result/print.php?elearningResultId=$elearningResultId", 600, 600);
              if ($watchLive) {
                $strLiveResults = $elearningResultUtils->renderExerciseResultsGraph($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true, true, $exerciseName)
                  . " <img id='" . ELEARNING_DOM_ID_INACTIVE . $elearningSubscriptionId . '_' . $elearningExerciseId . "' src='$gCommonImagesUrl/$gImageLightOrangeSmallBlink' title='' alt='' style='visibility: hidden;' />";
              }

              $strResultGrades = $elearningResultUtils->renderResultGrades($elearningResultId, $grade, $nbCorrectAnswers, $nbQuestions, $points);
              $strResultRatio = $elearningResultUtils->renderResultRatio($elearningResultId, $nbCorrectAnswers, $nbQuestions);
              $strResultAnswers = $elearningResultUtils->renderResultAnswers($elearningResultId, $nbCorrectAnswers, $nbIncorrectAnswers, $nbQuestions);
              $strResultPoints = $elearningResultUtils->renderResultPoints($elearningResultId, $points);
            }
            $strCommand = $strDisplayResult
              . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePeople' title='$mlText[24]'>", "$gElearningUrl/subscription/copilot.php?elearningSubscriptionId=$elearningSubscriptionId&elearningExerciseId=$elearningExerciseId&lastExercisePageId=$lastExercisePageId", 900, 800)
              . ' ' . $strDoExercise
              . ' ' . $strEditExercise
              . ' ' . $strComposeExercise
              . ' ' . $strSendResult
              . ' ' . $strPrintResult;

            $strExerciseDate = '';
            if ($elearningResultId) {
              $strExerciseDate = $popupUtils->getDialogPopup($exerciseDate, "$gElearningUrl/result/view.php?elearningResultId=$elearningResultId", 900, 800, $mlText[94]);
            }

            $panelUtils->addLine($panelUtils->addCell($strExerciseName, "n"), $panelUtils->addCell($strLiveResults, "n"), $panelUtils->addCell($strExerciseDate, "nc"), $panelUtils->addCell($strResultGrades, "nc"), $panelUtils->addCell($strResultRatio, "nc"), $panelUtils->addCell($strResultAnswers, "nc"), $panelUtils->addCell($strResultPoints, "nc"), $panelUtils->addCell("$strCommand", "nr"));
            if ($elearningResult) {
              $strLiveResultIds .= UTILS_URL_VALUE_SEPARATOR . $elearningResultId;
            }

          }
        } else if ($elearningLessonId) {
          if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {

            $lessonName = $elearningLesson->getName();
            $strLessonName = $popupUtils->getDialogPopup($lessonName . " <img src='$gCommonImagesUrl/$gImageLesson' class='no_style_image_icon' title='$mlText[4]' alt='' style='vertical-align:middle;' />", "$gElearningUrl/lesson/display_lesson.php?elearningLessonId=$elearningLessonId&elearningSubscriptionId=$elearningSubscriptionId", 900, 800);
            $strDoLesson = $popupUtils->getDialogPopup("<img src='$gCommonImagesUrl/$gImageLesson' class='no_style_image_icon' title='$mlText[4]' alt='' style='vertical-align:middle;' />", "$gElearningUrl/lesson/display_lesson.php?elearningLessonId=$elearningLessonId&elearningSubscriptionId=$elearningSubscriptionId", 900, 800);
            $strCommand = $strDoLesson;
            $panelUtils->addLine($panelUtils->addCell($strLessonName, "n"), '', '', '', '', '', '', '', $panelUtils->addCell($strCommand, "nr"));

            if ($elearningLessonParagraphs = $elearningLessonParagraphUtils->selectByLessonId($elearningLessonId)) {
              foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
                $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();

                if ($elearningExerciseId) {
                  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
                    $elearningResultId = '';
                    $exerciseDate = '';
                    $points = '';
                    $grade = '';
                    $nbQuestions = '';
                    $nbIncorrectAnswers = '';
                    $nbCorrectAnswers = '';
                    $lastExercisePageId = '';
                    if ($elearningResult = $elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $elearningExerciseId)) {
                      $elearningResultId = $elearningResult->getId();
                      $exerciseDate = $elearningResult->getExerciseDate();
                      $exerciseDate = $clockUtils->systemToLocalNumericDate($elearningResult->getExerciseDate());
                      $lastExercisePageId = $elearningSubscription->getLastExercisePageId();
                      $resultTotals = $elearningResultUtils->getExerciseTotals($elearningExerciseId, $elearningResultId);
                      $nbQuestions = $elearningResultUtils->getResultNbQuestions($resultTotals);
                      $nbIncorrectAnswers = $elearningResultUtils->getResultNbIncorrectAnswers($resultTotals);
                      $nbCorrectAnswers = $elearningResultUtils->getResultNbCorrectAnswers($resultTotals);
                      $points = $elearningResultUtils->getResultNbPoints($resultTotals);
                      $grade = $elearningResultRangeUtils->calculateGrade($nbCorrectAnswers, $nbQuestions);

                      $totalCorrectAnswers = $totalCorrectAnswers + $nbCorrectAnswers;
                      $totalIncorrectAnswers = $totalIncorrectAnswers + $nbIncorrectAnswers;
                      $totalQuestions = $totalQuestions + $nbQuestions;
                      $totalPoints = $totalPoints + $points;
                    }

                    $exerciseName = $elearningExercise->getName();

                    $exerciseIsAvailable = $elearningExerciseUtils->isParticipantExerciseAvailable($elearningSubscriptionId, $elearningExerciseId);

                    if ($exerciseIsAvailable) {
                      $strDoExercise = "<a href='$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus title='" . $mlText[91] . "'>" . "<img src='$gCommonImagesUrl/$gImageExercise' class='no_style_image_icon' title='' alt='' style='vertical-align:middle;' /></a>";
                      $strExerciseName = $strIndent . ' ' . "<a href='$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId' $gJSNoStatus title='" . $mlText[91] . "'>" . $exerciseName . " <img src='$gCommonImagesUrl/$gImageExercise' class='no_style_image_icon' title='' alt='' style='vertical-align:middle;' />" . "</a>";
                    } else {
                      $strDoExercise = '';
                      $strExerciseName = $strIndent . ' ' . "<span title='" . $mlText[8] . "'>" . $exerciseName . '</span>';
                    }

                    $strEditExercise = "<a target='_blank' href='$gElearningUrl/exercise/edit.php?elearningExerciseId=$elearningExerciseId'>"
                      . "<img src='$gCommonImagesUrl/$gImageEdit' class='no_style_image_icon' title='$mlText[9]' alt='' style='vertical-align:middle;' /></a>";

                    $strComposeExercise = "<a target='_blank' href='$gElearningUrl/exercise/compose.php?elearningExerciseId=$elearningExerciseId'>"
                      . "<img src='$gCommonImagesUrl/$gImageDesign' class='no_style_image_icon' title='$mlText[11]' alt='' style='vertical-align:middle;' /></a>";

                    $strDisplayResult = '';
                    $strSendResult = '';
                    $strPrintResult = '';
                    $strResultGrades = '';
                    $strResultRatio = '';
                    $strResultAnswers = '';
                    $strResultPoints = '';
                    $strLiveResults = '';
                    if ($elearningSubscriptionUtils->exerciseHasResults($elearningSubscriptionId, $elearningExerciseId)) {
                      $strDisplayResult = "<a href='$gElearningUrl/result/view.php?elearningResultId=$elearningResultId'>"
                        . "<img src='$gCommonImagesUrl/$gImageCheckList' class='no_style_image_icon' title='" .  $mlText[94] . " 'alt='' style='vertical-align:middle;' /></a>";
                      $strSendResult = ' ' . $popupUtils->getDialogPopup("<img src='$gCommonImagesUrl/$gImageEmail' class='no_style_image_icon' title='" .  $mlText[90] .  "' alt='' style='vertical-align:middle;' />", "$gElearningUrl/result/send.php?elearningResultId=$elearningResultId", 600, 600);

                      $strPrintResult = ' ' . $popupUtils->getDialogPopup("<img src='$gCommonImagesUrl/$gImagePrinter' class='no_style_image_icon' title='" .  $mlText[10] . " 'alt='' style='vertical-align:middle;' />", "$gElearningUrl/result/print.php?elearningResultId=$elearningResultId", 600, 600);

                      $strResultGrades = $elearningResultUtils->renderResultGrades($elearningResultId, $grade, $nbCorrectAnswers, $nbQuestions, $points);
                      $strResultRatio = $elearningResultUtils->renderResultRatio($elearningResultId, $nbCorrectAnswers, $nbQuestions);
                      $strResultAnswers = $elearningResultUtils->renderResultAnswers($elearningResultId, $nbCorrectAnswers, $nbIncorrectAnswers, $nbQuestions);
                      $strResultPoints = $elearningResultUtils->renderResultPoints($elearningResultId, $points);
                    }
                    if ($watchLive) {
                      $strLiveResults = $elearningResultUtils->renderExerciseResultsGraph($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true, true, '')
                        . " <img id='" . ELEARNING_DOM_ID_INACTIVE . $elearningSubscriptionId . '_' . $elearningExerciseId . "' src='$gCommonImagesUrl/$gImageLightOrangeSmallBlink' title='' alt='' style='visibility: hidden;' />";
                      if ($elearningResult) {
                        $strLiveResultIds .= UTILS_URL_VALUE_SEPARATOR . $elearningResultId;
                      }
                    }

                    $strCommand = $strDisplayResult
                      . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePeople' title='$mlText[24]'>", "$gElearningUrl/subscription/copilot.php?elearningSubscriptionId=$elearningSubscriptionId&elearningExerciseId=$elearningExerciseId&lastExercisePageId=$lastExercisePageId", 900, 800)
                      . ' ' . $strDoExercise
                      . ' ' . $strEditExercise
                      . ' ' . $strComposeExercise
                      . ' ' . $strSendResult
                      . ' ' . $strPrintResult;

                    $strExerciseDate = '';
                    if ($elearningResultId) {
                      $strExerciseDate = $popupUtils->getDialogPopup($exerciseDate, "$gElearningUrl/result/view.php?elearningResultId=$elearningResultId", 900, 800, $mlText[94]);
                    }

                    $panelUtils->addLine($panelUtils->addCell($strExerciseName, "n"), $panelUtils->addCell($strLiveResults, 'n'), $panelUtils->addCell($strExerciseDate, "nc"), $panelUtils->addCell($strResultGrades, "nc"), $panelUtils->addCell($strResultRatio, "nc"), $panelUtils->addCell($strResultAnswers, "nc"), $panelUtils->addCell($strResultPoints, "nc"), $panelUtils->addCell($strCommand, "nr"));
                  }
                }
              }
            }
          }
        }
      }
      $panelUtils->closeList();

      $resultGradeScale = $elearningExerciseUtils->resultGradeScale();
      $averageCorrectAnswers = $elearningResultUtils->calculateAverageCorrectAnswers($totalCorrectAnswers, $totalQuestions);
      $grade = $elearningResultRangeUtils->calculateGrade($totalCorrectAnswers, $totalQuestions);
      $strResultGrades = $elearningResultUtils->renderResultGrades('', $grade);
      $strResultRatio = $elearningResultUtils->renderResultRatio('', $averageCorrectAnswers, $resultGradeScale);
      $strResultPoints = $elearningResultUtils->renderResultPoints('', $totalPoints);

      $label = $popupUtils->getTipPopup($mlText[23], $mlText[25], 300, 300);
      $panelUtils->addLine();
      $panelUtils->addLine('', '', $panelUtils->addCell($label, "nbr"), $panelUtils->addCell($strResultGrades, "nc"), $panelUtils->addCell($strResultRatio, "nc"), '', $panelUtils->addCell($strResultPoints, "nc"), '');
    }
  }
} else {
  $panelUtils->addLine($panelUtils->addCell($mlText[145], "w"));
}

$str = $panelUtils->render();

printAdminPage($str);

?>
