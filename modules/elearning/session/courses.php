<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
  $elearningSessionCourseId = LibEnv::getEnvHttpPOST("elearningSessionCourseId");
  $delete = LibEnv::getEnvHttpPOST("delete");

  if ($delete) {
    if ($elearningSubscriptions = $elearningSubscriptionUtils->selectBySessionIdAndCourseId($elearningSessionId, $elearningCourseId)) {
      foreach ($elearningSubscriptions as $elearningSubscription) {
        $elearningSubscription->setCourseId('');
        $elearningSubscriptionUtils->update($elearningSubscription);
      }
    }
    $elearningSessionCourseUtils->deleteSessionCourse($elearningSessionId, $elearningCourseId);
  } else {
    if (!$elearningSessionCourseUtils->selectBySessionIdAndCourseId($elearningSessionId, $elearningCourseId)) {
      $elearningSessionCourse = new ElearningSessionCourse();
      $elearningSessionCourse->setElearningSessionId($elearningSessionId);
      $elearningSessionCourse->setElearningCourseId($elearningCourseId);
      $elearningSessionCourseUtils->insert($elearningSessionCourse);
    }
  }

}

$elearningSessionId = LibEnv::getEnvHttpGET("elearningSessionId");

if (!$elearningSessionId) {
  $elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");
}

$openDate = '';
$closeDate = '';
$closed = '';
if ($elearningSessionId) {
  if ($elearningSession = $elearningSessionUtils->selectById($elearningSessionId)) {
    $sessionName = $elearningSession->getName();
    $openDate = $elearningSession->getOpenDate();
    $closeDate = $elearningSession->getCloseDate();
    $closed = $elearningSession->getClosed();
  }
}

$openDate = $clockUtils->systemToLocalNumericDate($openDate);
$closeDate = $clockUtils->systemToLocalNumericDate($closeDate);

// If no courses exist yet then redirect to the course creation page
$elearningCourses = $elearningCourseUtils->selectAll();
if (count($elearningCourses) == 0) {
  array_push($warnings, $mlText[8]);
  array_push($warnings, "<a href='$gElearningUrl/course/edit.php' $gJSNoStatus>" . $mlText[11] . "</a>");
}

$elearningCourses = $elearningCourseUtils->selectCoursesNotAssignedToSession($elearningSessionId);
$elearningCourseList = Array('' => '');
foreach ($elearningCourses as $elearningCourse) {
  $wId = $elearningCourse->getId();
  // Check that the course contains some exercises
  if ($elearningCourseItems = $elearningCourseItemUtils->selectByCourseId($wId)) {
    $wName = $elearningCourse->getName();
    $elearningCourseList[$wId] = $wName;
  }
}
$strSelectCourse = LibHtml::getSelectList("elearningCourseId", $elearningCourseList, '', true);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/session/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$help = $popupUtils->getHelpPopup($mlText[3], 300, 300);
$panelUtils->setHelp($help);
$strCommand = " <a href='$gElearningUrl/session/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSession' title='$mlText[6]'></a>"
  . " <a href='$gElearningUrl/course/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageCourse' title='$mlText[9]'></a>";
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $panelUtils->addCell($sessionName, "n"), $panelUtils->addCell('<b>' . $mlText[2] . '</b> ' . $strSelectCourse, "n"), '', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $openDate, '', '', '');
$panelUtils->addLine($panelUtils->addCell($mlText[10], "nbr"), $closeDate, '', '', '');
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningSessionId', $elearningSessionId);
$panelUtils->closeForm();
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[4], "br"), '', '', '', '');
$panelUtils->addLine();

$elearningCourses = $elearningCourseUtils->selectBySessionId($elearningSessionId);

$panelUtils->openList();
foreach ($elearningCourses as $elearningCourse) {
  $elearningCourseId = $elearningCourse->getId();
  $name = $elearningCourse->getName();

  $panelUtils->openForm($PHP_SELF);
  $strCommand = ''
    . "<input type='image' border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[5]'>"
    . "<input type='hidden' name='elearningCourseId' value='$elearningCourseId'>";
  $panelUtils->addLine($panelUtils->addCell($name, "r"), '', '', '', $panelUtils->addCell($strCommand, "nr"));
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('delete', 1);
  $panelUtils->addHiddenField('elearningSessionId', $elearningSessionId);
  $panelUtils->closeForm();
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
