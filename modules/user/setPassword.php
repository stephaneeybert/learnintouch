<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_USER);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ( $formSubmitted == 1 ) {

  $userId = LibEnv::getEnvHttpPOST("userId");
  $newpassword1 = LibEnv::getEnvHttpPOST("newpassword1");
  $newpassword2 = LibEnv::getEnvHttpPOST("newpassword2");

  $newpassword1 = LibString::cleanString($newpassword1);
  $newpassword2 = LibString::cleanString($newpassword2);

  // The new password is required
  if (!$newpassword1) {
    array_push($warnings, $mlText[20]);
  }

  // Check that the password contains only alphanumerical characters
  $cleanedPassword = LibString::stripNonFilenameChar($newpassword1);
  if ($cleanedPassword != $newpassword1) {
    array_push($warnings, $mlText[43] . ' ' . $cleanedPassword);
  }

  // The new password must be confirmed
  if ($newpassword1 != $newpassword2) {
    array_push($warnings, $mlText[21]);
  }

  // Update the new password into the database
  if ($user = $userUtils->selectById($userId)) {
    $passwordSalt = LibUtils::generateUniqueId(USER_PASSWORD_SALT_LENGTH);
    $hashedPassword = md5($newpassword1 . $passwordSalt);

    $user->setPassword($hashedPassword);
    $user->setPasswordSalt($passwordSalt);
    // The password is also stored in a readable format (non encrypted)
    // so that it'll be possible to mail it later to the user
    // The readable password is then removed at the first user login
    $user->setReadablePassword($newpassword1);
    $userUtils->updatePassword($user);
    $email = $user->getEmail();
    error_log("In setPassword.php updating readablePassword: $newpassword1 passwordSalt: $passwordSalt hashedPassword: $hashedPassword");
    LibEmail::sendMail(STAFF_EMAIL, STAFF_EMAIL, "Modifying user password (setPassword) for $email", "Modifying user password with email: $email and password: $newpassword1 with passwordSalt: $passwordSalt and hashedPassword: $hashedPassword");
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();
  }

  if (count($warnings) == 0) {

    // Send an email to the user
    $websiteName = $profileUtils->getProfileValue("website.name");
    $websiteEmail = $profileUtils->getProfileValue("website.email");
    $emailSubject = "$mlText[10] $websiteName";
    $emailBody = "$mlText[11] <B>$newpassword1</B><br><br>$mlText[12]<br><br>$websiteName";
    if (LibEmail::validate($email)) {
      LibEmail::sendMail($email, "$firstname $lastname", $emailSubject, $emailBody, $websiteEmail, $websiteName);
    }

    $str = LibHtml::urlRedirect("$gUserUrl/admin.php");
    printMessage($str);
    return;

  }

} else {

  $userId = LibEnv::getEnvHttpGET("userId");

  // Get the user properties
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

$panelUtils->setHeader($mlText[0], "$gUserUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$help = $popupUtils->getHelpPopup($mlText[3], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), "$firstname $lastname");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[4], $mlText[6], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "br"), "<input type='password' name='newpassword1' size='10' maxlength='10'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[5], $mlText[7], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "br"), "<input type='password' name='newpassword2' size='10' maxlength='10'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('userId', $userId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
