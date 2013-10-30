<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

if (!$elearningTeacher = $elearningTeacherUtils->selectByUserId($userId)) {
  $str = LibHtml::urlRedirect("$gElearningUrl/teacher/register.php");
  printContent($str);
  return;
}

$strIndent = "<img border='0' src='$gCommonImagesUrl/$gImageTransparent' title=''> ";

$str = '';

$str .= "\n<div class='system'>";

// Suggest a social network notification
$newlyRegistered = LibEnv::getEnvHttpGET("newlyRegistered");
if ($newlyRegistered) {
  $str .= $elearningTeacherUtils->publishSocialNotification();
}

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$help = $popupUtils->getTipPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_QUESTION_MARK_MEDIUM . "' class='no_style_image_icon' title='' alt='' />", $websiteText[24], 300, 400);

$str .= "\n<div style='text-align:right;'><a href='$gElearningUrl/teacher/corner/course/edit.php' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$websiteText[2]'></a> $help</div>";

$str .= "<table border='0' cellspacing='0' cellpadding='0' style='width:100%;'>";

if ($elearningCourses = $elearningCourseUtils->selectByUserId($userId)) {
  foreach ($elearningCourses as $elearningCourse) {
    $elearningCourseId = $elearningCourse->getId();
    $name = $elearningCourse->getName();
    $description = $elearningCourse->getDescription();
    $matterId = $elearningCourse->getMatterId();

    $str .= "<tr>";

    $str .= "\n<td class='no_style_list_line'><img src='$gImagesUserUrl/" . IMAGE_ELEARNING_COURSE . "' title='$websiteText[20]' style='vertical-align:middle;' /> <span title='$description'>$name</span></td>"
      . "<td class='no_style_list_line' style='text-align:right;'>"
      . "<a href='$gElearningUrl/teacher/corner/lesson/edit.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$websiteText[1]'></a>"
      . " <a href='$gElearningUrl/teacher/corner/exercise/edit.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$websiteText[8]'></a>"
      . " <a href='$gElearningUrl/teacher/corner/course/edit.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$websiteText[7]'></a>"
      . "</td>";

    $str .= "</tr>";

    $elearningCourseItems = $elearningCourseItemUtils->selectByCourseId($elearningCourseId);
    foreach ($elearningCourseItems as $elearningCourseItem) {
      $elearningCourseItemId = $elearningCourseItem->getId();
      $elearningExerciseId = $elearningCourseItem->getElearningExerciseId();
      $elearningLessonId = $elearningCourseItem->getElearningLessonId();
      if ($elearningExerciseId) {
        if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
          $name = $elearningExercise->getName();
          $description = $elearningExercise->getDescription();

          $partOfOnlyOneCourse = $elearningExerciseUtils->partOfOnlyOneCourse($elearningExerciseId);

          $strCommand = "<a href='$gElearningUrl/teacher/corner/exercise/edit.php?elearningExerciseId=$elearningExerciseId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$websiteText[3]'></a>"
            . " <a href='$gElearningUrl/teacher/corner/exercise/compose.php?elearningExerciseId=$elearningExerciseId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImageDesign' title='$websiteText[4]'></a>"
            . " <a href='$gElearningUrl/teacher/corner/course/add_exercise.php?elearningExerciseId=$elearningExerciseId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImageAddTo' title='$websiteText[18]'></a>";
          if (!$partOfOnlyOneCourse) {
            $strCommand .= " <a href='$gElearningUrl/teacher/corner/course/remove_exercise.php?elearningExerciseId=$elearningExerciseId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
              . "<img border='0' src='$gCommonImagesUrl/$gImageRemoveFrom' title='$websiteText[9]'></a>";
          }
          $strCommand .= ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$websiteText[12]'>", "$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId", 600, 600)
            . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$websiteText[14]'>", "$gElearningUrl/exercise/print_exercise.php?elearningExerciseId=$elearningExerciseId", 600, 600)
            . " <a href='$gElearningUrl/exercise/pdf.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImagePdf' title='$websiteText[14]'></a>"
            . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$websiteText[16]'>", "$gElearningUrl/exercise/send.php?elearningExerciseId=$elearningExerciseId", 600, 600);
          if ($partOfOnlyOneCourse) {
            $strCommand .= " <a href='$gElearningUrl/teacher/corner/exercise/delete.php?elearningExerciseId=$elearningExerciseId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
              . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$websiteText[10]'></a>";
          }

          $str .= "\n<tr><td class='no_style_list_line'>$strIndent <span class='no_style_drag_and_drop' elearningCourseItemId='$elearningCourseItemId'><img src='$gImagesUserUrl/" . IMAGE_ELEARNING_EXERCISE . "' title='$websiteText[22]' style='vertical-align:middle;' /> <span title='$description'>$name</span></span></td>"
            . "<td class='no_style_list_line' style='text-align:right;'>"
            . $strCommand
            . "</td></tr>";
        }
      } else if ($elearningLessonId) {
        if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
          $name = $elearningLesson->getName();
          $description = $elearningLesson->getDescription();

          $partOfOnlyOneCourse = $elearningLessonUtils->partOfOnlyOneCourse($elearningLessonId);

          $strCommand = "<a href='$gElearningUrl/teacher/corner/lesson/edit.php?elearningLessonId=$elearningLessonId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$websiteText[5]'></a>"
            . " <a href='$gElearningUrl/teacher/corner/lesson/compose.php?elearningLessonId=$elearningLessonId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImageDesign' title='$websiteText[6]'></a>"
            . " <a href='$gElearningUrl/teacher/corner/course/add_lesson.php?elearningLessonId=$elearningLessonId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImageAddTo' title='$websiteText[17]'></a>";
          if (!$partOfOnlyOneCourse) {
            $strCommand .= " <a href='$gElearningUrl/teacher/corner/course/remove_lesson.php?elearningLessonId=$elearningLessonId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
              . "<img border='0' src='$gCommonImagesUrl/$gImageRemoveFrom' title='$websiteText[9]'></a>";
          }
          $strCommand .= ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$websiteText[13]'>", "$gElearningUrl/lesson/preview.php?elearningLessonId=$elearningLessonId", 600, 600)
            . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$websiteText[15]'>", "$gElearningUrl/lesson/print_lesson.php?elearningLessonId=$elearningLessonId", 600, 600)
            . " <a href='$gElearningUrl/lesson/pdf.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
            . "<img border='0' src='$gCommonImagesUrl/$gImagePdf' title='$websiteText[15]'></a>"
            . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$websiteText[16]'>", "$gElearningUrl/lesson/send.php?elearningLessonId=$elearningLessonId", 600, 600);
          if ($partOfOnlyOneCourse) {
            $strCommand .= " <a href='$gElearningUrl/teacher/corner/lesson/delete.php?elearningLessonId=$elearningLessonId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
              . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$websiteText[11]'></a>";
          }

          $str .= "\n<tr><td class='no_style_list_line'>$strIndent <span class='no_style_drag_and_drop' elearningCourseItemId='$elearningCourseItemId'><img src='$gImagesUserUrl/" . IMAGE_ELEARNING_LESSON . "' title='$websiteText[21]' style='vertical-align:middle;' /> <span title='$description'>$name</span></span></td>"
            . "<td class='no_style_list_line' style='text-align:right;'>"
            . $strCommand
            . "</td></tr>";
          if ($elearningLessonParagraphs = $elearningLessonParagraphUtils->selectByLessonId($elearningLessonId)) {
            foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
              $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
              if ($elearningExerciseId) {
                if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
                  $name = $elearningExercise->getName();
                  $strCommand = "<a href='$gElearningUrl/teacher/corner/exercise/edit.php?elearningExerciseId=$elearningExerciseId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
                    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$websiteText[3]'></a>"
                    . " <a href='$gElearningUrl/teacher/corner/exercise/compose.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
                    . "<img border='0' src='$gCommonImagesUrl/$gImageDesign' title='$websiteText[4]'></a>"
                    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$websiteText[12]'>", "$gElearningUrl/exercise/preview.php?elearningExerciseId=$elearningExerciseId", 600, 600)
                    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$websiteText[14]'>", "$gElearningUrl/exercise/print_exercise.php?elearningExerciseId=$elearningExerciseId", 600, 600)
                    . " <a href='$gElearningUrl/exercise/pdf.php?elearningExerciseId=$elearningExerciseId' $gJSNoStatus>"
                    . "<img border='0' src='$gCommonImagesUrl/$gImagePdf' title='$websiteText[14]'></a>"
                    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$websiteText[16]'>", "$gElearningUrl/exercise/send.php?elearningExerciseId=$elearningExerciseId", 600, 600);

                  $str .= "\n<tr><td class='no_style_list_line'>$strIndent $strIndent<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_EXERCISE . "' title='$websiteText[23]' style='vertical-align:middle;' /> <span title='$description'>$name</span></td>"
                    . "<td class='no_style_list_line' style='text-align:right;'>"
                    . $strCommand
                    . "</td></tr>";
                }
              }
            }
          }
        }
      }
    }
  }
} else {
  $str .= "\n<div class='system_comment'>$websiteText[25]</div>"
    . "<div class='system_comment'>$websiteText[26] <a href='$gElearningUrl/teacher/corner/course/edit.php' $gJSNoStatus>$websiteText[27]</a>"
    . "</div>";
}

$str .= "</table>";

$str .= "\n</div>";

$strListOrderDragAndDrop = <<<HEREDOC
<style type="text/css">
.no_style_drag_and_drop {
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

  $(".no_style_drag_and_drop").draggable({
    helper: 'clone', // Drag a copy of the element
    ghosting: true, // Display the element in semi transparent fashion when dragging
    opacity: 0.5, // The transparency level of the dragged element
    cursorAt: { top: 10, left: 10 }, // Position the mouse cursor in the dragged element when starting to drag
    cursor: 'move', // Change the cursor shape when dragging
    revert: 'invalid', // Put back the dragged element if it could not be dropped
    containment: '.list_lines' // Limit the area of dragging
  });

  $(".no_style_drag_and_drop").droppable({
    accept: '.no_style_drag_and_drop', // Specify what kind of element can be dropped
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
