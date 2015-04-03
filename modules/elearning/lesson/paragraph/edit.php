<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonParagraphId = LibEnv::getEnvHttpPOST("elearningLessonParagraphId");
  $headline = LibEnv::getEnvHttpPOST("headline");
  $video = LibEnv::getEnvHttpPOST("video");
  $videoUrl = LibEnv::getEnvHttpPOST("videoUrl");
  $elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
  $elearningLessonHeadingId = LibEnv::getEnvHttpPOST("elearningLessonHeadingId");
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
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    if ($elearningLessonParagraph = $elearningLessonParagraphUtils->selectById($elearningLessonParagraphId)) {
      // When changing the paragraph heading, within a lesson, reassign a new list order
      if ($elearningLessonId == $elearningLessonParagraph->getElearningLessonId() || $elearningLessonHeadingId != $elearningLessonParagraph->getElearningLessonHeadingId()) {
        $listOrder = $elearningLessonParagraphUtils->getNextListOrder($elearningLessonId, $elearningLessonHeadingId);
        $elearningLessonParagraph->setElearningLessonHeadingId($elearningLessonHeadingId);
        $elearningLessonParagraph->setListOrder($listOrder);
      } else if ($elearningLessonId != $elearningLessonParagraph->getElearningLessonId()) {
        // When changing the lesson, reset the heading, as the current paragraph heading only belongs to the
        // model of the lesson of the paragraph
        // This is to avoid having a paragraph, with a heading that does not belong to the model of his new lesson
        $elearningLessonParagraph->setElearningLessonHeadingId('');
      }
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
      $elearningLessonParagraph->setElearningLessonHeadingId($elearningLessonHeadingId);
      $listOrder = $elearningLessonParagraphUtils->getNextListOrder($elearningLessonId, $elearningLessonHeadingId);
      $elearningLessonParagraph->setListOrder($listOrder);
      $elearningLessonParagraph->setElearningExerciseId($elearningExerciseId);
      $elearningLessonParagraph->setExerciseTitle($exerciseTitle);
      $elearningLessonParagraphUtils->insert($elearningLessonParagraph);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/lesson/compose.php");
    printMessage($str);
    return;

  }

} else {

  $elearningLessonParagraphId = LibEnv::getEnvHttpGET("elearningLessonParagraphId");
  $elearningLessonHeadingId = LibEnv::getEnvHttpGET("elearningLessonHeadingId");
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
      if (!$elearningLessonHeadingId) {
        $elearningLessonHeadingId = $elearningLessonParagraph->getElearningLessonHeadingId();
      }
      $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
      $exerciseTitle = $elearningLessonParagraph->getExerciseTitle();
    }
  }

}

// Get the lesson name
$elearningLessonModelId = '';
$elearningLessonName = '';
if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
  $elearningLessonModelId = $elearningLesson->getLessonModelId();
  $elearningLessonName = $elearningLesson->getName();
}

// Get the list of headings if any
$elearningLessonHeadings = $elearningLessonHeadingUtils->selectByElearningLessonModelId($elearningLessonModelId);
$elearningLessonHeadingList = Array();
foreach ($elearningLessonHeadings as $elearningLessonHeading) {
  $wId = $elearningLessonHeading->getId();
  $wName = $elearningLessonHeading->getName();
  $elearningLessonHeadingList[$wId] = $wName;
}
$strSelectLessonHeading = LibHtml::getSelectList("elearningLessonHeadingId", $elearningLessonHeadingList, $elearningLessonHeadingId);

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

$help = $popupUtils->getHelpPopup($mlText[11], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/compose.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/lesson/suggestLessons.php", "elearningLessonName", "elearningLessonId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningLessonId', $elearningLessonId);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' id='elearningLessonName' value='$elearningLessonName' size='30'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $strSelectLessonHeading);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='headline' value='$headline' size='30'>");
$panelUtils->addLine();
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/exercise/suggestExercises.php", "elearningExerciseName", "elearningExerciseId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
$label = $popupUtils->getTipPopup($mlText[6], $mlText[9], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='elearningExerciseName' name='elearningExerciseName' value='$elearningExerciseName' size='30' />");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[7], $mlText[8], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='exerciseTitle' value='$exerciseTitle' size='30'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[23], $mlText[24], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<textarea name='video' cols='28' rows='4'>$video</textarea>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[25], $mlText[26], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='videoUrl' value='$videoUrl' size='30'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningLessonParagraphId', $elearningLessonParagraphId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
