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

$elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");
if (!$elearningCourseId) {
    $elearningCourseId = LibSession::getSessionValue(ELEARNING_SESSION_COURSE);
} else {
    LibSession::putSessionValue(ELEARNING_SESSION_COURSE, $elearningCourseId);
}

// The course must belong to the user
if ($elearningCourseId && !$elearningCourseUtils->createdByUser($elearningCourseId, $userId)) {
  $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
  printContent($str);
  return;
}

$strIndent = "<img border='0' src='$gCommonImagesUrl/$gImageTransparent' title=''> ";

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_comment'><a href='$gElearningUrl/teacher/corner/course/list.php' $gJSNoStatus title='$websiteText[3]'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /></a></div>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$help = $popupUtils->getTipPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_QUESTION_MARK_MEDIUM . "' class='no_style_image_icon' title='' alt='' />", $websiteText[24], 300, 400);

$str .= "\n<div style='text-align:right;'><a href='$gElearningUrl/teacher/corner/course/info/edit.php?elearningCourseId=$elearningCourseId' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$websiteText[2]'></a> $help</div>";

$str .= "<table border='0' cellspacing='0' cellpadding='0' style='width:100%;'>";

if ($elearningCourseInfos = $elearningCourseInfoUtils->selectByCourseId($elearningCourseId)) {
  foreach ($elearningCourseInfos as $elearningCourseInfo) {
    $elearningCourseInfoId = $elearningCourseInfo->getId();
    $headline = $elearningCourseInfo->getHeadline();
    $information = $elearningCourseInfo->getInformation();

    $str .= "<tr>";

    $str .= "\n<td class='no_style_list_line no_style_drag_and_drop' elearningCourseInfoId='$elearningCourseInfoId'><img src='$gImagesUserUrl/" . IMAGE_ELEARNING_COURSE . "' title='$websiteText[20]' style='vertical-align:middle;' /> <span title='$information'>$headline</span></td>"
      . "<td class='no_style_list_line' style='text-align:right;'>"
      . "<a href='$gElearningUrl/teacher/corner/course/info/edit.php?elearningCourseInfoId=$elearningCourseInfoId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$websiteText[1]'></a>"
      . " <a href='$gElearningUrl/teacher/corner/course/info/delete.php?elearningCourseInfoId=$elearningCourseInfoId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$websiteText[10]'></a>"
      . "</td>";

    $str .= "</tr>";
  }
} else {
  $str .= "\n<div class='system_comment'>$websiteText[25]</div>"
    . "<div class='system_comment'>$websiteText[26] <a href='$gElearningUrl/teacher/corner/course/info/edit.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>$websiteText[27]</a>"
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
      moveAfter(ui.draggable.attr("elearningCourseInfoId"), $(this).attr("elearningCourseInfoId"));
    }
  });

  function moveAfter(elearningCourseInfoId, targetId) {
    var url = '$gElearningUrl/course/info/move_after.php?elearningCourseInfoId='+elearningCourseInfoId+'&targetId='+targetId;
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
