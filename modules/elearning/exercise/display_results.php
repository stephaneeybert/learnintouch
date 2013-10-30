<?PHP

require_once("website.php");

$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");
$email = LibEnv::getEnvHttpGET("email");

$email = urldecode($email);

$str = $elearningExerciseUtils->renderResult($elearningExerciseId, $elearningSubscriptionId, $email);

$gTemplate->setPageContent($str);

$elearningTemplateModelId = $elearningExerciseUtils->getTemplateModel();
if ($elearningTemplateModelId > 0) {
  $templateModelId = $elearningTemplateModelId;
}

require_once($gTemplatePath . "render.php");

?>
