<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningSubjectId = LibEnv::getEnvHttpPOST("elearningSubjectId");

  if ($elearningExercises = $elearningExerciseUtils->selectBySubjectId($elearningSubjectId)) {
    foreach ($elearningExercises as $elearningExercise) {
      $elearningExercise->setSubjectId('');
      $elearningExerciseUtils->update($elearningExercise);
    }
  }

  if ($elearningLessons = $elearningLessonUtils->selectBySubjectId($elearningSubjectId)) {
    foreach ($elearningLessons as $elearningLesson) {
      $elearningLesson->setSubjectId('');
      $elearningLessonUtils->update($elearningLesson);
    }
  }

  // Delete
  $elearningSubjectUtils->delete($elearningSubjectId);

  $str = LibHtml::urlRedirect("$gElearningUrl/subject/admin.php");
  printContent($str);
  return;

} else {

  $elearningSubjectId = LibEnv::getEnvHttpGET("elearningSubjectId");

  if ($subject = $elearningSubjectUtils->selectById($elearningSubjectId)) {
    $name = $subject->getName();
    $description = $subject->getDescription();
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/subject/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningSubjectId', $elearningSubjectId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
