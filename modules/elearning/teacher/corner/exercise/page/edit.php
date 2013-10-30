<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $questionType = LibEnv::getEnvHttpPOST("questionType");
  $hintPlacement = LibEnv::getEnvHttpPOST("hintPlacement");
  $hideText = LibEnv::getEnvHttpPOST("hideText");
  $textMaxHeight = LibEnv::getEnvHttpPOST("textMaxHeight");
  $video = LibEnv::getEnvHttpPOST("video");
  $videoUrl = LibEnv::getEnvHttpPOST("videoUrl");
  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");

  $elearningExercisePageId = LibString::cleanString($elearningExercisePageId);
  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $currentLanguageCode = LibString::cleanString($currentLanguageCode);
  $questionType = LibString::cleanString($questionType);
  $hintPlacement = LibString::cleanString($hintPlacement);
  $hideText = LibString::cleanString($hideText);
  $elearningExerciseId = LibString::cleanString($elearningExerciseId);
  $videoUrl = LibString::cleanString($videoUrl);

  // The content must belong to the user
  if ($elearningExercisePageId && !$elearningExercisePageUtils->createdByUser($elearningExercisePageId, $userId)) {
    array_push($warnings, $websiteText[12]);
  }

  if (count($warnings) == 0) {

    if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      if ($elearningExerciseId != $elearningExercisePage->getElearningExerciseId()) {
        $listOrder = $elearningExercisePageUtils->getNextListOrder($elearningExerciseId);
        $elearningExercisePage->setListOrder($listOrder);
      }
      $elearningExercisePage->setName($name);
      $elearningExercisePage->setDescription($description);
      $elearningExercisePage->setQuestionType($questionType);
      $elearningExercisePage->setHintPlacement($hintPlacement);
      $elearningExercisePage->setHideText($hideText);
      $elearningExercisePage->setTextMaxHeight($textMaxHeight);
      $elearningExercisePage->setVideo($video);
      $elearningExercisePage->setVideoUrl($videoUrl);
      $elearningExercisePage->setElearningExerciseId($elearningExerciseId);
      $elearningExercisePageUtils->update($elearningExercisePage);
    } else {
      $elearningExercisePage = new ElearningExercisePage();
      $elearningExercisePage->setName($name);
      $elearningExercisePage->setDescription($description);
      $elearningExercisePage->setQuestionType($questionType);
      $elearningExercisePage->setHintPlacement($hintPlacement);
      $elearningExercisePage->setHideText($hideText);
      $elearningExercisePage->setTextMaxHeight($textMaxHeight);
      $elearningExercisePage->setVideo($video);
      $elearningExercisePage->setVideoUrl($videoUrl);
      $elearningExercisePage->setElearningExerciseId($elearningExerciseId);
      $listOrder = $elearningExercisePageUtils->getNextListOrder($elearningExerciseId);
      $elearningExercisePage->setListOrder($listOrder);
      $elearningExercisePageUtils->insert($elearningExercisePage);
      $elearningExercisePageId = $elearningExercisePageUtils->getLastInsertId();

      // Add a question for a text to type in
      // It is likely that the content creator will forget about adding a question
      if ($questionType == 'WRITE_TEXT') {
        $elearningQuestion = new ElearningQuestion();
        $elearningQuestion->setElearningExercisePage($elearningExercisePageId);
        $elearningQuestion->setPoints(1);
        $listOrder = $elearningQuestionUtils->getNextListOrder($elearningExercisePageId);
        $elearningQuestion->setListOrder($listOrder);
        $elearningQuestionUtils->insert($elearningQuestion);
      }

      // Set the exercise display in a collapsed state
      LibSession::putSessionValue(ELEARNING_SESSION_DISPLAY_EXERCISE . $elearningExerciseId, ELEARNING_COLLAPSED);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/exercise/compose.php");
    printContent($str);
    return;

  }

} else {

  $elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  $name = '';
  $description = '';
  $questionType = '';
  $hintPlacement = '';
  $hideText = '';
  $textMaxHeight = '';
  $video = '';
  $videoUrl = '';
  if ($elearningExercisePageId) {
    if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
      $name = $elearningExercisePage->getName();
      $description = $elearningExercisePage->getDescription();
      $questionType = $elearningExercisePage->getQuestionType();
      $hintPlacement = $elearningExercisePage->getHintPlacement();
      $hideText = $elearningExercisePage->getHideText();
      $textMaxHeight = $elearningExercisePage->getTextMaxHeight();
      $video = $elearningExercisePage->getVideo();
      $videoUrl = $elearningExercisePage->getVideoUrl();
      $elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
    }
  }

}

$questionTypeList = Array();
foreach ($gElearningQuestionTypes as $wQuestionType => $questionTypeName) {
  $questionTypeList[$wQuestionType] = $questionTypeName;
}
$strSelectQuestionType = LibHtml::getSelectList("questionType", $questionTypeList, $questionType);

$hintPlacementList = Array();
foreach ($gElearningHintPlacements as $wHintPlacement => $hintPlacementName) {
  $hintPlacementList[$wHintPlacement] = $hintPlacementName;
}
$strSelectHintPlacement = LibHtml::getSelectList("hintPlacement", $hintPlacementList, $hintPlacement);

// Get the exercise name
$elearningExerciseName = '';
if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $elearningExerciseName = $elearningExercise->getName();
}

if ($hideText == '1') {
  $checkedHideText = "CHECKED";
} else {
  $checkedHideText = '';
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

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/exercise/page/edit.php' method='post'>";

$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/exercise/suggestExercises.php", "elearningExerciseName", "elearningExerciseId");
$str .= $strJsSuggest;
$str .= "\n<input type='hidden' name='elearningExerciseId' value='$elearningExerciseId' />";

$label = $popupUtils->getTipPopup($websiteText[2], $websiteText[22], 300, 200);
$str .= "<div class='system_label'>$label</div>"
. "<div class='system_field'><input type='text' id='elearningExerciseName' value='$elearningExerciseName' size='30'></div>";

$str .= "<div class='system_label'>$websiteText[4]</div>"
. "<div class='system_field'><input type='text' name='name' value='$name' size='30'></div>";

$str .= "<div class='system_label'>$websiteText[1]</div>"
. "<div class='system_field'><input type='text' name='description' value='$description' size='30' maxlength='255'></div>";

$label = $popupUtils->getTipPopup($websiteText[6], $websiteText[7], 300, 300);
$str .= "<div class='system_label'>$label</div>"
. "<div class='system_field'>$strSelectQuestionType</div>";

$label = $popupUtils->getTipPopup($websiteText[8], $websiteText[9], 300, 300);
$str .= "<div class='system_label'>$label</div>"
. "<div class='system_field'>$strSelectHintPlacement</div>";

$label = $popupUtils->getTipPopup($websiteText[29], $websiteText[30], 300, 300);
$str .= "<div class='system_label'>$label</div>"
. "<div class='system_field'><input type='checkbox' name='hideText' $checkedHideText value='1'></div>";

$label = $popupUtils->getTipPopup($websiteText[3], $websiteText[5], 300, 300);
$str .= "<div class='system_label'>$label</div>"
. "<div class='system_field'><input type='text' name='textMaxHeight' value='$textMaxHeight' size='4' maxlength='4'></div>";

$label = $popupUtils->getTipPopup($websiteText[23], $websiteText[24], 300, 300);
$str .= "<div class='system_label'>$label</div>"
. "<div class='system_field'><textarea name='video' cols='28' rows='4'>$video</textarea></div>";

$label = $popupUtils->getTipPopup($websiteText[25], $websiteText[26], 300, 300);
$str .= "<div class='system_label'>$label</div>"
. "<div class='system_field'><input type='text' name='videoUrl' value='$videoUrl' size='30'></div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[21]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningExercisePageId' value='$elearningExercisePageId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/exercise/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[20]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
