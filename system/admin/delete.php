<?PHP

require_once("website.php");

$adminUtils->checkSuperAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $adminId = LibEnv::getEnvHttpPOST("adminId");

  if ($admin = $adminUtils->selectById($adminId)) {
    $login = $admin->getLogin();
  }

  // A staff admin cannot be deleted
  if ($adminUtils->isStaffLogin($login)) {
    array_push($warnings, $mlText[3]);
  }

  // An admin who is a news editor cannot be deleted
  if ($newsEditor = $newsEditorUtils->selectByAdminId($adminId)) {
    array_push($warnings, $mlText[5]);
  }

  // An admin cannot delete oneself
  $loginSession = $adminUtils->getSessionLogin();
  if ($login == $loginSession) {
    array_push($warnings, $mlText[2]);
  }

  if (count($warnings) == 0) {

    $adminUtils->deleteAdmin($adminId);

    $str = LibHtml::urlRedirect("$gAdminUrl/list.php");
    printContent($str);
    return;

  }

} else {

  $adminId = LibEnv::getEnvHttpGET("adminId");

}

if ($admin = $adminUtils->selectById($adminId)) {
  $login = $admin->getLogin();
  $firstname = $admin->getFirstname();
  $lastname = $admin->getLastname();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gAdminUrl/list.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "br"), "$firstname $lastname");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('adminId', $adminId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
