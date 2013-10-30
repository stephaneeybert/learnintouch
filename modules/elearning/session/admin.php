<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$closedStatus = LibEnv::getEnvHttpPOST("closedStatus");

if (!$closedStatus) {
  $closedStatus = LibSession::getSessionValue(ELEARNING_SESSION_SESSION_STATUS);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_SESSION_STATUS, $closedStatus);
}

$closedStatusList = array(
  '-1' => '',
  ELEARNING_SESSION_OPEN => $mlText[20],
  ELEARNING_SESSION_CLOSED => $mlText[21],
  ELEARNING_SESSION_NOT_OPENED => $mlText[19],
);
$strSelectClosed = LibHtml::getSelectList("closedStatus", $closedStatusList, $closedStatus, true);

$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");
$help = $popupUtils->getHelpPopup($mlText[10], 300, 400);
$panelUtils->setHelp($help);

$strCommand = ''
  . " <a href='$gElearningUrl/course/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCourse' title='$mlText[23]'></a>";

$panelUtils->openForm($PHP_SELF);
$label = $popupUtils->getTipPopup($mlText[8], $mlText[17], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell($strSelectClosed, "n"), '', '', '', '', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->closeForm();

$strCommand = "<a href='$gElearningUrl/session/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";

$panelUtils->addLine();
$labelCourse = $popupUtils->getTipPopup($mlText[4], $mlText[16], 300, 200);
$panelUtils->addLine($panelUtils->addCell("$mlText[25]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell("$mlText[7]", "nb"), $panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell($labelCourse, "nb"), '', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

$systemDate = $clockUtils->getSystemDate();

if ($closedStatus == ELEARNING_SESSION_NOT_OPENED) {
  $elearningSessions = $elearningSessionUtils->selectNotYetOpened($systemDate);
} else if ($closedStatus == ELEARNING_SESSION_OPEN) {
  $elearningSessions = $elearningSessionUtils->selectCurrentlyOpened($systemDate);
} else if ($closedStatus == ELEARNING_SESSION_CLOSED) {
  $elearningSessions = $elearningSessionUtils->selectClosed($systemDate);
} else {
  $elearningSessions = $elearningSessionUtils->selectAll();
}

$panelUtils->openList();
foreach ($elearningSessions as $elearningSession) {
  $elearningSessionId = $elearningSession->getId();
  $name = $elearningSession->getName();
  $openDate = $elearningSession->getOpenDate();
  $closeDate = $elearningSession->getCloseDate();
  $closed = $elearningSession->getClosed();

  $strOpenDate = $clockUtils->systemToLocalNumericDate($openDate);
  if ($clockUtils->systemDateIsSet($closeDate)) {
    $strCloseDate = $clockUtils->systemToLocalNumericDate($closeDate);
  } else {
    $strCloseDate = '';
  }

  $strClosed = '';
  if ($closed) {
    $strStatus = $mlText[9];
  } else if ($openDate > $systemDate) {
    $strStatus = $mlText[12];
  } else if ($clockUtils->systemDateIsSet($closeDate) && $systemDate > $closeDate) {
    $strStatus = $mlText[13];
  } else {
    $strStatus = $mlText[14];
  }

  $strCommand = ''
    . " <a href='$gElearningUrl/session/edit.php?elearningSessionId=$elearningSessionId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gElearningUrl/session/courses.php?elearningSessionId=$elearningSessionId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageList' title='$mlText[11]'></a>"
    . " <a href='$gElearningUrl/session/delete.php?elearningSessionId=$elearningSessionId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  // Display one course
  $strCourse = '';
  if ($elearningCourses = $elearningCourseUtils->selectBySessionId($elearningSessionId)) {
    if (count($elearningCourses) > 0) {
      $elearningCourse = $elearningCourses[0];
      $strCourse = $elearningCourse->getName();
      if (count($elearningCourses) > 1) {
        $strCourse = " <a href='$gElearningUrl/session/courses.php?elearningSessionId=$elearningSessionId' $gJSNoStatus title='$mlText[15]'>$strCourse</a>";
      }
    }
  }

  $panelUtils->addLine($name, $strOpenDate, $strCloseDate, $strStatus, $strCourse, '', $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
