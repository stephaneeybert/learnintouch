<?PHP

require_once("website.php");

$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
if (!$elearningExerciseId) {
  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
}
$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");

$elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId);
$elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId);

$str = '';

if ($elearningExercise) {
  // Check if the exercise requires a login
  $elearningExerciseUtils->checkUserLoginForExercise($elearningExercise, $elearningSubscription);

  // Check if the exercise question input fields are to be reset
  $elearningExerciseUtils->checkResetExercise($elearningExerciseId);

  // Display the exercise introduction or a page of questions
  if ($elearningExerciseUtils->skipExerciseIntroduction($elearningExerciseId) && !$elearningExercisePageId) {
    $elearningExercisePageId = $elearningExercisePageUtils->getFirstExercisePage($elearningExercise);
  }
  if ($elearningExercisePageId) {
    if ($elearningSubscription) {
      $userId = $userUtils->getLoggedUserId();
      $elearningSubscriptionUtils->checkIsOpenedUserSubscription($userId, $elearningSubscription);

      $systemDateTime = $clockUtils->getSystemDateTime();
      if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
        if ($elearningSubscription->getWatchLive()) {
          $userId = $elearningSubscription->getUserId();
          if ($elearningResult = $elearningResultUtils->selectBySubscriptionAndExercise($elearningSubscriptionId, $elearningExerciseId)) {
            $elearningResultId = $elearningResult->getId();
            $sessionElearningResultId = LibSession::getSessionValue(ELEARNING_SESSION_RESULT_ID);
            // Check if the exercise is not currently being done
            if (!$sessionElearningResultId || $sessionElearningResultId != $elearningResultId) {
              $elearningResult->setExerciseDate($systemDateTime);
              $elearningResultUtils->update($elearningResult);
              $elearningResultUtils->deleteQuestionsResults($elearningResultId);
              LibSession::putSessionValue(ELEARNING_SESSION_RESULT_ID, $elearningResultId);
            }
          } else {
            if ($user = $userUtils->selectById($userId)) {
              $elearningResult = new ElearningResult();
              $elearningResult->setSubscriptionId($elearningSubscriptionId);
              $elearningResult->setElearningExerciseId($elearningExerciseId);
              $elearningResult->setExerciseDate($systemDateTime);
              $elearningResult->setEmail($user->getEmail());
              $elearningResult->setFirstname($user->getFirstname());
              $elearningResult->setLastname($user->getLastname());
              $elearningResultUtils->insert($elearningResult);
              $elearningResultId = $elearningResultUtils->getLastInsertId();
              LibSession::putSessionValue(ELEARNING_SESSION_RESULT_ID, $elearningResultId);
            }
          }
          $elearningAssignmentUtils->setElearningResult($elearningSubscriptionId, $elearningExerciseId, $elearningResultId);
        }

        $elearningSubscriptionUtils->saveLastExerciseId($elearningSubscription, $elearningExerciseId);
        $elearningSubscriptionUtils->saveLastExercisePageId($elearningSubscription, $elearningExercisePageId);
        $elearningSubscriptionUtils->saveLastActive($elearningSubscription);
      }
    }

    $str = $elearningExerciseUtils->renderExercise($elearningExercise, $elearningExercisePageId, $elearningSubscriptionId);
  } else {
    $str = $elearningExerciseUtils->renderExerciseIntroduction($elearningExerciseId, $elearningSubscriptionId);
  }
}

$gTemplate->setPageContent($str);

$preferenceUtils->init($dynpageUtils->preferences);
if ($preferenceUtils->getValue("DYNPAGE_NAME_AS_TITLE")) {
  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $name = $elearningExercise->getName();
    if ($name) {
      $gTemplate->setPageTitle($name);
    }
  }
}

$elearningTemplateModelId = $elearningExerciseUtils->getTemplateModel();
if ($elearningTemplateModelId > 0) {
  $templateModelId = $elearningTemplateModelId;
}

require_once($gTemplatePath . "render.php");

?>
