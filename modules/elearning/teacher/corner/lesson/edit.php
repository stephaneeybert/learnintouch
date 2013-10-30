<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The lesson must belong to the user
  if ($elearningLessonId && !$elearningLessonUtils->createdByUser($elearningLessonId, $userId)) {
    array_push($warnings, $websiteText[10]);
  }

  // The name is required
  if (!$name) {
    array_push($warnings, $websiteText[3]);
  }

  // The name must not already exist
  if ($elearningLesson = $elearningLessonUtils->selectByName($name)) {
    if ($elearningLessonId != $elearningLesson->getId()) {
      array_push($warnings, $websiteText[5]);
    }
  }

  // The course is required
  if (!$elearningCourseId) {
    array_push($warnings, $websiteText[4]);
  }

  if (count($warnings) == 0) {

    if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
      $elearningLesson->setName($name);
      $elearningLesson->setDescription($description);
      $elearningLessonUtils->update($elearningLesson);
    } else {
      $releaseDate = $clockUtils->getSystemDate();

      $elearningLesson = new ElearningLesson();
      $elearningLesson->setName($name);
      $elearningLesson->setDescription($description);
      $elearningLesson->setReleaseDate($releaseDate);
      $elearningLessonUtils->insert($elearningLesson);
      $elearningLessonId = $elearningLessonUtils->getLastInsertId();
    }

    if (!$elearningCourseItem = $elearningCourseItemUtils->selectByCourseIdAndLessonId($elearningCourseId, $elearningLessonId)) {
      $elearningCourseItem = new ElearningCourseItem();
      $elearningCourseItem->setElearningCourseId($elearningCourseId);
      $elearningCourseItem->setElearningLessonId($elearningLessonId);
      $listOrder = $elearningCourseItemUtils->getNextListOrder($elearningCourseId);
      $elearningCourseItem->setListOrder($listOrder);
      $elearningCourseItemUtils->insert($elearningCourseItem);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
    printContent($str);
    exit;
  }
 
} else {

  $elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");
  $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");

  $name = '';
  $description = '';
  if ($elearningLessonId) {
    if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
      $name = $elearningLesson->getName();
      $description = $elearningLesson->getDescription();
    }
  }

}

$elearningCourses = $elearningCourseUtils->selectByUserId($userId);
$elearningCourseList = Array('' => '');
foreach ($elearningCourses as $elearningCourse) {
  $wCourseId = $elearningCourse->getId();
  $wName = $elearningCourse->getName();
  $elearningCourseList[$wCourseId] = $wName;
}
$strSelectCourse = LibHtml::getSelectList("elearningCourseId", $elearningCourseList, $elearningCourseId);

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/lesson/edit.php' method='post'>";

$str .= "\n<div class='system_label'>$websiteText[6]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='name' value='$name' size='20' maxlength='50' /></div>";

$str .= "\n<div class='system_label'>$websiteText[8]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='description' value='$description' size='20' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[9]</div>";
$str .= "\n<div class='system_field'>$strSelectCourse</div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[7]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningLessonId' value='$elearningLessonId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/course/list.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[1]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
