<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $subscribe = LibEnv::getEnvHttpPOST("subscribe");

  $subscribe = LibString::cleanString($subscribe);

  if (!$subscribe) {
    array_push($warnings, $websiteText[4]);
  }

  if ($elearningTeacher = $elearningTeacherUtils->selectByUserId($userId)) {
    array_push($warnings, $websiteText[5]);
  }

  if (count($warnings) == 0) {

    $elearningTeacher = new ElearningTeacher();
    $elearningTeacher->setUserId($userId);
    $elearningTeacherUtils->insert($elearningTeacher);

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php?newlyRegistered=1");
    printContent($str);
    return;

  }

}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<div class='system_comment'>$websiteText[1]</div>";

$str .= "\n<form name='register_form' id='register_form' action='$gElearningUrl/teacher/register.php' method='post'>";

$str .= "\n<div class='system_comment'><span onclick=\"clickAdjacentInputElement(this);\" />$websiteText[2]</span> <input type='checkbox' name='subscribe' value='1' style='vertical-align:middle;'></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['register_form'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[3]</a></div>";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
