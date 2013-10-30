<?PHP

require_once("website.php");

$elearningExerciseUtils->checkUserLogin();

$userId = LibEnv::getEnvHttpGET("userId");

$str = $elearningExerciseUtils->renderParticipantSubscriptions($userId);

$gTemplate->setPageContent($str);

$elearningTemplateModelId = $elearningExerciseUtils->getTemplateModel();
if ($elearningTemplateModelId > 0) {
  $templateModelId = $elearningTemplateModelId;
}

require_once($gTemplatePath . "render.php");

?>
