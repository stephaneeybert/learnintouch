<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");
if (!$elearningLessonId) {
  $elearningLessonId = LibSession::getSessionValue(ELEARNING_SESSION_LESSON);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_LESSON, $elearningLessonId);
}

$elearningLessonParagraphId = LibEnv::getEnvHttpGET("elearningLessonParagraphId");
$elearningLessonHeadingId = LibEnv::getEnvHttpGET("elearningLessonHeadingId");
$deleteLessonParagraph = LibEnv::getEnvHttpGET("deleteLessonParagraph");

if ($deleteLessonParagraph && $elearningLessonParagraphId) {
  $elearningLessonParagraphUtils->deleteParagraph($elearningLessonParagraphId);
}

$swapWithPrevious = LibEnv::getEnvHttpGET("swapWithPrevious");
$swapWithNext = LibEnv::getEnvHttpGET("swapWithNext");

if ($swapWithPrevious && $elearningLessonId) {
  $elearningLessonParagraphUtils->swapWithPrevious($elearningLessonParagraphId);
} else if ($swapWithNext && $elearningLessonId) {
  $elearningLessonParagraphUtils->swapWithNext($elearningLessonParagraphId);
}

$name = '';
$description = '';
$elearningLessonModelId = '';
if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
  $name = $elearningLesson->getName();
  $description = $elearningLesson->getDescription();
  $elearningLessonModelId = $elearningLesson->getLessonModelId();
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/admin.php");
$help = $popupUtils->getHelpPopup($mlText[29], 300, 300);
$panelUtils->setHelp($help);

$strConfirmDelete = <<<HEREDOC
<script type='text/javascript'>
function confirmDelete() {
  confirmation = confirm('$mlText[15]');
  if (confirmation) {
    return(true);
  }

  return(false);
}

</script>
HEREDOC;
$panelUtils->addContent($strConfirmDelete);

$strCommand = "<a href='$gElearningUrl/lesson/edit.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[18]'></a>"
  . ' ' . "<a href='$gElearningUrl/lesson/introduction.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCompose' title='$mlText[19]'></a>"
  . ' ' . "<a href='$gElearningUrl/lesson/instructions.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageInstructions' title='$mlText[14]'></a>"
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[41]'>", "$gElearningUrl/lesson/image.php?elearningLessonId=$elearningLessonId", 600, 600)
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$mlText[42]'>", "$gElearningUrl/lesson/audio.php?elearningLessonId=$elearningLessonId", 600, 600)
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[34]'>", "$gElearningUrl/lesson/display_lesson.php?elearningLessonId=$elearningLessonId", 600, 600)
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$mlText[11]'>", "$gElearningUrl/lesson/print_lesson.php?elearningLessonId=$elearningLessonId", 600, 600)
  . " <a href='$gElearningUrl/lesson/pdf.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImagePdf' title='$mlText[11]'></a>";

$strLessonName = $name;
if ($description) {
  $strLessonName .= ' - ' . $description;
}

$modelName = '';
$instructions = '';
if ($elearningLessonModel = $elearningLessonModelUtils->selectById($elearningLessonModelId)) {
  $modelName = $elearningLessonModel->getName();
  $modelName = "<a href='$gElearningUrl/lesson/model/compose.php?elearningLessonModelId=$elearningLessonModelId' $gJSNoStatus>$modelName <img border='0' src='$gCommonImagesUrl/$gImageModel' title='$mlText[12]'></a>";
  $currentLanguageCode = $languageUtils->getCurrentAdminLanguageCode();
  $instructions = $languageUtils->getTextForLanguage($elearningLessonModel->getInstructions(), $currentLanguageCode);
}
$strModel = '<b>' . $mlText[6] . '</b> ' . $modelName;

$panelUtils->addLine('<b>' . $mlText[4] . '</b> ' . $strLessonName, $strModel, '', $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();
if ($instructions) {
  $panelUtils->addLine($instructions, '');
  $panelUtils->addLine();
}

$panelUtils->openList();
if ($elearningLessonHeadings = $elearningLessonHeadingUtils->selectByElearningLessonModelId($elearningLessonModelId)) {
  $panelUtils->addLine($panelUtils->addCell($mlText[10], "nb"), $panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($mlText[3], "nb"), '');

  foreach ($elearningLessonHeadings as $elearningLessonHeading) {
    $elearningLessonHeadingId = $elearningLessonHeading->getId();
    $headingName = $elearningLessonHeading->getName();
    $headingContent = $elearningLessonHeading->getContent();

    $label = $mlText[16] . ' "' . $headingName . '"';
    $strAddLessonParagraph = "<a href='$gElearningUrl/lesson/paragraph/edit.php?elearningLessonId=$elearningLessonId&elearningLessonHeadingId=$elearningLessonHeadingId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$label'></a>";

    $panelUtils->addLine($headingName, '', '', $panelUtils->addCell($strAddLessonParagraph, "nbr"));

    if ($elearningLessonParagraphs = $elearningLessonParagraphUtils->selectByLessonIdAndLessonHeadingId($elearningLessonId, $elearningLessonHeadingId)) {

      foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
        $elearningLessonParagraphId = $elearningLessonParagraph->getId();
        $headline = $elearningLessonParagraph->getHeadline();
        $body = $elearningLessonParagraph->getBody();
        $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();

        $exerciseName = '';
        if ($elearningExerciseId) {
          if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
            $exerciseName = $elearningExercise->getName();
          }
        }

        $strSwap = "<a href='$PHP_SELF?elearningLessonParagraphId=$elearningLessonParagraphId&swapWithPrevious=1' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[9]'></a>"
          . " <a href='$PHP_SELF?elearningLessonParagraphId=$elearningLessonParagraphId&swapWithNext=1' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[1]'></a>";

        $strCommand = "<a href='$gElearningUrl/lesson/paragraph/edit.php?elearningLessonParagraphId=$elearningLessonParagraphId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[20]'></a>"
          . " <a href='$gElearningUrl/lesson/paragraph/compose.php?elearningLessonParagraphId=$elearningLessonParagraphId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageCompose' title='$mlText[21]'></a>"
          . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[41]'>", "$gElearningUrl/lesson/paragraph/image.php?elearningLessonParagraphId=$elearningLessonParagraphId", 600, 600)
          . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$mlText[42]'>", "$gElearningUrl/lesson/paragraph/audio.php?elearningLessonParagraphId=$elearningLessonParagraphId", 600, 600)
          . " <a href='$PHP_SELF?elearningLessonParagraphId=$elearningLessonParagraphId&deleteLessonParagraph=1' onclick='javascript:return(confirmDelete(this))' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[8]'></a>";

        $panelUtils->addLine('', $strSwap . ' ' . $headline, $exerciseName, $panelUtils->addCell($strCommand, "nbr"));

        $headingName = '';
        $headingContent = '';
        $strAddLessonParagraph = '';
      }
    } else {
      $strAdd = "<a href='$gElearningUrl/lesson/paragraph/edit.php?elearningLessonId=$elearningLessonId&elearningLessonHeadingId=$elearningLessonHeadingId' $gJSNoStatus style='color:red;'>$mlText[13]</a>";
      $panelUtils->addLine($strAdd, '', '', '');
    }
  }
} else {
  // Allow the adding of paragraphs without any lesson model heading if the lesson has no model
  $strCommand = "<a href='$gElearningUrl/lesson/paragraph/edit.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[17]'></a>";

  $panelUtils->addLine('', $panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($mlText[3], "nb"), $panelUtils->addCell($strCommand, "nbr"));
  $panelUtils->addLine();
}

if ($elearningLessonParagraphs = $elearningLessonParagraphUtils->selectByLessonIdAndNoLessonHeading($elearningLessonId)) {
  foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
    $elearningLessonParagraphId = $elearningLessonParagraph->getId();
    $headline = $elearningLessonParagraph->getHeadline();
    $body = $elearningLessonParagraph->getBody();
    $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
    $elearningLessonHeadingId = $elearningLessonParagraph->getElearningLessonHeadingId();

    $exerciseName = '';
    if ($elearningExerciseId) {
      if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
        $exerciseName = $elearningExercise->getName();
      }
    }

    $strCommand = "<a href='$gElearningUrl/lesson/paragraph/edit.php?elearningLessonParagraphId=$elearningLessonParagraphId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[20]'></a>"
      . " <a href='$gElearningUrl/lesson/paragraph/compose.php?elearningLessonParagraphId=$elearningLessonParagraphId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageCompose' title='$mlText[21]'></a>"
      . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[41]'>", "$gElearningUrl/lesson/paragraph/image.php?elearningLessonParagraphId=$elearningLessonParagraphId", 600, 600)
      . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$mlText[42]'>", "$gElearningUrl/lesson/paragraph/audio.php?elearningLessonParagraphId=$elearningLessonParagraphId", 600, 600)
      . " <a href='$PHP_SELF?elearningLessonParagraphId=$elearningLessonParagraphId&deleteLessonParagraph=1' onclick='javascript:return(confirmDelete(this))' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[8]'></a>";

    $panelUtils->addLine('', $panelUtils->addCell($headline, "v"), $panelUtils->addCell($exerciseName, "v"), $panelUtils->addCell($strCommand, "nbr"));
  }
}

if ($elearningLessonParagraphs = $elearningLessonParagraphUtils->selectWithInvalidModelHeading($elearningLessonId)) {
  foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
    $elearningLessonParagraphId = $elearningLessonParagraph->getId();
    $headline = $elearningLessonParagraph->getHeadline();
    $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();

    $exerciseName = '';
    if ($elearningExerciseId) {
      if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
        $exerciseName = $elearningExercise->getName();
      }
    }

    if ($elearningLessonHeadings) {
      $strAddMissingHeading = "<a href='$gElearningUrl/lesson/paragraph/edit.php?elearningLessonParagraphId=$elearningLessonParagraphId' $gJSNoStatus style='color:red;'>$mlText[7]</a>";
    } else {
      $strAddMissingHeading = '';
    }

    $strCommand = "<a href='$gElearningUrl/lesson/paragraph/edit.php?elearningLessonParagraphId=$elearningLessonParagraphId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[20]'></a>";

    $panelUtils->addLine($panelUtils->addCell($strAddMissingHeading, "nw"), $panelUtils->addCell($headline, "v"), $panelUtils->addCell($exerciseName, "v"), $panelUtils->addCell($strCommand, "nbr"));
  }
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("elearning_lesson_compose_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
