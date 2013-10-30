<?PHP

require_once("website.php");

$adminLogin = $adminUtils->checkAdminLogin();

if (!$adminUtils->isSuperAdmin($adminLogin)) {
  array_push($warnings, $mlText[1]);
}

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $adminId = LibEnv::getEnvHttpPOST("adminId");
  $npassword1 = LibEnv::getEnvHttpPOST("npassword1");
  $npassword2 = LibEnv::getEnvHttpPOST("npassword2");

  // The new password is required
  if ($npassword1 != $npassword2 || (!$npassword1)) {
    array_push($warnings, $mlText[6]);
  }

  if (count($warnings) == 0) {

    // Update the new password
    if ($admin = $adminUtils->selectById($adminId)) {
      $passwordSalt = LibUtils::generateUniqueId(ADMIN_PASSWORD_SALT_LENGTH);
      $hashedPassword = md5($npassword1 . $passwordSalt);
      $admin->setPassword($hashedPassword);
      $admin->setPasswordSalt($passwordSalt);
      $adminUtils->updatePassword($admin);

      $str = LibHtml::urlRedirect("$gAdminUrl/list.php");
      printMessage($str);
      return;
    }

  }

} else {

  $adminId = LibEnv::getEnvHttpGET("adminId");

}

$firstname = '';
$lastname = '';
if ($admin = $adminUtils->selectById($adminId)) {
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
$help = $popupUtils->getHelpPopup($mlText[3], 300, 100);
$panelUtils->setHelp($help);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "rb"), "$firstname $lastname");
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "rb"), "<input type='password' name='npassword1' size='10'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[8], "rb"), "<input type='password' name='npassword2' size='10'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('adminId', $adminId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
