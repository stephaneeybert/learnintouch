<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $maxDuration = LibEnv::getEnvHttpPOST("maxDuration");
  $numberPageTabs = LibEnv::getEnvHttpPOST("numberPageTabs");
  $hideKeyboard = LibEnv::getEnvHttpPOST("hideKeyboard");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $maxDuration = LibString::cleanString($maxDuration);
  $numberPageTabs = LibString::cleanString($numberPageTabs);
  $hideKeyboard = LibString::cleanString($hideKeyboard);

  // The content must belong to the user
  if ($elearningExerciseId && !$elearningExerciseUtils->createdByUser($elearningExerciseId, $userId)) {
    array_push($warnings, $websiteText[10]);
  }

  // The name is required
  if (!$name) {
    array_push($warnings, $websiteText[3]);
  }

  // The name must not already exist
  if ($elearningExercise = $elearningExerciseUtils->selectByName($name)) {
    if ($elearningExerciseId != $elearningExercise->getId()) {
      array_push($warnings, $websiteText[5]);
    }
  }

  // The course is required
  if (!$elearningCourseId) {
    array_push($warnings, $websiteText[4]);
  }

  // Validate the maximum duration if any
  if ($maxDuration && !is_numeric(LibString::stripSpaces($maxDuration))) {
    array_push($warnings, $mlText[11]);
  }

  if (count($warnings) == 0) {

    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
      $elearningExercise->setName($name);
      $elearningExercise->setDescription($description);
      $elearningExercise->setMaxDuration($maxDuration);
      $elearningExercise->setNumberPageTabs($numberPageTabs);
      $elearningExercise->setHideKeyboard($hideKeyboard);
      $elearningExerciseUtils->update($elearningExercise);
    } else {
      $releaseDate = $clockUtils->getSystemDate();

      $elearningExercise = new ElearningExercise();
      $elearningExercise->setName($name);
      $elearningExercise->setDescription($description);
      $elearningExercise->setReleaseDate($releaseDate);
      $elearningExercise->setMaxDuration($maxDuration);
      $elearningExercise->setNumberPageTabs($numberPageTabs);
      $elearningExercise->setHideKeyboard($hideKeyboard);
      $elearningExerciseUtils->insert($elearningExercise);
      $elearningExerciseId = $elearningExerciseUtils->getLastInsertId();
    }

    if (!$elearningCourseItem = $elearningCourseItemUtils->selectByCourseIdAndExerciseId($elearningCourseId, $elearningExerciseId)) {
      $elearningCourseItem = new ElearningCourseItem();
      $elearningCourseItem->setElearningCourseId($elearningCourseId);
      $elearningCourseItem->setElearningExerciseId($elearningExerciseId);
      $listOrder = $elearningCourseItemUtils->getNextListOrder($elearningCourseId);
      $elearningCourseItem->setListOrder($listOrder);
      $elearningCourseItemUtils->insert($elearningCourseItem);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
    printContent($str);
    exit;
  }
 
} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
  $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");

  $name = '';
  $description = '';
  $maxDuration = '';
  $numberPageTabs = '';
  $hideKeyboard = '';
  if ($elearningExerciseId) {
    if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
      $name = $elearningExercise->getName();
      $description = $elearningExercise->getDescription();
      $maxDuration = $elearningExercise->getMaxDuration();
      $numberPageTabs = $elearningExercise->getNumberPageTabs();
      $hideKeyboard = $elearningExercise->getHideKeyboard();
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

$numberPageTabsList = array(
  '0' => '',
  ELEARNING_PAGE_TAB_IS_NUMBER => $websiteText[53],
  ELEARNING_PAGE_TAB_WITH_NUMBER => $websiteText[54],
);
$strSelectNumberPageTab = LibHtml::getSelectList("numberPageTabs", $numberPageTabsList, $numberPageTabs);

if ($hideKeyboard == '1') {
  $checkedHideKeyboard = "CHECKED";
} else {
  $checkedHideKeyboard = '';
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/exercise/edit.php' method='post'>";

$str .= "\n<div class='system_label'>$websiteText[6]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='name' value='$name' size='20' maxlength='50' /></div>";

$str .= "\n<div class='system_label'>$websiteText[8]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='description' value='$description' size='20' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[9]</div>";
$str .= "\n<div class='system_field'>$strSelectCourse</div>";

$label = $userUtils->getTipPopup($websiteText[12], $websiteText[13], 300, 400);
$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='maxDuration' value='$maxDuration' size='3' maxlength='3' /></div>";

$label = $userUtils->getTipPopup($websiteText[20], $websiteText[21], 300, 400);
$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'>$strSelectNumberPageTab</div>";

$label = $userUtils->getTipPopup($websiteText[22], $websiteText[23], 300, 400);
$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'><input type='checkbox' name='hideKeyboard' $checkedHideKeyboard value='1'></div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[7]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningExerciseId' value='$elearningExerciseId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/course/list.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[1]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
