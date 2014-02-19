<?PHP

require_once("website.php");
require_once($gAdminPath . "includes.php");
require_once($gElearningPath . "includes.php");
require_once($gPeoplePath . "includes.php");
require_once($gUniqueTokenPath . "includes.php");

// The administrator may access this page without being logged in if a unique token is used
// This allows an administrator to access this page by clicking on a link in an email
$tokenName = LibEnv::getEnvHttpGET("tokenName");
$tokenValue = LibEnv::getEnvHttpGET("tokenValue");
if ($uniqueTokenUtils->isValid($tokenName, $tokenValue)) {
  // In case the website email is also the one of a registered admin then log in the admin
  $siteEmail = LibEnv::getEnvHttpGET("siteEmail");
  if ($admin = $adminUtils->selectByEmail($siteEmail)) {
    $login = $admin->getLogin();
    $adminUtils->logIn($login);
  }
} else {
  // If no token is used, then
  // check that the administrator is allowed to use the module
  $adminModuleUtils->checkAdminModule(MODULE_ELEARNING);
}

$mlText = $languageUtils->getMlText(__FILE__);

$preferenceUtils->init($elearningExerciseUtils->preferences);

$elearningResultId = LibEnv::getEnvHttpGET("elearningResultId");

$currentLanguageCode = $languageUtils->getCurrentAdminLanguageCode();

$watchLive = '';

$panelUtils->setHeader($mlText[0], "$gElearningUrl/result/admin.php");

if ($elearningResult = $elearningResultUtils->selectById($elearningResultId)) {
  $elearningExerciseId = $elearningResult->getElearningExerciseId();
  $exerciseDate = $clockUtils->systemToLocalNumericDate($elearningResult->getExerciseDate());
  $exerciseTime = $clockUtils->dateTimeToSystemTime($elearningResult->getExerciseDate());
  $exerciseElapsedTime = $elearningResult->getExerciseElapsedTime();
  $firstname = $elearningResult->getFirstname();
  $lastname = $elearningResult->getLastname();
  $message = $elearningResult->getMessage();
  $comment = $elearningResult->getComment();
  $email = $elearningResult->getEmail();
  $elearningSubscriptionId = $elearningResult->getSubscriptionId();

  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    $userId = $elearningSubscription->getUserId();
    $watchLive = $elearningSubscription->getWatchLive();
    if ($user = $userUtils->selectById($userId)) {
      $firstname = $user->getFirstname();
      $lastname = $user->getLastname();
      $email = $user->getEmail();
    }
  }

  // Get the exercise details
  // If no exercise is found for the result then delete it
  if (!$elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $elearningResultUtils->deleteResult($elearningResultId);
    $str .= LibHtml::urlRedirect("$gElearningUrl/result/admin.php", $gRedirectDelay);
    printMessage($str);
    return;
  }

  $exerciseName = $elearningExerciseUtils->renderExerciseComposeLink($elearningExerciseId, $mlText[18]);

  $description = $elearningExercise->getDescription();

  $maxDuration = $elearningExerciseUtils->getMaximumDuration($elearningExerciseId);
  $elapsedTime = $elearningExerciseUtils->renderElapsedTime($exerciseElapsedTime, 0, $maxDuration);

  $strDurations = '';
  if ($maxDuration > 0) {
    $strDurations .= $mlText[4] . ' <b>' . $maxDuration . ' mn</b><br>';
  }
  if ($exerciseElapsedTime > 0) {
    $strDurations .= $mlText[5] . ' <b>' . $elapsedTime . '</b>';
  }

  $resultTotals = $elearningResultUtils->getExerciseTotals($elearningExerciseId, $elearningResultId);
  $labelGrade = $popupUtils->getTipPopup($mlText[1], $mlText[8], 300, 300);
  $labelRatio = $popupUtils->getTipPopup($mlText[23], $mlText[24], 300, 300);
  $labelAnswers = $popupUtils->getTipPopup($mlText[25], $mlText[26], 300, 300);
  $labelPoints = $popupUtils->getTipPopup($mlText[9], $mlText[15], 300, 300);
  $strGrade = "<table border='0'>";
  $strGrade .= "<tr><td></td><td align='center' style='white-space:nowrap;'><b>$labelGrade</b></td><td align='center' style='white-space:nowrap;'><b>$labelRatio</b></td><td align='center' style='white-space:nowrap;'><b>$labelAnswers</b></td><td align='center' style='white-space:nowrap;'><b>$labelPoints</b></td></tr>";

  $nbReadingQuestions = $elearningResultUtils->getResultNbReadingQuestions($resultTotals);
  $nbWritingQuestions = $elearningResultUtils->getResultNbWritingQuestions($resultTotals);
  $nbListeningQuestions = $elearningResultUtils->getResultNbListeningQuestions($resultTotals);

  if ($nbWritingQuestions > 0 || $nbListeningQuestions > 0) {
    $nbCorrectReadingAnswers = $elearningResultUtils->getResultNbCorrectReadingAnswers($resultTotals);
    $nbIncorrectReadingAnswers = $elearningResultUtils->getResultNbIncorrectReadingAnswers($resultTotals);
    $nbCorrectWritingAnswers = $elearningResultUtils->getResultNbCorrectWritingAnswers($resultTotals);
    $nbIncorrectWritingAnswers = $elearningResultUtils->getResultNbIncorrectWritingAnswers($resultTotals);
    $nbCorrectListeningAnswers = $elearningResultUtils->getResultNbCorrectListeningAnswers($resultTotals);
    $nbIncorrectListeningAnswers = $elearningResultUtils->getResultNbIncorrectListeningAnswers($resultTotals);
    $nbReadingPoints = $elearningResultUtils->getResultNbReadingPoints($resultTotals);
    $nbWritingPoints = $elearningResultUtils->getResultNbWritingPoints($resultTotals);
    $nbListeningPoints = $elearningResultUtils->getResultNbListeningPoints($resultTotals);

    $strResultRatio = $elearningResultUtils->renderReadingResultRatio($elearningResultId, $nbCorrectReadingAnswers, $nbReadingQuestions);
    $strResultAnswers = $elearningResultUtils->renderReadingResultAnswers($elearningResultId, $nbCorrectReadingAnswers, $nbIncorrectReadingAnswers, $nbReadingQuestions);
    $strResultPoints = $elearningResultUtils->renderReadingResultPoints($elearningResultId, $nbReadingPoints);
    $strGrade .= "<tr><td><b>$mlText[10]</b></td><td align='center' style='white-space:nowrap;'><b></b></td><td align='center' style='white-space:nowrap;'><b>$strResultRatio</b></td><td align='center' style='white-space:nowrap;'><b>$strResultAnswers</b></td><td align='center' style='white-space:nowrap;'><b>$strResultPoints</b></td></tr>";

    $strResultRatio = $elearningResultUtils->renderWritingResultRatio($elearningResultId, $nbCorrectWritingAnswers, $nbWritingQuestions);
    $strResultAnswers = $elearningResultUtils->renderWritingResultAnswers($elearningResultId, $nbCorrectWritingAnswers, $nbIncorrectWritingAnswers, $nbWritingQuestions);
    $strResultPoints = $elearningResultUtils->renderWritingResultPoints($elearningResultId, $nbWritingPoints);
    $strGrade .= "<tr><td><b>$mlText[11]</b></td><td align='center' style='white-space:nowrap;'><b></b></td><td align='center' style='white-space:nowrap;'><b>$strResultRatio</b></td><td align='center' style='white-space:nowrap;'><b>$strResultAnswers</b></td><td align='center' style='white-space:nowrap;'><b>$strResultPoints</b></td></tr>";

    $strResultRatio = $elearningResultUtils->renderListeningResultRatio($elearningResultId, $nbCorrectListeningAnswers, $nbListeningQuestions);
    $strResultAnswers = $elearningResultUtils->renderListeningResultAnswers($elearningResultId, $nbCorrectListeningAnswers, $nbIncorrectListeningAnswers, $nbListeningQuestions);
    $strResultPoints = $elearningResultUtils->renderListeningResultPoints($elearningResultId, $nbListeningPoints);
    $strGrade .= "<tr><td><b>$mlText[12]</b></td><td align='center' style='white-space:nowrap;'><b></b></td><td align='center' style='white-space:nowrap;'><b>$strResultRatio</b></td><td align='center' style='white-space:nowrap;'><b>$strResultAnswers</b></td><td align='center' style='white-space:nowrap;'><b>$strResultPoints</b></td></tr>";
  }

  $nbQuestions = $elearningResultUtils->getResultNbQuestions($resultTotals);
  $nbIncorrectAnswers = $elearningResultUtils->getResultNbIncorrectAnswers($resultTotals);
  $nbCorrectAnswers = $elearningResultUtils->getResultNbCorrectAnswers($resultTotals);
  $nbPoints = $elearningResultUtils->getResultNbPoints($resultTotals);
  $grade = $elearningResultRangeUtils->calculateGrade($nbCorrectAnswers, $nbQuestions);
  $nbStars = $elearningResultUtils->renderStars($nbQuestions, $nbCorrectAnswers);

  $strResultGrades = $elearningResultUtils->renderResultGrades($elearningResultId, $grade, $nbCorrectAnswers, $nbQuestions, $nbPoints);
  $strResultRatio = $elearningResultUtils->renderResultRatio($elearningResultId, $nbCorrectAnswers, $nbQuestions);
  $strResultAnswers = $elearningResultUtils->renderResultAnswers($elearningResultId, $nbCorrectAnswers, $nbIncorrectAnswers, $nbQuestions);
  $strResultPoints = $elearningResultUtils->renderResultPoints($elearningResultId, $nbPoints);

  $strGrade .= "<tr><td><b>$mlText[13]</b></td><td align='center' style='white-space:nowrap;'><b>$strResultGrades</b></td><td align='center' style='white-space:nowrap;'><b>$strResultRatio</b></td><td align='center' style='white-space:nowrap;'><b>$strResultAnswers</b></td><td align='center' style='white-space:nowrap;'><b>$strResultPoints</b></td></tr>";
  $strGrade .= "</table>";

  $scoringId = $elearningExercise->getScoringId();
  $strScoring = '';
  if ($elearningScoring = $elearningScoringUtils->selectById($scoringId)) {
    if ($nbQuestions > 0) {
      $resultScore = $nbCorrectAnswers * 100 / $nbQuestions;
    } else {
      $resultScore = 0;
    }

    if ($elearningScoringRange = $elearningScoringUtils->getScoringMatch($scoringId, $resultScore)) {
      $score = $languageUtils->getTextForLanguage($elearningScoringRange->getScore(), $currentLanguageCode);
      $advice = $languageUtils->getTextForLanguage($elearningScoringRange->getAdvice(), $currentLanguageCode);
      $proposal = $languageUtils->getTextForLanguage($elearningScoringRange->getProposal(), $currentLanguageCode);

      $strScoring = "<div>$score</div>";
      $strScoring .= "<div>$advice</div>";
      $strScoring .= "<div>$proposal</div>";
    }
  }

  $strDetails = "<b>$mlText[27]</b> $firstname $lastname"
    . "<br /><b>$mlText[29]</b> <a href='mailto:$email'>$email</a>"
    . "<br /><b>$mlText[2]</b> $exerciseName"
    . "<br /><b>$mlText[3]</b> $exerciseDate"
    . "<br />$strDurations"
    . "<br />$strScoring";

  if (trim($message)) {
    $strDetails .= "<br><b>$mlText[7]</b> $message";
  }

  $strDetails .= '<br><b>' . "<a href='$gElearningUrl/result/comment.php?elearningResultId=$elearningResultId' $gJSNoStatus>" . "<img border='0' src='$gCommonImagesUrl/$gImageNote' title='$mlText[17]'></a> " . $mlText[16] . '</b>' . ' ' . $comment;

  $strCommand = ''
    . " <a href='$gElearningUrl/result/comment.php?elearningResultId=$elearningResultId' $gJSNoStatus>" . "<img border='0' src='$gCommonImagesUrl/$gImageNote' title='$mlText[17]'></a>"
    . " <a href='$gElearningUrl/result/send_by_admin.php?elearningResultId=$elearningResultId' $gJSNoStatus>" . "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[28]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$mlText[6]'>", "$gElearningUrl/result/adminPrint.php?elearningResultId=$elearningResultId", 600, 600)
    . " <a href='$gElearningUrl/result/delete.php?elearningResultId=$elearningResultId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[19]'></a>";

  $strResults = $strGrade;
  $strResults .= $nbStars;
  $labelLiveResults = $popupUtils->getTipPopup($mlText[30], $mlText[31], 300, 300);
  $strLiveResults = "<br /><br /><b>" . $labelLiveResults . '</b> ' . $elearningResultUtils->renderExerciseResultsGraph($elearningResultId, $nbQuestions, $nbCorrectAnswers, $nbIncorrectAnswers, true, true, '');
  $strResults .= $strLiveResults . " <img id='" . ELEARNING_DOM_ID_INACTIVE . $elearningSubscriptionId . '_' . $elearningExerciseId . "' src='$gCommonImagesUrl/$gImageLightOrangeSmallBlink' title='' alt='' style='visibility: hidden;' />" . '<br/>';

  $elearningExercisePages = $elearningExercisePageUtils->selectByExerciseId($elearningExerciseId);
  foreach ($elearningExercisePages as $elearningExercisePage) {
    $elearningExercisePageId = $elearningExercisePage->getId();
    $exercise_pageName = $elearningExercisePage->getName();
    $exercise_pageDescription = $elearningExercisePage->getDescription();
    $strResults .= "<br /><br /><b>$exercise_pageName</b>";
    if ($exercise_pageDescription) {
      $strResults .= " ($exercise_pageDescription)";
    }
    $elearningQuestions = $elearningQuestionUtils->selectByExercisePage($elearningExercisePageId);
    foreach ($elearningQuestions as $elearningQuestion) {
      $question = $elearningQuestion->getQuestion();
      $points = $elearningQuestion->getPoints();
      $elearningQuestionId = $elearningQuestion->getId();

      // Render all the possible solutions
      $allPossibleSolutions = $elearningSolutionUtils->getQuestionSolutions($elearningQuestionId);

      // Check if the question was correctly answered
      $isCorrect = $elearningResultUtils->isACorrectAnswer($elearningResultId, $elearningQuestionId);
      $isAnswered = $elearningResultUtils->isAnswered($elearningResultId, $elearningQuestionId);
      $thumbImage = '';
      if (!$elearningExercisePageUtils->typeIsWriteText($elearningExercisePage)) {
        if ($isCorrect) {
          $thumbImage = " <img border='0' src='$gImagesUserUrl/" . IMAGE_ELEARNING_ANSWER_TRUE . "' title=''>";
        } else if ($isAnswered) {
          $thumbImage = " <img border='0' src='$gImagesUserUrl/" . IMAGE_ELEARNING_ANSWER_FALSE . "' title=''>";
        }
      }

      $participantAnswers = "<span id='participantAnswers_$elearningQuestionId'>". $elearningQuestionResultUtils->renderParticipantAnswers($elearningResultId, $elearningQuestionId, $isCorrect) . '</span>';

      if (strstr($question, ELEARNING_ANSWER_MCQ_MARKER)) {
        $question = str_replace(ELEARNING_ANSWER_MCQ_MARKER, $participantAnswers, $question);
      } else {
        $question .= $participantAnswers;
      }

      $strResults .= "<br/>- " . $question;

      $strResults .= ' ' . "<span id='thumbImage_$elearningQuestionId'>$thumbImage</span>";

      if ($isCorrect) {
        $strResults .= "<br/><span id='points_$elearningQuestionId'>" . $mlText[22] . ' ' . $points . '</span>';
      } else {
        $strResults .= "<br/><span id='solutions_$elearningQuestionId'>" . $mlText[21] . ' ' . $allPossibleSolutions . '</span>';
      }
    }
  }

  $panelUtils->addLine($panelUtils->addCell($strDetails, "t"), $strResults, $panelUtils->addCell($strCommand, "nr"));
}

if ($watchLive) {
  $strLiveResultIds = $elearningResultId;
  $strLiveResultJs = $elearningResultUtils->renderLiveResultJs();
  $panelUtils->addContent($strLiveResultJs);
}

$str = $panelUtils->render();

printAdminPage($str);

?>
