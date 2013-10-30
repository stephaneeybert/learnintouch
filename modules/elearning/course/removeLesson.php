<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");

  if ($elearningCourseUtils->isLockedForLoggedInAdmin($elearningCourseId)) {
    array_push($warnings, $mlText[9]);
  }

  if (count($warnings) == 0) {

    // Remove the lesson course links
    if ($elearningCourseItem = $elearningCourseItemUtils->selectByCourseIdAndLessonId($elearningCourseId, $elearningLessonId)) {
      $elearningCourseItemId = $elearningCourseItem->getId();
      $elearningCourseItemUtils->delete($elearningCourseItemId);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/lesson/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");
  $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");

}

$name = '';
$description = '';
if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
  $name = $elearningLesson->getName();
  $description = $elearningLesson->getDescription();
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

$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/admin.php");
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
$panelUtils->addHiddenField('elearningLessonId', $elearningLessonId);
$panelUtils->addHiddenField('elearningCourseId', $elearningCourseId);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
