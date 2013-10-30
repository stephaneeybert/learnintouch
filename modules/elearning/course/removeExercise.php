<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");

  if ($elearningCourseUtils->isLockedForLoggedInAdmin($elearningCourseId)) {
    array_push($warnings, $mlText[9]);
  }

  if (count($warnings) == 0) {

    // Remove the exercise course link
    if ($elearningCourseItem = $elearningCourseItemUtils->selectByCourseIdAndExerciseId($elearningCourseId, $elearningExerciseId)) {
      $elearningCourseItemId = $elearningCourseItem->getId();
      $elearningCourseItemUtils->delete($elearningCourseItemId);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/exercise/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
  $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");

  $name = '';
  $description = '';
  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $name = $elearningExercise->getName();
    $description = $elearningExercise->getDescription();
  }

}

$courseName = '';
$courseDescription = '';
if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
  $courseName = $elearningCourse->getName();
  $courseDescription = $elearningCourse->getDescription();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[2], $mlText[1], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "$courseName $courseDescription");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
$panelUtils->addHiddenField('elearningCourseId', $elearningCourseId);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
