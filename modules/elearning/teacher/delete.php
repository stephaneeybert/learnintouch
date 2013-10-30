<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningTeacherId = LibEnv::getEnvHttpPOST("elearningTeacherId");

  if (count($warnings) == 0) {
    $elearningTeacherUtils->deleteTeacher($elearningTeacherId);

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/admin.php");
    printContent($str);
    return;
  }

} else {

  $elearningTeacherId = LibEnv::getEnvHttpGET("elearningTeacherId");

}

$firstname = '';
$lastname = '';
$email = '';
if ($elearningTeacher = $elearningTeacherUtils->selectById($elearningTeacherId)) {
  $userId = $elearningTeacher->getUserId();
  if ($user = $userUtils->selectById($userId)) {
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();
    $email = $user->getEmail();
  }
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/teacher/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "$firstname $lastname");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $email);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningTeacherId', $elearningTeacherId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
