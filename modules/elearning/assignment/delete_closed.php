<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");

  if (count($warnings) == 0) {

    $systemDate = $clockUtils->getSystemDate();
    $elearningAssignmentUtils->deleteBySubscriptionIdAndClosed($elearningSubscriptionId, $systemDate);

    $str = LibHtml::urlRedirect("$gElearningUrl/assignment/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");

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
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $panelUtils->getOk());
$panelUtils->addHiddenField('elearningSubscriptionId', $elearningSubscriptionId);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
