<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_USER);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $userId = LibEnv::getEnvHttpPOST("userId");

  if ($elearningTeacher = $elearningTeacherUtils->selectByUserId($userId)) {
    array_push($warnings, $mlText[2]);
  }

  if (count($warnings) == 0) {
    $userUtils->deleteUser($userId);

    $str = LibHtml::urlRedirect("$gUserUrl/admin.php");
    printContent($str);
    return;
  }

} else {

  $userId = LibEnv::getEnvHttpGET("userId");

  if ($elearningTeacher = $elearningTeacherUtils->selectByUserId($userId)) {
    array_push($warnings, $mlText[2]);
  }

  if ($elearningSubscriptions = $elearningSubscriptionUtils->selectByUserId($userId)) {
    $nbSubscriptions = count($elearningSubscriptions);
    array_push($warnings, $mlText[5] . ' ' . $nbSubscriptions . ' ' . $mlText[6]);
    array_push($warnings, $mlText[4]);
  }

  if ($shopOrders = $shopOrderUtils->selectByUserId($userId)) {
    $nbShopOrders = count($shopOrders);
    array_push($warnings, $mlText[7] . ' ' . $nbShopOrders . ' ' . $mlText[8]);
    array_push($warnings, $mlText[9]);
  }

}

$firstname = '';
$lastname = '';
if ($user = $userUtils->selectById($userId)) {
  $firstname = $user->getFirstname();
  $lastname = $user->getLastname();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gUserUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[3], "br"), "$firstname $lastname");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('userId', $userId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
