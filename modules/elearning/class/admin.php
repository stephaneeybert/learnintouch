<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$elearningTeacherId = LibEnv::getEnvHttpPOST("elearningTeacherId");

if (!$elearningTeacherId) {
  $elearningTeacherId = LibSession::getSessionValue(ELEARNING_SESSION_TEACHER);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_SESSION, $elearningTeacherId);
}

$teacherName = '';
if ($elearningTeacher = $elearningTeacherUtils->selectById($elearningTeacherId)) {
  $teacherUserId = $elearningTeacher->getUserId();
  if ($teacherUser = $userUtils->selectById($teacherUserId)) {
    $teacherName = $teacherUser->getFirstname() . ' ' . $teacherUser->getLastname();
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");
$help = $popupUtils->getHelpPopup($mlText[10], 300, 200);
$panelUtils->setHelp($help);

$panelUtils->openForm($PHP_SELF);
$strSuggestTeacher = $commonUtils->ajaxAutocompleteForList("$gElearningUrl/teacher/suggest.php", "teacherName", "elearningTeacherId");
$panelUtils->addContent($strSuggestTeacher);
$panelUtils->addHiddenField('elearningTeacherId', $elearningTeacherId);
$panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), $panelUtils->addCell("<input type='text' id='teacherName' value='$teacherName' /> " . $panelUtils->getTinyOk(), "n"), '');
$panelUtils->closeForm();
$panelUtils->addLine();

$strCommand = "<a href='$gElearningUrl/class/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

if ($elearningTeacherId > 0) {
  $elearningClasses = $elearningClassUtils->selectBySubscriptionWithTeacherId($elearningTeacherId);
} else {
  $elearningClasses = $elearningClassUtils->selectAll();
}

$panelUtils->openList();
foreach ($elearningClasses as $elearningClass) {
  $elearningClassId = $elearningClass->getId();
  $name = $elearningClass->getName();
  $description = $elearningClass->getDescription();

  $strCommand = '';
  if ($elearningTeacherId > 0) {
    $strCommand .= " <a href='$gElearningUrl/subscription/send.php?elearningTeacherId=$elearningTeacherId&elearningClassId=$elearningClassId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEmail' title='$mlText[7]'></a>";
  }

  $strCommand .= " <a href='$gElearningUrl/subscription/course.php?elearningClassId=$elearningClassId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCourse' title='$mlText[4]'></a>";

  $strCommand .= " <a href='$gElearningUrl/subscription/edit.php?elearningClassId=$elearningClassId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[11]'></a>"
    . " <a href='$gElearningUrl/subscription/admin.php?elearningClassId=$elearningClassId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImagePeople' title='$mlText[12]'></a>"
    . " <a href='$gElearningUrl/class/edit.php?elearningClassId=$elearningClassId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gElearningUrl/class/delete.php?elearningClassId=$elearningClassId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $description, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
