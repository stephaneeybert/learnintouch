<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningQuestionId = LibEnv::getEnvHttpPOST("elearningQuestionId");
  $question = LibEnv::getEnvHttpPOST("question");
  $points = LibEnv::getEnvHttpPOST("points");

  $question = LibString::cleanString($question);
  $points = LibString::cleanString($points);

  // The content must belong to the user
  if ($elearningQuestionId && !$elearningQuestionUtils->createdByUser($elearningQuestionId, $userId)) {
    array_push($warnings, $websiteText[11]);
  }

  if (count($warnings) == 0) {

    $elearningQuestionUtils->duplicate($elearningQuestionId, '', $question, $points);

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/exercise/compose.php");
    printContent($str);
    return;

  }

} else {

  $elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");

  $question = '';
  $points = '';
  if ($elearningQuestionId) {
    if ($elearningQuestion = $elearningQuestionUtils->selectById($elearningQuestionId)) {
      $question = $elearningQuestion->getQuestion();
      $points = $elearningQuestion->getPoints();
    }
  }

} 

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='duplicate' id='duplicate' action='$gElearningUrl/teacher/corner/exercise/question/duplicate.php' method='post'>";

$str .= "\n<div class='system_label'>$websiteText[1]</div>";
$str .= "\n<div class='system_field'><input type='text' name='question' value='$question' size='30'></div>";

$str .= "\n<div class='system_label'>$websiteText[5]</div>";
$str .= "\n<div class='system_field'><input type='text' name='points' value='$points' size='3'></div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['duplicate'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[7]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningQuestionId' value='$elearningQuestionId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/exercise/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[10]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
