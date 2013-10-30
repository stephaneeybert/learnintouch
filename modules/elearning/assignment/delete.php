<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningAssignmentId = LibEnv::getEnvHttpPOST("elearningAssignmentId");

  if (count($warnings) == 0) {

    $elearningAssignmentUtils->delete($elearningAssignmentId);

    $str = LibHtml::urlRedirect("$gElearningUrl/assignment/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningAssignmentId = LibEnv::getEnvHttpGET("elearningAssignmentId");

  $elearningSubscriptionId = '';
  $elearningExerciseId = '';
  if ($elearningAssignment = $elearningAssignmentUtils->selectById($elearningAssignmentId)) {
    $elearningSubscriptionId = $elearningAssignment->getElearningSubscriptionId();
    $elearningExerciseId = $elearningAssignment->getElearningExerciseId();
  }

}

$exerciseName = '';
if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $exerciseName = $elearningExercise->getName();
}

$firstname = '';
$lastname = '';
if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
  $userId = $elearningSubscription->getUserId();

  if ($user = $userUtils->selectById($userId)) {
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();
  }
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/assignment/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $firstname . ' ' . $lastname);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $exerciseName);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('elearningAssignmentId', $elearningAssignmentId);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
