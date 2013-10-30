<?PHP

require_once("website.php");

$elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
$elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");

if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
  if (!$elearningExercisePageId) {
    $elearningExercisePageId = $elearningExercisePageUtils->getFirstExercisePage($elearningExercise);
    }

  $str = $elearningExerciseUtils->renderCorrection($elearningExercise, $elearningExercisePageId, $elearningSubscriptionId);

  $gTemplate->setPageContent($str);

  $preferenceUtils->init($dynpageUtils->preferences);
  if ($preferenceUtils->getValue("DYNPAGE_NAME_AS_TITLE")) {
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
