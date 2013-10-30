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

  if ($elearningCourseItem = $elearningCourseItemUtils->selectByCourseIdAndLessonId($elearningCourseId, $elearningLessonId)) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    $elearningCourseItem = new ElearningCourseItem();
    $elearningCourseItem->setElearningCourseId($elearningCourseId);
    $elearningCourseItem->setElearningLessonId($elearningLessonId);
    $listOrder = $elearningCourseItemUtils->getNextListOrder($elearningCourseId);
    $elearningCourseItem->setListOrder($listOrder);
    $elearningCourseItemUtils->insert($elearningCourseItem);

    $str = LibHtml::urlRedirect("$gElearningUrl/lesson/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");

}

$name = '';
$description = '';
if ($elearningLessonId) {
  if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
    $name = $elearningLesson->getName();
    $description = $elearningLesson->getDescription();
  }
}

$elearningCourses = $elearningCourseUtils->selectAll();
$elearningCourseList = Array('' => '');
foreach ($elearningCourses as $elearningCourse) {
  $wCourseId = $elearningCourse->getId();
  if (!$elearningCourseItems = $elearningCourseItemUtils->selectByCourseIdAndLessonId($wCourseId, $elearningLessonId)) {
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

$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/admin.php");
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
$panelUtils->addHiddenField('elearningLessonId', $elearningLessonId);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
