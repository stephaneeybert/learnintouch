<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");
$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
$elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");
$redirectToExercise = LibEnv::getEnvHttpGET("redirectToExercise");

if ($elearningExerciseId) {

  if ($elearningCourseItem = $elearningCourseItemUtils->selectByCourseIdAndExerciseId($elearningCourseId, $elearningExerciseId)) {
    $elearningCourseItemId = $elearningCourseItem->getId();
    $elearningCourseItemUtils->swapWithPrevious($elearningCourseItemId);
  }

} else if ($elearningLessonId) {

  if ($elearningCourseItem = $elearningCourseItemUtils->selectByCourseIdAndLessonId($elearningCourseId, $elearningLessonId)) {
    $elearningCourseItemId = $elearningCourseItem->getId();
    $elearningCourseItemUtils->swapWithPrevious($elearningCourseItemId);
  }

}

if ($redirectToExercise) {
  $str = LibHtml::urlRedirect("$gElearningUrl/exercise/admin.php");
  printContent($str);
  return;
} else {
  $str = LibHtml::urlRedirect("$gElearningUrl/lesson/admin.php");
  printContent($str);
  return;
}

?>
