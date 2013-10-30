<?PHP

require_once("website.php");

$elearningExerciseUtils->checkUserLogin();

$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");

$elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId);

$userId = $userUtils->getLoggedUserId();

if (!$elearningSubscriptionUtils->isTeacherSubscription($elearningSubscription, $userId)) {
  $elearningSubscriptionUtils->checkIsOpenedUserSubscription($userId, $elearningSubscription);
}

$str = $elearningExerciseUtils->renderParticipantCourse($elearningSubscription);

$gTemplate->setPageContent($str);

$elearningTemplateModelId = $elearningExerciseUtils->getTemplateModel();
if ($elearningTemplateModelId > 0) {
  $templateModelId = $elearningTemplateModelId;
}

require_once($gTemplatePath . "render.php");

?>
