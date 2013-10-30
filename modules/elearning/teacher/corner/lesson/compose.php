<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningLessonUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

$elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");
if (!$elearningLessonId) {
  $elearningLessonId = LibSession::getSessionValue(ELEARNING_SESSION_LESSON);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_LESSON, $elearningLessonId);
}

$elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");
if (!$elearningCourseId) {
  $elearningCourseId = LibSession::getSessionValue(ELEARNING_SESSION_COURSE);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_COURSE, $elearningCourseId);
}

if (!$elearningLessonId) {
  $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
  printContent($str);
  return;
}

// The content must belong to the user
if ($elearningLessonId && !$elearningLessonUtils->createdByUser($elearningLessonId, $userId)) {
  $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/course/list.php");
  printContent($str);
  return;
}

$name = '';
$description = '';
if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
  $name = $elearningLesson->getName();
  $description = $elearningLesson->getDescription();
}

$strIndent = "<img border='0' src='$gCommonImagesUrl/$gImageTransparent' title=''> ";

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_comment'><a href='$gElearningUrl/teacher/corner/course/list.php' $gJSNoStatus title='$websiteText[2]'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /></a></div>";

$str .= "\n<div class='system_title'>$websiteText[9]</div>";

$str .= "\n<div><span class='system_label'>$websiteText[10]</span><span class='system_field'> $name</span></div>";

$help = $popupUtils->getTipPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_QUESTION_MARK_MEDIUM . "' class='no_style_image_icon' title='' alt='' />", $websiteText[0], 300, 400);

$str .= "\n<div style='text-align:right;'>$help</div>";

$strCommand = ''
  . " <a href='$gElearningUrl/teacher/corner/lesson/paragraph/edit.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$websiteText[3]'></a>"
  . " <a href='$gElearningUrl/teacher/corner/lesson/edit.php?elearningLessonId=$elearningLessonId&elearningCourseId=$elearningCourseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$websiteText[40]'></a>"
  . " <a href='$gElearningUrl/teacher/corner/lesson/introduction.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCompose' title='$websiteText[43]'></a>"
  . " <a href='$gElearningUrl/teacher/corner/lesson/instructions.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageInstructions' title='$websiteText[8]'></a>"
  . " <a href='$gElearningUrl/teacher/corner/lesson/image.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$websiteText[41]'></a>"
  . " <a href='$gElearningUrl/teacher/corner/lesson/audio.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$websiteText[42]'></a>"
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$websiteText[34]'>", "$gElearningUrl/lesson/display_lesson.php?elearningLessonId=$elearningLessonId", 600, 600)
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$websiteText[26]'>", "$gElearningUrl/lesson/print_lesson.php?elearningLessonId=$elearningLessonId", 600, 600)
  . " <a href='$gElearningUrl/lesson/pdf.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImagePdf' title='$websiteText[26]'></a>"
  . " <a href='$gElearningUrl/teacher/corner/lesson/delete.php?elearningLessonId=$elearningLessonId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$websiteText[4]'></a>";

$str .= "\n<div class='no_style_list_line' style='text-align:right;'>$strCommand</div>";

$str .= "<table border='0' cellspacing='0' cellpadding='0' style='width:100%;'>";

$elearningLessonParagraphs = $elearningLessonParagraphUtils->selectByLessonId($elearningLessonId);
foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
  $elearningLessonParagraphId = $elearningLessonParagraph->getId();
  $headline = $elearningLessonParagraph->getHeadline();

  $strName = "$headline";

  $strCommand = ''
    . " <a href='$gElearningUrl/teacher/corner/lesson/paragraph/edit.php?elearningLessonParagraphId=$elearningLessonParagraphId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$websiteText[6]'></a>"
    . " <a href='$gElearningUrl/teacher/corner/lesson/paragraph/content.php?elearningLessonParagraphId=$elearningLessonParagraphId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCompose' title='$websiteText[1]'></a>"
    . " <a href='$gElearningUrl/teacher/corner/lesson/paragraph/image.php?elearningLessonParagraphId=$elearningLessonParagraphId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$websiteText[7]'></a>"
    . " <a href='$gElearningUrl/teacher/corner/lesson/paragraph/audio.php?elearningLessonParagraphId=$elearningLessonParagraphId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAudio' title='$websiteText[27]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePrinter' title='$websiteText[52]'>", "$gElearningUrl/lesson/paragraph/print.php?elearningLessonParagraphId=$elearningLessonParagraphId", 600, 600)
    . " <a href='$gElearningUrl/teacher/corner/lesson/paragraph/delete.php?elearningLessonParagraphId=$elearningLessonParagraphId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$websiteText[5]'></a>";

  $str .= "\n<tr><td class='no_style_list_line' style='text-align:left;'>$strName</td>"
    . "<td class='no_style_list_line' style='text-align:right;'>"
    . $strCommand
    . "</td></tr>";
}

$str .= "</table>";

$str .= "</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
