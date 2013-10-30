<?PHP

require_once("website.php");

$elearningExerciseUtils->checkUserLogin();

$str = $elearningSubscriptionUtils->renderTeacherSubscriptions();

$gTemplate->setPageContent($str);

$elearningTemplateModelId = $elearningExerciseUtils->getTemplateModel();
if ($elearningTemplateModelId > 0) {
  $templateModelId = $elearningTemplateModelId;
}

require_once($gTemplatePath . "render.php");

?>
