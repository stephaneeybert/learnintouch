<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

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

  $str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
  printMessage($str);
  return;

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

  $help = $popupUtils->getHelpPopup($mlText[11], 300, 200);
  $panelUtils->setHelp($help);
  $panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/compose.php");
  $panelUtils->openForm($PHP_SELF);
  $strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/exercise/suggestExercises.php", "elearningExerciseName", "elearningExerciseId");
  $panelUtils->addContent($strJsSuggest);
  $panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' id='elearningExerciseName' value='$elearningExerciseName' size='30' />");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[6], $mlText[7], 300, 500);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectQuestionType);
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[8], $mlText[9], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectHintPlacement);
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[29], $mlText[30], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='hideText' $checkedHideText value='1'>");
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[3], $mlText[5], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='textMaxHeight' value='$textMaxHeight' size='4' maxlength='4'>");
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[25], $mlText[26], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='videoUrl' value='$videoUrl' size='30'>");
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[23], $mlText[24], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<textarea name='video' cols='28' rows='4'>$video</textarea>");
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningExercisePageId', $elearningExercisePageId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
