<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
if (!$elearningExerciseId) {
  $elearningExerciseId = LibSession::getSessionValue(ELEARNING_SESSION_EXERCISE);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_EXERCISE, $elearningExerciseId);
}

$elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");
if (!$elearningCourseId) {
  $elearningCourseId = LibSession::getSessionValue(ELEARNING_SESSION_COURSE);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_COURSE, $elearningCourseId);
}

if (!$elearningExerciseId) {
  $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
  printContent($str);
  return;
}

// The content must belong to the user
if ($elearningExerciseId && !$elearningExerciseUtils->createdByUser($elearningExerciseId, $userId)) {
  $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
  printContent($str);
  return;
}

$name = '';
$description = '';
if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $name = $elearningExercise->getName();
  $description = $elearningExercise->getDescription();
}

$strIndent = "<img border='0' src='$gCommonImagesUrl/$gImageTransparent' title=''> ";

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_comment'><a href='$gElearningUrl/teacher/corner/course/list.php' $gJSNoStatus title='$websiteText[2]'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /></a></div>";

$str .= "\n<div class='system_title'>$websiteText[1]</div>";

$str .= "\n<div><span class='system_label'>$websiteText[9]</span><span class='system_field'> $name</span></div>";

$help = $popupUtils->getTipPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_QUESTION_MARK_MEDIUM . "' class='no_style_image_icon' title='' alt='' />", $websiteText[0], 300, 400);

$str .= "\n<div style='text-align:right;'>$help</div>";

$strCommand = ''
  . " <a href='$gElearningUrl/teacher/corner/exercise/page/edit.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$websiteText[3]'></a>"
  . " <a href='$gElearningUrl/teacher/corner/exercise/edit.php?elearningExerciseId=$elearningExerciseId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$websiteText[40]'></a>"
  . " <a href='$gElearningUrl/teacher/corner/exercise/introduction.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCompose' title='$websiteText[43]'></a>"
  . " <a href='$gElearningUrl/teacher/corner/exercise/instructions.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageInstructions' title='$websiteText[8]'></a>"
  . " <a href='$gElearningUrl/teacher/corner/exercise/image.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$websiteText[41]'></a>"
  . " <a href='$gElearningUrl/teacher/corner/exercise/audio.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$websiteText[42]'></a>"
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$websiteText[34]'>", "$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId", 600, 600)
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$websiteText[26]'>", "$gElearningUrl/exercise/print_exercise.php?elearningExerciseId=$elearningExerciseId", 600, 600)
  . " <a href='$gElearningUrl/exercise/pdf.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImagePdf' title='$websiteText[26]'></a>"
  . " <a href='$gElearningUrl/teacher/corner/exercise/delete.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$websiteText[4]'></a>";

$str .= "\n<div class='no_style_list_line' style='text-align:right;'>$strCommand</div>";

$str .= "<table border='0' cellspacing='0' cellpadding='0' style='width:100%;'>";

$elearningExercisePages = $elearningExercisePageUtils->selectByExerciseId($elearningExerciseId);
foreach ($elearningExercisePages as $elearningExercisePage) {
  $elearningExercisePageId = $elearningExercisePage->getId();
  $name = $elearningExercisePage->getName();
  $description = $elearningExercisePage->getDescription();
  $questionType = $elearningExercisePage->getQuestionType();

  if ($questionType && isset($gElearningQuestionTypes[$questionType])) {
    $questionTypeName = $gElearningQuestionTypes[$questionType];
  } else {
    $questionTypeName = '';
  }

  $strName = "<span title='$description'>$name</span>";

  // Check if the object is collapsed
  $exercisePageDisplayState = LibEnv::getEnvHttpGET("exercisePageDisplayState$elearningExercisePageId");
  if (!$exercisePageDisplayState) {
    $exercisePageDisplayState = LibSession::getSessionValue(ELEARNING_SESSION_DISPLAY_EXERCISE_PAGE . $elearningExercisePageId);
  } else {
    LibSession::putSessionValue(ELEARNING_SESSION_DISPLAY_EXERCISE_PAGE . $elearningExercisePageId, $exercisePageDisplayState);
  }

  if ($exercisePageDisplayState == ELEARNING_COLLAPSED) {
    $strDisplayState = "<a href='$PHP_SELF?exercisePageDisplayState$elearningExercisePageId=" . ELEARNING_FOLDED . "' title='' style=''><img border='0' src='$gCommonImagesUrl/$gImageCollapsed' style='vertical-align:middle;' /></a>";
  } else {
    $strDisplayState = "<a href='$PHP_SELF?exercisePageDisplayState$elearningExercisePageId=" . ELEARNING_COLLAPSED . "' title=''><img border='0' src='$gCommonImagesUrl/$gImageFolded' style='vertical-align:middle;' /></a>";
  }

  $strCommand = ''
    . " <a href='$gElearningUrl/teacher/corner/exercise/question/edit.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$websiteText[15]'></a>"
    . " <a href='$gElearningUrl/teacher/corner/exercise/page/edit.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$websiteText[6]'></a>"
    . " <a href='$gElearningUrl/teacher/corner/exercise/page/content.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCompose' title='$websiteText[10]'></a>"
    . " <a href='$gElearningUrl/teacher/corner/exercise/page/instructions.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageInstructions' title='$websiteText[16]'></a>"
    . " <a href='$gElearningUrl/teacher/corner/exercise/page/image.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$websiteText[7]'></a>"
    . " <a href='$gElearningUrl/teacher/corner/exercise/page/audio.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$websiteText[27]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$websiteText[17]'>", "$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId&elearningExercisePageId=$elearningExercisePageId", 600, 600)
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$websiteText[52]'>", "$gElearningUrl/exercise_page/print.php?elearningExercisePageId=$elearningExercisePageId", 600, 600)
    . " <a href='$gElearningUrl/exercise_page/pdf.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImagePdf' title='$websiteText[52]'></a>"
    . " <a href='$gElearningUrl/teacher/corner/exercise/page/duplicate.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCopy' title='$websiteText[48]'></a>"
    . " <a href='$gElearningUrl/teacher/corner/exercise/page/delete.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$websiteText[5]'></a>";

  // Display a warning message if the number of answer locations within the text is different than the number of questions
  $strWarning = '';
  if ($elearningExercisePageUtils->typeIsWriteInText($elearningExercisePage) || $elearningExercisePageUtils->typeIsSelectInText($elearningExercisePage) || $elearningExercisePageUtils->typeIsDragAndDropInText($elearningExercisePage)) {
    $nbMarkers = $elearningExercisePageUtils->getNumberOfInTextQuestionMarkers($elearningExercisePageId);
    $nbQuestions = $elearningExercisePageUtils->getNumberOfQuestions($elearningExercisePageId);
    if ($nbMarkers < $nbQuestions) {
      $strWarning = $websiteText[45];
    } else if ($nbQuestions < $nbMarkers) {
      $strWarning = $websiteText[44];
    }
  }

  $strCell = "<span class='drag_and_drop_page' elearningExercisePageId='$elearningExercisePageId'>" . $strDisplayState . ' ' . $strName . '</span>';

  if ($strWarning) {
    $strCell .= "<span style='font-weight:bold; color:red;'>$strWarning</span>";
  }

  $str .= "\n<tr><td class='no_style_list_line' style='text-align:left;'>$strCell</td>"
    . "<td class='no_style_list_line' style='text-align:right;'>"
    . $strCommand
    . "</td></tr>";

  // If the display is in the folded state, do not display the items
  if ($exercisePageDisplayState != 1) {
    continue;
  }

  // Reset the list orders if some are mistakenly the same
  $elearningQuestionUtils->resetListOrder($elearningExercisePageId);

  $elearningQuestions = $elearningQuestionUtils->selectByExercisePage($elearningExercisePageId);
  foreach ($elearningQuestions as $elearningQuestion) {
    $question = $elearningQuestion->getQuestion();
    $points = $elearningQuestion->getPoints();
    $hint = $elearningQuestion->getHint();
    $elearningQuestionId = $elearningQuestion->getId();

    $strCommand = ''
      . " <a href='$gElearningUrl/teacher/corner/exercise/answer/edit.php?elearningExercisePageId=$elearningExercisePageId&elearningQuestionId=$elearningQuestionId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$websiteText[22]'></a>"
      . " <a href='$gElearningUrl/teacher/corner/exercise/question/edit.php?elearningQuestionId=$elearningQuestionId&elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$websiteText[20]'></a>"
      . " <a href='$gElearningUrl/teacher/corner/exercise/question/image.php?elearningQuestionId=$elearningQuestionId&elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$websiteText[7]'></a>"
      . " <a href='$gElearningUrl/teacher/corner/exercise/question/audio.php?elearningQuestionId=$elearningQuestionId&elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$websiteText[27]'></a>"
      . " <a href='$gElearningUrl/teacher/corner/exercise/question/duplicate.php?elearningQuestionId=$elearningQuestionId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageCopy' title='$websiteText[14]'></a>"
      . " <a href='$gElearningUrl/teacher/corner/exercise/question/delete.php?elearningQuestionId=$elearningQuestionId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$websiteText[21]'></a>";

    if ($elearningQuestionUtils->isListeningContent($elearningQuestionId)) {
      $questionContentType = "<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$websiteText[38] - $websiteText[13]'>";
    } else if ($elearningQuestionUtils->isWrittenAnswer($elearningQuestion)) {
      $questionContentType = "<img border='0' src='$gCommonImagesUrl/$gImageWrite' title='$websiteText[37] - $websiteText[12]'>";
    } else {
      $questionContentType = "<img border='0' src='$gCommonImagesUrl/$gImageRead' title='$websiteText[36] - $websiteText[11]'>";
    }

    // Check if the object is collapsed
    $questionDisplayState = LibEnv::getEnvHttpGET("questionDisplayState$elearningQuestionId");
    if (!$questionDisplayState) {
      $questionDisplayState = LibSession::getSessionValue(ELEARNING_SESSION_DISPLAY_QUESTION . $elearningQuestionId);
    } else {
      LibSession::putSessionValue(ELEARNING_SESSION_DISPLAY_QUESTION . $elearningQuestionId, $questionDisplayState);
    }

    if ($questionDisplayState == ELEARNING_COLLAPSED) {
      $strDisplayState = "<a href='$PHP_SELF?questionDisplayState$elearningQuestionId=" . ELEARNING_FOLDED . "' title=''><img border='0' src='$gCommonImagesUrl/$gImageCollapsed' style='vertical-align:middle;' /></a>";
    } else {
      $strDisplayState = "<a href='$PHP_SELF?questionDisplayState$elearningQuestionId=" . ELEARNING_COLLAPSED . "' title=''><img border='0' src='$gCommonImagesUrl/$gImageFolded' style='vertical-align:middle;' /></a>";
    }

    // If a question has only one possible answer then display a warning if the type is not a matching one
    $strWarning = '';
    if (!$elearningQuestionUtils->offersSeveralAnswers($elearningQuestionId)) {
      if ($elearningExercisePageUtils->typeIsSelectInQuestion($elearningExercisePage) || $elearningExercisePageUtils->typeIsRadioButtonVertical($elearningExercisePage) || $elearningExercisePageUtils->typeIsRadioButtonHorizontal($elearningExercisePage) || $elearningExercisePageUtils->typeIsSelectInText($elearningExercisePage) || $elearningExercisePageUtils->typeIsRequireOneOrMoreCorrectAnswers($elearningExercisePage) || $elearningExercisePageUtils->typeIsRequireAllPossibleAnswers($elearningExercisePage) || $elearningExercisePageUtils->typeIsDragAndDropOrderSentence($elearningExercisePage) || $elearningExercisePageUtils->typeIsDragAndDropInQuestion($elearningExercisePage)) {
        $strWarning = $websiteText[51];
      }
    }

    // If a question is a sentence to order from its answers
    // then display a warning if the merged answers are different than the question
    // or if one of the answers is not specified as being a solution
    if ($elearningExercisePageUtils->typeIsDragAndDropOrderSentence($elearningExercisePage)) {
      $mergedAnswers = '';
      $elearningAnswers = $elearningAnswerUtils->selectByQuestion($elearningQuestionId);
      foreach ($elearningAnswers as $elearningAnswer) {
        $elearningAnswerId = $elearningAnswer->getId();
        $answer = $elearningAnswer->getAnswer();
        if (!$elearningAnswerUtils->isASolution($elearningQuestion, $elearningAnswerId)) {
          $strWarning = $websiteText[50];
        }
        $mergedAnswers .= LibString::trim($answer, 0);
      }
      if (LibString::trim($question, 0) != $mergedAnswers) {
        $strWarning = $websiteText[49];
      }
    }

    $strCell = $strIndent . ' ' . "<span class='drag_and_drop_question' elearningQuestionId='$elearningQuestionId'>" . $strDisplayState . ' ' . $question . '</span>';

    if ($strWarning) {
      $strCell .= "<div style='font-weight:bold; font-size:normal; color:red;'>$strWarning</div>";
    }

    $str .= "\n<tr><td class='no_style_list_line' style='text-align:left;'>$strCell</td>"
      . "<td class='no_style_list_line' style='text-align:right;'>"
      . $strCommand
      . "</td></tr>";

    // If the display is in the folded state, do not display the items
    if ($questionDisplayState != 1) {
      continue;
    }

    // Reset the list orders if some are mistakenly the same
    $elearningAnswerUtils->resetListOrder($elearningQuestionId);

    $elearningAnswers = $elearningAnswerUtils->selectByQuestion($elearningQuestionId);
    foreach ($elearningAnswers as $elearningAnswer) {
      $elearningAnswerId = $elearningAnswer->getId();
      $answer = $elearningAnswer->getAnswer();

      if (!$elearningQuestionUtils->isWrittenAnswer($elearningQuestion)) {
        $wAnswer = $elearningAnswerId;
      } else {
        $wAnswer = $answer;
      }
      if ($elearningAnswerUtils->isASolution($elearningQuestion, $wAnswer)) {
        $answer = "<span style='color:green; font-weight:bold;'>$answer</span>";
      }

      $strCommand = " <a href='$gElearningUrl/teacher/corner/exercise/answer/edit.php?elearningAnswerId=$elearningAnswerId&elearningExercisePageId=$elearningExercisePageId&elearningQuestionId=$elearningQuestionId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$websiteText[23]'></a>";
      if ($elearningExerciseUtils->acceptMultipleAnswers()) {
        // Check if the answer is not yet specified as a possible solution
        if (!$elearningSolution = $elearningSolutionUtils->selectByQuestionAndAnswer($elearningQuestionId, $elearningAnswerId)) {
          $strCommand .= " <a href='$gElearningUrl/teacher/corner/exercise/answer/solution.php?elearningAnswerId=$elearningAnswerId' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImageFalse' title='$websiteText[25]'></a>";
        } else {
          $strCommand .= " <a href='$gElearningUrl/teacher/corner/exercise/answer/solution.php?elearningAnswerId=$elearningAnswerId&notSolution=1' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImageTrue' title='$websiteText[33]'></a>";
        }
      } else {
        if (!$elearningSolution = $elearningSolutionUtils->selectByQuestionAndAnswer($elearningQuestionId, $elearningAnswerId)) {
          $strCommand .= " <a href='$gElearningUrl/teacher/corner/exercise/answer/solution.php?elearningAnswerId=$elearningAnswerId' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImageFalse' title='$websiteText[25]'></a>";
        }
      }
      $strCommand .= " <a href='$gElearningUrl/teacher/corner/exercise/answer/image.php?elearningAnswerId=$elearningAnswerId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$websiteText[7]'></a>"
        . " <a href='$gElearningUrl/teacher/corner/exercise/answer/audio.php?elearningAnswerId=$elearningAnswerId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$websiteText[27]'></a>"
        . " <a href='$gElearningUrl/teacher/corner/exercise/answer/delete.php?elearningAnswerId=$elearningAnswerId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$websiteText[24]'></a>";

      $strAnswer = $strIndent . ' ' . $strIndent . ' ' . "<span class='drag_and_drop_answer' elearningAnswerId='$elearningAnswerId'>"
        . ' ' . $answer . '</span>';

      $str .= "\n<tr><td class='no_style_list_line' style='text-align:left;'>$strAnswer</td>"
        . "<td class='no_style_list_line' style='text-align:right;'>"
        . $strCommand
        . "</td></tr>";
    }
  }
}

$str .= "</table>";

$str .= "</div>";

$strListOrderDragAndDrop = <<<HEREDOC
<style type="text/css">
.drag_and_drop {
  cursor:pointer;
}
.droppable-hover {
  outline:2px solid #ABABAB;
}
#droppableTooltip {
  position:absolute;
  z-index:9999;
  background-color:#fff;
  font-weight:normal;
  border:1px solid #ABABAB;
  padding:4px;
}
</style>

<script type="text/javascript">

$(document).ready(function() {

  $(".drag_and_drop").draggable({
    helper: 'clone', // Drag a copy of the element
    ghosting: true, // Display the element in semi transparent fashion when dragging
    opacity: 0.5, // The transparency level of the dragged element
    cursorAt: { top: 10, left: 10 }, // Position the mouse cursor in the dragged element when starting to drag
    cursor: 'move', // Change the cursor shape when dragging
    revert: 'invalid', // Put back the dragged element if it could not be dropped
    containment: '.list_lines' // Limit the area of dragging
  });

  $(".drag_and_drop").droppable({
    accept: '.drag_and_drop', // Specify what kind of element can be dropped
    hoverClass: 'droppable-hover', // Styling a droppable when hovering on it
    tolerance: 'pointer', // Assume a droppable fit when the mouse cursor hovers
    over: function(ev, ui) {
      $(this).append('<div id="droppableTooltip">$websiteText[19]</div>');
      $('#droppableTooltip').css('top', ev.pageY);
      $('#droppableTooltip').css('left', ev.pageX + 20);
      $('#droppableTooltip').fadeIn('500');
    },
    out: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
    },
    drop: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
      moveAfter(ui.draggable.attr("elearningCourseItemId"), $(this).attr("elearningCourseItemId"));
    }
  });

  function moveAfter(elearningCourseItemId, targetId) {
    var url = '$gElearningUrl/course/move_after.php?elearningCourseItemId='+elearningCourseItemId+'&targetId='+targetId;
    ajaxAsynchronousRequest(url, renderPage);
  }

  function renderPage(responseText) {
    var response = eval('(' + responseText + ')');
    var moved = response.moved;
    if (moved) {
      window.location = window.location.href;
    }
  }

});

</script>
HEREDOC;
$str .= $strListOrderDragAndDrop;

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
