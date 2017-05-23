<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $elearningCourseInfoId = LibEnv::getEnvHttpPOST("elearningCourseInfoId");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
  $headline = LibEnv::getEnvHttpPOST("headline");
  $information = LibEnv::getEnvHttpPOST("information");

  $headline = LibString::cleanString($headline);
  $information = LibString::cleanHtmlString($information);

  // The course must belong to the user
  if ($elearningCourseId && !$elearningCourseUtils->createdByUser($elearningCourseId, $userId)) {
    array_push($warnings, $websiteText[10]);
  }

  // The headline is required
  if (!$headline) {
    array_push($warnings, $websiteText[3]);
  }

  if (count($warnings) == 0) {

    if ($elearningCourseInfo = $elearningCourseInfoUtils->selectById($elearningCourseInfoId)) {
      $elearningCourseInfo->setHeadline($headline);
      $elearningCourseInfo->setInformation($information);
      $elearningCourseInfoUtils->update($elearningCourseInfo);
    } else {
      $elearningCourseInfo = new ElearningCourseInfo();
      $elearningCourseInfo->setHeadline($headline);
      $elearningCourseInfo->setInformation($information);
      $listOrder = $elearningCourseInfoUtils->getNextListOrder($elearningCourseId);
      $elearningCourseInfo->setListOrder($listOrder);
      $elearningCourseInfo->setElearningCourseId($elearningCourseId);
      $elearningCourseInfoUtils->insert($elearningCourseInfo);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/info/list.php");
    printContent($str);
    exit;
  }

} else {

  $elearningCourseInfoId = LibEnv::getEnvHttpGET("elearningCourseInfoId");
  $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");

  $headline = '';
  $information = '';
  if ($elearningCourseInfoId) {
    if ($elearningCourseInfo = $elearningCourseInfoUtils->selectById($elearningCourseInfoId)) {
      $headline = $elearningCourseInfo->getHeadline();
      $information = $elearningCourseInfo->getInformation();
      $elearningCourseId = $elearningCourseInfo->getElearningCourseId();
    }
  }

}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/course/info/edit.php' method='post'>";

$str .= "\n<div class='system_label'>$websiteText[6]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='headline' value='$headline' size='30' maxlength='255' /></div>";

include($gHtmlEditorPath . "CKEditorUtils.php");
$contentEditor = new CKEditorUtils();
$contentEditor->languageUtils = $languageUtils;
$contentEditor->commonUtils = $commonUtils;
$contentEditor->load();
$contentEditor->withReducedToolbar();
$strEditor = $contentEditor->render();
$strEditor .= $contentEditor->renderInstance("information", $information);
$str .= "\n<div class='system_label'>$websiteText[8]</div>";
$str .= "\n<div class='system_field'>$strEditor</div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[7]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningCourseId' value='$elearningCourseId' />";
$str .= "\n<input type='hidden' name='elearningCourseInfoId' value='$elearningCourseInfoId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/course/info/list.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[13]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
