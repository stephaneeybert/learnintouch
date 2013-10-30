<?PHP

require_once("website.php");

// The administrator may access this page without being logged in if a unique token is used
// This allows an administrator to access this page by clicking on a link in an email
$tokenName = LibEnv::getEnvHttpGET("tokenName");
$tokenValue = LibEnv::getEnvHttpGET("tokenValue");
if ($uniqueTokenUtils->isValid($tokenName, $tokenValue)) {
  // In case the website email is also the one of a registered admin then log in the admin
  $siteEmail = LibEnv::getEnvHttpGET("siteEmail");
  if ($admin = $adminUtils->selectByEmail($siteEmail)) {
    $login = $admin->getLogin();
    $adminUtils->logIn($login);
  }
} else {
  // If no token is used, then
  // check that the administrator is allowed to use the module
  $adminModuleUtils->checkAdminModule(MODULE_ELEARNING);
}

$mlText = $languageUtils->getMlText(__FILE__);

$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
if (!$elearningExerciseId) {
  $elearningExerciseId = LibSession::getSessionValue(ELEARNING_SESSION_EXERCISE);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_EXERCISE, $elearningExerciseId);
}

$name = '';
$description = '';
if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $name = $elearningExercise->getName();
  $description = $elearningExercise->getDescription();
}

$strIndent = "<img border='0' src='$gCommonImagesUrl/$gImageTransparent' title=''> ";

$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/admin.php");
$help = $popupUtils->getHelpPopup($mlText[29], 300, 300);
$panelUtils->setHelp($help);

$strName = $name;
if ($description) {
  $strName .= ' - ' . $description;
}
$panelUtils->addLine("<b>$mlText[4]</b> $strName", '', '', '', '');

$strCommand = ''
  . " <a href='$gElearningUrl/exercise_page/edit.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[3]'></a>"
  . " <a href='$gElearningUrl/exercise/edit.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[40]'></a>"
  . " <a href='$gElearningUrl/exercise/edit_introduction.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCompose' title='$mlText[43]'></a>"
  . " <a href='$gElearningUrl/exercise/instructions.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageInstructions' title='$mlText[8]'></a>"
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[41]'>", "$gElearningUrl/exercise/image.php?elearningExerciseId=$elearningExerciseId", 600, 600)
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$mlText[42]'>", "$gElearningUrl/exercise/audio.php?elearningExerciseId=$elearningExerciseId", 600, 600)
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[34]'>", "$gElearningUrl/exercise/preview.php?elearningExerciseId=$elearningExerciseId", 600, 600)
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$mlText[26]'>", "$gElearningUrl/exercise/print_exercise.php?elearningExerciseId=$elearningExerciseId", 600, 600)
  . " <a href='$gElearningUrl/exercise/pdf.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImagePdf' title='$mlText[26]'></a>";

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell("$mlText[2]", "bz60"), $panelUtils->addCell("$mlText[35]", "nbc"), $panelUtils->addCell("$mlText[39]", "nbc"), $panelUtils->addCell($mlText[32], "nbc"), $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

// Reset the list orders if some are mistakenly the same
$elearningExercisePageUtils->resetListOrder($elearningExerciseId);

$panelUtils->openList();
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

  $strName = $name;
  if ($description) {
    $strName .= ' - ' . $description;
  }

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
    . " <a href='$gElearningUrl/question/edit.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[15]'></a>"
    . " <a href='$gElearningUrl/exercise_page/edit.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[6]'></a>"
    . " <a href='$gElearningUrl/exercise_page/edit_content.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCompose' title='$mlText[10]'></a>"
    . " <a href='$gElearningUrl/exercise_page/instructions.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageInstructions' title='$mlText[16]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[7]'>", "$gElearningUrl/exercise_page/image.php?elearningExercisePageId=$elearningExercisePageId", 600, 600)
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$mlText[27]'>", "$gElearningUrl/exercise_page/audio.php?elearningExercisePageId=$elearningExercisePageId", 600, 600)
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[17]'>", "$gElearningUrl/exercise/preview.php?elearningExerciseId=$elearningExerciseId&elearningExercisePageId=$elearningExercisePageId", 600, 600)
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$mlText[52]'>", "$gElearningUrl/exercise_page/print.php?elearningExercisePageId=$elearningExercisePageId", 600, 600)
    . " <a href='$gElearningUrl/exercise_page/pdf.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImagePdf' title='$mlText[52]'></a>";

  if ($elearningQuestionUtils->couldBeReordered($elearningExercisePageId)) {
    $strCommand .= ''
      . " <a href='$gElearningUrl/question/reset_order.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageOrder' title='$mlText[46]'></a>"
      . " <a href='$gElearningUrl/question/reset_order.php?elearningExercisePageId=$elearningExercisePageId&chronological=1' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageOrderAlpha' title='$mlText[47]'></a>";
  }
  $strCommand .= " <a href='$gElearningUrl/exercise_page/duplicate.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCopy' title='$mlText[48]'></a>"
    . " <a href='$gElearningUrl/exercise_page/delete.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[5]'></a>";

  // Display a warning message if the number of answer locations within the text is different than the number of questions
  $strWarning = '';
  if ($elearningExercisePageUtils->typeIsWriteInText($elearningExercisePage) || $elearningExercisePageUtils->typeIsSelectInText($elearningExercisePage) || $elearningExercisePageUtils->typeIsDragAndDropInText($elearningExercisePage)) {
    $nbMarkers = $elearningExercisePageUtils->getNumberOfInTextQuestionMarkers($elearningExercisePageId);
    $nbQuestions = $elearningExercisePageUtils->getNumberOfQuestions($elearningExercisePageId);
    if ($nbMarkers < $nbQuestions) {
      $strWarning = $mlText[45] . ' (' . $nbMarkers . ') ' . $mlText[61] . ' (' . $nbQuestions . ')';
    } else if ($nbQuestions < $nbMarkers) {
      $strWarning = $mlText[44];
    }
  }

  $strSwap = "<a href='$gElearningUrl/exercise_page/swapup.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[9]' style='vertical-align:middle;' /></a></span>"
    . " <a href='$gElearningUrl/exercise_page/swapdown.php?elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[1]' style='vertical-align:middle;' /></a></span>";

  $strCell = $strSwap . " <span class='drag_and_drop_page' elearningExercisePageId='$elearningExercisePageId'>" . $strDisplayState . ' ' . $strName . '</span>' . "<div style='color:grey;'>" . $questionTypeName . '</div>';

  if ($strWarning) {
    $strCell .= "<div style='font-weight:bold; color:red;'>$strWarning</div>";
  }

  $panelUtils->addLine($strCell, '', '', '', $panelUtils->addCell($strCommand, "nr"));

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
      . " <a href='$gElearningUrl/answer/edit.php?elearningExercisePageId=$elearningExercisePageId&elearningQuestionId=$elearningQuestionId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[22]'></a>"
      . " <a href='$gElearningUrl/question/edit.php?elearningQuestionId=$elearningQuestionId&elearningExercisePageId=$elearningExercisePageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[20]'></a>"
      . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[7]'>", "$gElearningUrl/question/image.php?elearningQuestionId=$elearningQuestionId", 600, 600)
      . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$mlText[27]'>", "$gElearningUrl/question/audio.php?elearningQuestionId=$elearningQuestionId", 600, 600)
      . " <a href='$gElearningUrl/question/duplicate.php?elearningQuestionId=$elearningQuestionId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageCopy' title='$mlText[14]'></a>"
      . " <a href='$gElearningUrl/question/delete.php?elearningQuestionId=$elearningQuestionId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[21]'></a>";

    if ($elearningQuestionUtils->isListeningContent($elearningQuestionId)) {
      $questionContentType = "<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$mlText[38] - $mlText[13]'>";
    } else if ($elearningQuestionUtils->isWrittenAnswer($elearningQuestion)) {
      $questionContentType = "<img border='0' src='$gCommonImagesUrl/$gImageWrite' title='$mlText[37] - $mlText[12]'>";
    } else {
      $questionContentType = "<img border='0' src='$gCommonImagesUrl/$gImageRead' title='$mlText[36] - $mlText[11]'>";
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
        $strWarning = $mlText[51];
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
          $strWarning = $mlText[50];
        }
        $mergedAnswers .= LibString::trim($answer, 0);
      }
      if (LibString::trim($question, 0) != $mergedAnswers) {
        $strWarning = $mlText[49];
      }
    }

    $strSwap = "<a href='$gElearningUrl/question/swapup.php?elearningQuestionId=$elearningQuestionId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[31]' style='vertical-align:middle;' /></a></span>"
      . " <a href='$gElearningUrl/question/swapdown.php?elearningQuestionId=$elearningQuestionId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[30]' style='vertical-align:middle;' /></a></span>";

    $strCell = $strIndent . ' ' . $strSwap . " <span class='drag_and_drop_question' elearningQuestionId='$elearningQuestionId'>" . $strDisplayState . ' ' . $question . '</span>';

    if ($strWarning) {
      $strCell .= "<div style='font-weight:bold; font-size:normal; color:red;'>$strWarning</div>";
    }

    $panelUtils->addLine($strCell, $panelUtils->addCell($hint, "c"), $panelUtils->addCell($questionContentType, "c"), $panelUtils->addCell($points, "c"), $panelUtils->addCell($strCommand, "nr"));

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

      $strCommand = " <a href='$gElearningUrl/answer/edit.php?elearningAnswerId=$elearningAnswerId&elearningExercisePageId=$elearningExercisePageId&elearningQuestionId=$elearningQuestionId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[23]'></a>";
      if ($elearningExerciseUtils->acceptMultipleAnswers()) {
        // Check if the answer is not yet specified as a possible solution
        if (!$elearningSolution = $elearningSolutionUtils->selectByQuestionAndAnswer($elearningQuestionId, $elearningAnswerId)) {
          $strCommand .= " <a href='$gElearningUrl/answer/solution.php?elearningAnswerId=$elearningAnswerId' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImageTrue' title='$mlText[25]'></a>";
        } else {
          $strCommand .= " <a href='$gElearningUrl/answer/solution.php?elearningAnswerId=$elearningAnswerId&notSolution=1' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImageFalse' title='$mlText[33]'></a>";
        }
      } else {
        if (!$elearningSolution = $elearningSolutionUtils->selectByQuestionAndAnswer($elearningQuestionId, $elearningAnswerId)) {
          $strCommand .= " <a href='$gElearningUrl/answer/solution.php?elearningAnswerId=$elearningAnswerId' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImageTrue' title='$mlText[25]'></a>";
        }
      }
      $strCommand .= ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[7]'>", "$gElearningUrl/answer/image.php?elearningAnswerId=$elearningAnswerId", 600, 600)
        . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$mlText[27]'>", "$gElearningUrl/answer/audio.php?elearningAnswerId=$elearningAnswerId", 600, 600)
        . " <a href='$gElearningUrl/answer/delete.php?elearningAnswerId=$elearningAnswerId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[24]'></a>";

      $strSwap = "<a href='$gElearningUrl/answer/swapup.php?elearningAnswerId=$elearningAnswerId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[19]' style='vertical-align:middle;' /></a></span>"
        . " <a href='$gElearningUrl/answer/swapdown.php?elearningAnswerId=$elearningAnswerId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[18]' style='vertical-align:middle;' /></a></span>";

      $strAnswer = $strIndent . ' ' . $strIndent . ' ' . $strSwap . " <span class='drag_and_drop_answer' elearningAnswerId='$elearningAnswerId'>"
        . ' ' . $answer . '</span>';

      $panelUtils->addLine($panelUtils->addCell($strAnswer, "n"), '', '', '', $panelUtils->addCell($strCommand, "nr"));
    }
  }
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("elearning_exercise_compose_vscroll");
$panelUtils->addContent($strRememberScroll);

$strListOrderDragAndDrop = <<<HEREDOC
<style type="text/css">
.drag_and_drop_page {
  cursor:pointer;
}
.drag_and_drop_question {
  cursor:pointer;
}
.drag_and_drop_answer {
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

  $(".drag_and_drop_page").draggable({
    helper: 'clone', // Drag a copy of the element
    ghosting: true, // Display the element in semi transparent fashion when dragging
    opacity: 0.5, // The transparency level of the dragged element
    cursorAt: { top: 10, left: 10 }, // Position the mouse cursor in the dragged element when starting to drag
    cursor: 'move', // Change the cursor shape when dragging
    revert: 'invalid', // Put back the dragged element if it could not be dropped
    containment: '.list_lines' // Limit the area of dragging
  });

  $(".drag_and_drop_question").draggable({
    helper: 'clone', // Drag a copy of the element
    ghosting: true, // Display the element in semi transparent fashion when dragging
    opacity: 0.5, // The transparency level of the dragged element
    cursorAt: { top: 10, left: 10 }, // Position the mouse cursor in the dragged element when starting to drag
    cursor: 'move', // Change the cursor shape when dragging
    revert: 'invalid', // Put back the dragged element if it could not be dropped
    containment: '.list_lines' // Limit the area of dragging
  });

  $(".drag_and_drop_answer").draggable({
    helper: 'clone', // Drag a copy of the element
    ghosting: true, // Display the element in semi transparent fashion when dragging
    opacity: 0.5, // The transparency level of the dragged element
    cursorAt: { top: 10, left: 10 }, // Position the mouse cursor in the dragged element when starting to drag
    cursor: 'move', // Change the cursor shape when dragging
    revert: 'invalid', // Put back the dragged element if it could not be dropped
    containment: '.list_lines' // Limit the area of dragging
  });

  $(".drag_and_drop_page").droppable({
    accept: '.drag_and_drop_page, .drag_and_drop_question', // Specify what kind of element can be dropped
    hoverClass: 'droppable-hover', // Styling a droppable when hovering on it
    tolerance: 'pointer', // Assume a droppable fit when the mouse cursor hovers
    over: function(ev, ui) {
      if (ui.draggable.attr("elearningExercisePageId")) {
        $(this).append('<div id="droppableTooltip">$mlText[59]</div>');
      } else if (ui.draggable.attr("elearningQuestionId")) {
        $(this).append('<div id="droppableTooltip">$mlText[56]</div>');
      }
      $('#droppableTooltip').css('top', ev.pageY);
      $('#droppableTooltip').css('left', ev.pageX + 20);
      $('#droppableTooltip').fadeIn('500');
    },
    out: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
    },
    drop: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
      if (ui.draggable.attr("elearningExercisePageId")) {
        movePageAfter(ui.draggable.attr("elearningExercisePageId"), $(this).attr("elearningExercisePageId"));
      } else if (ui.draggable.attr("elearningQuestionId")) {
        moveQuestionInto(ui.draggable.attr("elearningQuestionId"), $(this).attr("elearningExercisePageId"));
      }
    }
  });

  $(".drag_and_drop_question").droppable({
    accept: '.drag_and_drop_question, .drag_and_drop_answer', // Specify what kind of element can be dropped
    hoverClass: 'droppable-hover', // Styling a droppable when hovering on it
    tolerance: 'pointer', // Assume a droppable fit when the mouse cursor hovers
    over: function(ev, ui) {
      if (ui.draggable.attr("elearningQuestionId")) {
        $(this).append('<div id="droppableTooltip">$mlText[57]</div>');
      } else if (ui.draggable.attr("elearningAnswerId")) {
        $(this).append('<div id="droppableTooltip">$mlText[53]</div>');
      }
      $('#droppableTooltip').css('top', ev.pageY);
      $('#droppableTooltip').css('left', ev.pageX + 20);
      $('#droppableTooltip').fadeIn('500');
    },
    out: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
    },
    drop: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
      if (ui.draggable.attr("elearningQuestionId")) {
        moveQuestionAfter(ui.draggable.attr("elearningQuestionId"), $(this).attr("elearningQuestionId"));
      } else if (ui.draggable.attr("elearningAnswerId")) {
        moveAnswerInto(ui.draggable.attr("elearningAnswerId"), $(this).attr("elearningQuestionId"));
      }
    }
  });

  $(".drag_and_drop_answer").droppable({
    accept: '.drag_and_drop_answer', // Specify what kind of element can be dropped
    hoverClass: 'droppable-hover', // Styling a droppable when hovering on it
    tolerance: 'pointer', // Assume a droppable fit when the mouse cursor hovers
    over: function(ev, ui) {
      $(this).append('<div id="droppableTooltip">$mlText[54]</div>');
      $('#droppableTooltip').css('top', ev.pageY);
      $('#droppableTooltip').css('left', ev.pageX + 20);
      $('#droppableTooltip').fadeIn('500');
    },
    out: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
    },
    drop: function(ev, ui) {
      $(this).children('div#droppableTooltip').remove();
      moveAnswerAfter(ui.draggable.attr("elearningAnswerId"), $(this).attr("elearningAnswerId"));
    }
  });

  function movePageAfter(elearningExercisePageId, targetId) {
    var url = '$gElearningUrl/exercise_page/move_after.php?elearningExercisePageId='+elearningExercisePageId+'&targetId='+targetId;
    ajaxAsynchronousRequest(url, renderPage);
  }

  function moveQuestionInto(elearningQuestionId, elearningExercisePageId) {
    var url = '$gElearningUrl/question/move_into.php?elearningQuestionId='+elearningQuestionId+'&elearningExercisePageId='+elearningExercisePageId;
    ajaxAsynchronousRequest(url, renderPage);
  }

  function moveQuestionAfter(elearningQuestionId, targetId) {
    var url = '$gElearningUrl/question/move_after.php?elearningQuestionId='+elearningQuestionId+'&targetId='+targetId;
    ajaxAsynchronousRequest(url, renderPage);
  }

  function moveAnswerInto(elearningAnswerId, elearningQuestionId) {
    var url = '$gElearningUrl/answer/move_into.php?elearningAnswerId='+elearningAnswerId+'&elearningQuestionId='+elearningQuestionId;
    ajaxAsynchronousRequest(url, renderPage);
  }

  function moveAnswerAfter(elearningAnswerId, targetId) {
    var url = '$gElearningUrl/answer/move_after.php?elearningAnswerId='+elearningAnswerId+'&targetId='+targetId;
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
$panelUtils->addContent($strListOrderDragAndDrop);

$str = $panelUtils->render();

printAdminPage($str);

?>
