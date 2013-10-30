<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonParagraphId = LibEnv::getEnvHttpPOST("elearningLessonParagraphId");
  $headline = LibEnv::getEnvHttpPOST("headline");
  $video = LibEnv::getEnvHttpPOST("video");
  $videoUrl = LibEnv::getEnvHttpPOST("videoUrl");
  $elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $elearningExerciseName = LibEnv::getEnvHttpPOST("elearningExerciseName");
  $exerciseTitle = LibEnv::getEnvHttpPOST("exerciseTitle");

  $headline = LibString::cleanString($headline);
  $videoUrl = LibString::cleanString($videoUrl);
  $exerciseTitle = LibString::cleanString($exerciseTitle);

  // The field value can be removed
  if (!trim($elearningExerciseName)) {
    $elearningExerciseId = '';
  }

  // The paragraph headline is required
  if (!$headline) {
    array_push($warnings, $websiteText[3]);
  }

  if (count($warnings) == 0) {

    if ($elearningLessonParagraph = $elearningLessonParagraphUtils->selectById($elearningLessonParagraphId)) {
      $elearningLessonParagraph->setHeadline($headline);
      $elearningLessonParagraph->setVideo($video);
      $elearningLessonParagraph->setVideoUrl($videoUrl);
      $elearningLessonParagraph->setElearningLessonId($elearningLessonId);
      $elearningLessonParagraph->setElearningExerciseId($elearningExerciseId);
      $elearningLessonParagraph->setExerciseTitle($exerciseTitle);
      $elearningLessonParagraphUtils->update($elearningLessonParagraph);
    } else {
      $elearningLessonParagraph = new ElearningLessonParagraph();
      $elearningLessonParagraph->setHeadline($headline);
      $elearningLessonParagraph->setVideo($video);
      $elearningLessonParagraph->setVideoUrl($videoUrl);
      $elearningLessonParagraph->setElearningLessonId($elearningLessonId);
      $listOrder = $elearningLessonParagraphUtils->getNextListOrder($elearningLessonId, '');
      $elearningLessonParagraph->setListOrder($listOrder);
      $elearningLessonParagraph->setElearningExerciseId($elearningExerciseId);
      $elearningLessonParagraph->setExerciseTitle($exerciseTitle);
      $elearningLessonParagraphUtils->insert($elearningLessonParagraph);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/lesson/compose.php");
    printContent($str);
    return;

  }

} else {

  $elearningLessonParagraphId = LibEnv::getEnvHttpGET("elearningLessonParagraphId");
  $elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");
  if (!$elearningLessonId) {
    $elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
  }

  $headline = '';
  $video = '';
  $videoUrl = '';
  $videoUrl = '';
  $elearningExerciseId = '';
  $exerciseTitle = '';
  if ($elearningLessonParagraphId) {
    if ($elearningLessonParagraph = $elearningLessonParagraphUtils->selectById($elearningLessonParagraphId)) {
      $headline = $elearningLessonParagraph->getHeadline();
      $video = $elearningLessonParagraph->getVideo();
      $videoUrl = $elearningLessonParagraph->getVideoUrl();
      $elearningLessonId = $elearningLessonParagraph->getElearningLessonId();
      $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
      $exerciseTitle = $elearningLessonParagraph->getExerciseTitle();
    }
  }

}

// Get the lesson name
$elearningLessonName = '';
if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
  $elearningLessonName = $elearningLesson->getName();
}

// Get the exercise name
$elearningExerciseName = '';
if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $elearningExerciseName = $elearningExercise->getName();
} else {
  $elearningExerciseId = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$help = $popupUtils->getTipPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_QUESTION_MARK_MEDIUM . "' class='no_style_image_icon' title='' alt='' />", $websiteText[11], 300, 500);

$str .= "\n<div style='text-align:right;'>$help</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/lesson/paragraph/edit.php' method='post'>";

$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/lesson/suggestLessons.php", "elearningLessonName", "elearningLessonId");
$str .= $strJsSuggest;
$str .= "\n<input type='hidden' name='elearningLessonId' value='$elearningLessonId' />";

$label = $popupUtils->getTipPopup($websiteText[2], $websiteText[22], 300, 200);
$str .= "<div class='system_label'>$label</div>"
. "<div class='system_field'><input type='text' id='elearningLessonName' value='$elearningLessonName' size='30'></div>";

$str .= "<div class='system_label'>$websiteText[4]</div>"
. "<div class='system_field'><input type='text' name='headline' value='$headline' size='30'></div>";

$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/exercise/suggestExercises.php", "elearningExerciseName", "elearningExerciseId");
$str .= $strJsSuggest;
$str .= "\n<input type='hidden' name='elearningExerciseId' value='$elearningExerciseId' />";

$label = $popupUtils->getTipPopup($websiteText[6], $websiteText[9], 300, 300);
$str .= "<div class='system_label'>$label</div>"
. "<div class='system_field'><input type='text' id='elearningExerciseName' value='$elearningExerciseName' size='30'></div>";

$label = $popupUtils->getTipPopup($websiteText[7], $websiteText[8], 300, 300);
$str .= "<div class='system_label'>$label</div>"
. "<div class='system_field'><input type='text' name='exerciseTitle' value='$exerciseTitle' size='30'></div>";

$label = $popupUtils->getTipPopup($websiteText[23], $websiteText[24], 300, 300);
$str .= "<div class='system_label'>$label</div>"
. "<div class='system_field'><textarea name='video' cols='28' rows='4'>$video</textarea></div>";

$label = $popupUtils->getTipPopup($websiteText[25], $websiteText[26], 300, 300);
$str .= "<div class='system_label'>$label</div>"
. "<div class='system_field'><input type='text' name='videoUrl' value='$videoUrl' size='30'></div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[21]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningLessonParagraphId' value='$elearningLessonParagraphId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/lesson/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[20]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
