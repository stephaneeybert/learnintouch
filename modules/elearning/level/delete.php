<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLevelId = LibEnv::getEnvHttpPOST("elearningLevelId");

  if ($elearningExercises = $elearningExerciseUtils->selectByLevelId($elearningLevelId)) {
    foreach ($elearningExercises as $elearningExercise) {
      $elearningExercise->setLevelId('');
      $elearningExerciseUtils->update($elearningExercise);
    }
  }

  if ($elearningLessons = $elearningLessonUtils->selectByLevelId($elearningLevelId)) {
    foreach ($elearningLessons as $elearningLesson) {
      $elearningLesson->setLevelId('');
      $elearningLessonUtils->update($elearningLesson);
    }
  }

  $elearningLevelUtils->delete($elearningLevelId);

  $str = LibHtml::urlRedirect("$gElearningUrl/level/admin.php");
  printContent($str);
  return;

} else {

  $elearningLevelId = LibEnv::getEnvHttpGET("elearningLevelId");

  if ($level = $elearningLevelUtils->selectById($elearningLevelId)) {
    $name = $level->getName();
    $description = $level->getDescription();
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/level/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningLevelId', $elearningLevelId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
