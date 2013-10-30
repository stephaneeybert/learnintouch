<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningCategoryId = LibEnv::getEnvHttpPOST("elearningCategoryId");

  if ($elearningExercises = $elearningExerciseUtils->selectByCategoryId($elearningCategoryId)) {
    foreach ($elearningExercises as $elearningExercise) {
      $elearningExercise->setCategoryId('');
      $elearningExerciseUtils->update($elearningExercise);
      }
    }

  if ($elearningLessons = $elearningLessonUtils->selectByCategoryId($elearningCategoryId)) {
    foreach ($elearningLessons as $elearningLesson) {
      $elearningLesson->setCategoryId('');
      $elearningLessonUtils->update($elearningLesson);
      }
    }

  // Delete
  $elearningCategoryUtils->delete($elearningCategoryId);

  $str = LibHtml::urlRedirect("$gElearningUrl/category/admin.php");
  printContent($str);
  return;

  } else {

  $elearningCategoryId = LibEnv::getEnvHttpGET("elearningCategoryId");

  if ($category = $elearningCategoryUtils->selectById($elearningCategoryId)) {
    $name = $category->getName();
    $description = $category->getDescription();
    }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/category/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningCategoryId', $elearningCategoryId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
