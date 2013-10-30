<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
  $exercisesInGarbage = LibEnv::getEnvHttpPOST("exercisesInGarbage");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $exercisesInGarbage = LibString::cleanString($exercisesInGarbage);

  if ($elearningLessonUtils->isLockedForLoggedInAdmin($elearningLessonId)) {
    array_push($warnings, $mlText[9]);
  }

  if (count($warnings) == 0) {

    $elearningLessonUtils->putInGarbage($elearningLessonId, $exercisesInGarbage);

    $str = LibHtml::urlRedirect("$gElearningUrl/lesson/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");

  $name = '';
  $description = '';
  if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
    $name = $elearningLesson->getName();
    $description = $elearningLesson->getDescription();
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
if ($elearningLessonUtils->hasResults($elearningLessonId)) {
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->addCell($mlText[3], "w"));
} else {
  if ($elearningLessonUtils->hasExercises($elearningLessonId)) {
    $lessonExerciseLinks = $elearningLessonUtils->renderLessonExerciseComposeLinks($elearningLessonId, $mlText[7]);
    $panelUtils->addLine();
    $label = $popupUtils->getTipPopup($mlText[8], $mlText[6], 300, 300);
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $lessonExerciseLinks);
    $label = $popupUtils->getTipPopup($mlText[4], $mlText[6], 300, 300);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='exercisesInGarbage' value='1'>");
  }
}
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningLessonId', $elearningLessonId);
$panelUtils->addHiddenField('name', $name);
$panelUtils->addHiddenField('description', $description);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
