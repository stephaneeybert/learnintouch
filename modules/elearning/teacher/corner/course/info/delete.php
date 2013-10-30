<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$userUtils->checkValidUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningCourseInfoId = LibEnv::getEnvHttpPOST("elearningCourseInfoId");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");

  // The course must belong to the user
  if ($elearningCourseId && !$elearningCourseUtils->createdByUser($elearningCourseId, $userId)) {
    array_push($warnings, $websiteText[10]);
  }

  // The information must belong to the user
  if ($elearningCourseInfoId && !$elearningCourseInfoUtils->createdByUser($elearningCourseInfoId, $userId)) {
    array_push($warnings, $websiteText[10]);
  }

  if (count($warnings) == 0) {

    $elearningCourseInfoUtils->deleteCourseInfo($elearningCourseInfoId);

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/info/list.php");
    printContent($str);
    exit;

  }

} else {

  $elearningCourseInfoId = LibEnv::getEnvHttpGET("elearningCourseInfoId");

}

$headline = '';
$information = '';
$elearningCourseId = '';
if ($elearningExercise = $elearningCourseInfoUtils->selectById($elearningCourseInfoId)) {
  $headline = $elearningExercise->getHeadline();
  $information = $elearningExercise->getInformation();
  $elearningCourseId = $elearningExercise->getElearningCourseId();
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/course/info/delete.php' method='post'>";

$str .= "\n<div class='system_label'>$websiteText[4]</div>";
$str .= "\n<div class='system_field'>$headline</div>";

$str .= "\n<div class='system_label'>$websiteText[5]</div>";
$str .= "\n<div class='system_field'>$information</div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[6]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningCourseInfoId' value='$elearningCourseInfoId' />";
$str .= "\n<input type='hidden' name='elearningCourseId' value='$elearningCourseId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/course/info/list.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[7]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
