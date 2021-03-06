<?PHP

require_once("website.php");

$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");
if (!$elearningSubscriptionId) {
  $elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
}

$elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");
if (!$elearningLessonId) {
  $elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
}

if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
  $elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId);
  $userId = $userUtils->getLoggedUserId();
  $elearningSubscriptionUtils->checkIsOpenedUserSubscription($userId, $elearningSubscription);

  $elearningLessonUtils->checkUserLoginForLesson($elearningLesson, $elearningSubscription);
  $str = $elearningLessonUtils->renderLesson($elearningLesson, $elearningSubscription);

  $gTemplate->setPageContent($str);
}

$elearningTemplateModelId = $elearningExerciseUtils->getTemplateModel();
if ($elearningTemplateModelId > 0) {
  $templateModelId = $elearningTemplateModelId;
}

require_once($gTemplatePath . "render.php");

?>
