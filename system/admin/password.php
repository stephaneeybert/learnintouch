<?PHP

require_once("website.php");

$adminUtils->checkAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $login = LibEnv::getEnvHttpPOST("login");
  $cpassword = LibEnv::getEnvHttpPOST("cpassword");
  $npassword1 = LibEnv::getEnvHttpPOST("npassword1");
  $npassword2 = LibEnv::getEnvHttpPOST("npassword2");

  $login = LibString::cleanString($login);
  $cpassword = LibString::cleanString($cpassword);

  // The login name and the current password are required
  if ((!$cpassword) || (!$login)) {
    array_push($warnings, $mlText[4]);
  }

  // The new password is required
  if ($npassword1 != $npassword2 || (!$npassword1)) {
    array_push($warnings, $mlText[6]);
  }

  // Check that the current password is correct
  if ($admin = $adminUtils->selectByLogin($login)) {
    $passwordSalt = $admin->getPasswordSalt();
    $hashedPassword = md5($cpassword . $passwordSalt);
    if (!$admin = $adminUtils->selectByLoginAndPassword($login, $hashedPassword)) {
      array_push($warnings, $mlText[5]);
    }
  }

  if (count($warnings) == 0) {

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

// Get the login value
$login = $adminUtils->getSessionLogin();

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gAdminUrl/list.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$help = $popupUtils->getHelpPopup($mlText[3], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[10], 300, 100);
$panelUtils->addLine($panelUtils->addCell($label, "br"), "<input type='password' name='cpassword' size='10'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[2], $mlText[11], 300, 100);
$panelUtils->addLine($panelUtils->addCell($label, "br"), "<input type='password' name='npassword1' size='10'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[2], $mlText[12], 300, 100);
$panelUtils->addLine($panelUtils->addCell($label, "br"), "<input type='password' name='npassword2' size='10'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('login', $login);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
