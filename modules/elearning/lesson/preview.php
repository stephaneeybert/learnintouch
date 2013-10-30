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

$strCss = $templateUtils->renderPreviewCssProperties();

$str = $templateUtils->renderCommonJavascripts();

if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
  $elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId);
  $elearningLessonUtils->checkUserLoginForLesson($elearningLesson, $elearningSubscription);
  $str .= $elearningLessonUtils->renderLesson($elearningLesson, $elearningSubscription);
}

printContent($str, $strCss);

?>
