<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $clearSubscriptions = LibEnv::getEnvHttpPOST("clearSubscriptions");
  $deleteAnyway = LibEnv::getEnvHttpPOST("deleteAnyway");

  if ($elearningCourseUtils->isLockedForLoggedInAdmin($elearningCourseId)) {
    array_push($warnings, $mlText[9]);
  }

  // Check that there are no sessions using the course
  if ($elearningSessionCourses = $elearningSessionCourseUtils->selectByCourseId($elearningCourseId)) {
    array_push($warnings, $mlText[3]);
  }

  // Check that the course is not used by subscriptions
  if (!$clearSubscriptions) {
    if ($elearningSubscriptions = $elearningSubscriptionUtils->selectByCourseId($elearningCourseId)) {
      array_push($warnings, $mlText[6]);
    }
  }

  // Check that there are no exercises nor lesons using the course
  if (!$deleteAnyway) {
    if ($elearningCourseItems = $elearningCourseItemUtils->selectByCourseId($elearningCourseId)) {
      array_push($warnings, $mlText[4]);
    }
  }

  if (count($warnings) == 0) {

    $elearningCourseUtils->deleteCourse($elearningCourseId, $clearSubscriptions);

    $str = LibHtml::urlRedirect("$gElearningUrl/course/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");

  $clearSubscriptions = '';
  $deleteAnyway = '';
  $name = '';
  $description = '';
  if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
    $name = $elearningCourse->getName();
    $description = $elearningCourse->getDescription();
  }

}

if ($elearningSubscriptions = $elearningSubscriptionUtils->selectByCourseId($elearningCourseId)) {
  array_push($warnings, $mlText[10] . ' ' . count($elearningSubscriptions) . ' ' . $mlText[11]);
}

if ($clearSubscriptions == '1') {
  $checkedDeleteContent = "CHECKED";
} else {
  $checkedDeleteContent = '';
}

if ($deleteAnyway == '1') {
  $checkedDeleteAnyway = "CHECKED";
} else {
  $checkedDeleteAnyway = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/course/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[7], $mlText[8], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='clearSubscriptions' $checkedDeleteContent value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[12], $mlText[13], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='deleteAnyway' $checkedDeleteAnyway value='1'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningCourseId', $elearningCourseId);
$panelUtils->addHiddenField('name', $name);
$panelUtils->addHiddenField('description', $description);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
