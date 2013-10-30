<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$adminUtils->checkSuperAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  // Delete all exercises from the garbage
  if ($elearningExercises = $elearningExerciseUtils->selectGarbage()) {
    foreach ($elearningExercises as $elearningExercise) {
      $elearningExerciseId = $elearningExercise->getId();
      $elearningExerciseUtils->deleteExercise($elearningExerciseId);
      }
    }

  $str = LibHtml::urlRedirect("$gElearningUrl/exercise/garbage.php");
  printMessage($str);
  return;

  } else {

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/garbage.php");
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
