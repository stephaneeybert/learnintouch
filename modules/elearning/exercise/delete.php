<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  if ($elearningCourseItems = $elearningCourseItemUtils->selectByExerciseId($elearningExerciseId)) {
    $courseNames = ' ';
    foreach ($elearningCourseItems as $elearningCourseItem) {
      $elearningCourseId = $elearningCourseItem->getElearningCourseId();
      if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
        if (trim($courseNames)) {
          $courseNames .= ', ';
        }
        $courseNames .= '"' . $elearningCourse->getName() . '"';
      }
    }
    array_push($warnings, $mlText[6] . $courseNames);
    array_push($warnings, $mlText[7]);
  }

  if ($elearningLessonParagraphs = $elearningLessonParagraphUtils->selectByExerciseId($elearningExerciseId)) {
    $lessonNames = ' ';
    foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
      $elearningLessonId = $elearningLessonParagraph->getElearningLessonId();
      if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
        if (trim($lessonNames)) {
          $lessonNames .= ', ';
        }
        $lessonNames .= '"' . $elearningLesson->getName() . '"';
      }
    }
    array_push($warnings, $mlText[8] . $lessonNames);
    array_push($warnings, $mlText[9]);
  }

  if ($elearningExerciseUtils->isLockedForLoggedInAdmin($elearningExerciseId)) {
    array_push($warnings, $mlText[4]);
  }

  if (count($warnings) == 0) {

    $elearningExerciseUtils->putInGarbage($elearningExerciseId);

    $str = LibHtml::urlRedirect("$gElearningUrl/exercise/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

  $name = '';
  $description = '';
  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $name = $elearningExercise->getName();
    $description = $elearningExercise->getDescription();
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
if ($elearningResultUtils->exerciseHasResults($elearningExerciseId)) {
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->addCell($mlText[3], "w"));
}
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
$panelUtils->addHiddenField('name', $name);
$panelUtils->addHiddenField('description', $description);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
