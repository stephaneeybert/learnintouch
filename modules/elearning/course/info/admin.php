<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");
if (!$elearningCourseId) {
  $elearningCourseId = LibSession::getSessionValue(ELEARNING_SESSION_COURSE);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_COURSE, $elearningCourseId);
}

$elearningCourseInfoId = LibEnv::getEnvHttpGET("elearningCourseInfoId");
$deleteCourseInfo = LibEnv::getEnvHttpGET("deleteCourseInfo");

if ($deleteCourseInfo && $elearningCourseInfoId) {
  $elearningCourseInfoUtils->deleteCourseInfo($elearningCourseInfoId);
}

$swapWithPrevious = LibEnv::getEnvHttpGET("swapWithPrevious");
$swapWithNext = LibEnv::getEnvHttpGET("swapWithNext");

if ($swapWithPrevious && $elearningCourseId) {
  $elearningCourseInfoUtils->swapWithPrevious($elearningCourseInfoId);
} else if ($swapWithNext && $elearningCourseId) {
  $elearningCourseInfoUtils->swapWithNext($elearningCourseInfoId);
}

$name = '';
$description = '';
if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
  $name = $elearningCourse->getName();
  $description = $elearningCourse->getDescription();
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/course/admin.php");
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

$strCourseName = $name;
if ($description) {
  $strCourseName .= ' - ' . $description;
}

$panelUtils->addLine('<b>' . $mlText[4] . '</b> ' . $strCourseName, '');
$panelUtils->addLine();
$strCommand = "<a href='$gElearningUrl/course/info/edit.php?elearningCourseId=$elearningCourseId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[17]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$panelUtils->openList();
if ($elearningCourseInfos = $elearningCourseInfoUtils->selectByCourseId($elearningCourseId)) {
  foreach ($elearningCourseInfos as $elearningCourseInfo) {
    $elearningCourseInfoId = $elearningCourseInfo->getId();
    $headline = $elearningCourseInfo->getHeadline();
    $information = $elearningCourseInfo->getInformation();

    $strSwap = "<a href='$PHP_SELF?elearningCourseInfoId=$elearningCourseInfoId&swapWithPrevious=1' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[9]'></a>"
      . " <a href='$PHP_SELF?elearningCourseInfoId=$elearningCourseInfoId&swapWithNext=1' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[1]'></a>";

    $strCommand = "<a href='$gElearningUrl/course/info/edit.php?elearningCourseInfoId=$elearningCourseInfoId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageCompose' title='$mlText[20]'></a>"
      . " <a href='$PHP_SELF?elearningCourseInfoId=$elearningCourseInfoId&deleteCourseInfo=1' onclick='javascript:return(confirmDelete(this))' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[8]'></a>";

    $panelUtils->addLine($panelUtils->addCell($strSwap . ' ' . $headline, "v"), $panelUtils->addCell($strCommand, "nbr"));
  }
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("elearning_course_compose_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
