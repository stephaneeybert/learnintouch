<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");

  if (!$elearningCourseId) {
    array_push($warnings, $mlText[6]);
  }

  if ($elearningCourseUtils->isLockedForLoggedInAdmin($elearningCourseId)) {
    array_push($warnings, $mlText[9]);
  }

  if ($elearningCourseItem = $elearningCourseItemUtils->selectByCourseIdAndExerciseId($elearningCourseId, $elearningExerciseId)) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    $elearningCourseItem = new ElearningCourseItem();
    $elearningCourseItem->setElearningCourseId($elearningCourseId);
    $elearningCourseItem->setElearningExerciseId($elearningExerciseId);
    $listOrder = $elearningCourseItemUtils->getNextListOrder($elearningCourseId);
    $elearningCourseItem->setListOrder($listOrder);
    $elearningCourseItemUtils->insert($elearningCourseItem);

    $str = LibHtml::urlRedirect("$gElearningUrl/exercise/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

}

$name = '';
$description = '';
if ($elearningExerciseId) {
  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $name = $elearningExercise->getName();
    $description = $elearningExercise->getDescription();
  }
}

$elearningCourses = $elearningCourseUtils->selectAll();
$elearningCourseList = Array('' => '');
foreach ($elearningCourses as $elearningCourse) {
  $wCourseId = $elearningCourse->getId();
  if (!$elearningCourseItems = $elearningCourseItemUtils->selectByCourseIdAndExerciseId($wCourseId, $elearningExerciseId)) {
    $wName = $elearningCourse->getName();
    $elearningCourseList[$wCourseId] = $wName;
  }
}
$strSelectCourse = LibHtml::getSelectList("elearningCourseId", $elearningCourseList);

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
$label = $popupUtils->getTipPopup($mlText[2], $mlText[1], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectCourse);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
