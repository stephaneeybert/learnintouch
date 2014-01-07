<?PHP

require_once("website.php");

$adminUtils->checkForStaffLogin();

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$websiteName = $profileUtils->getProfileValue("website.name");
$websiteEmail = $profileUtils->getProfileValue("website.email");

$users = $userUtils->selectWithReadablePassword();

foreach ($users as $user) {
  $email = $user->getEmail();
  $firstname = $user->getFirstname();
  $lastname = $user->getLastname();
  $readablePassword = $user->getReadablePassword();

  $readablePassword = "password";
  $passwordSalt = LibUtils::generateUniqueId(USER_PASSWORD_SALT_LENGTH);
  $hashedPassword = md5($readablePassword . $passwordSalt);
  $user->setReadablePassword($readablePassword);
  $user->setPassword($hashedPassword);
  $user->setPasswordSalt($passwordSalt);
  $userUtils->updatePassword($user);

  $emailSubject = "$websiteText[1] $websiteName";
  $loginUrl = "$gUserUrl/login.php?email=$email&password=$readablePassword";
  $passwordUrl = "$gUserUrl/changePassword.php?email=$email&oldpassword=$readablePassword";
  $emailBody = "$websiteText[2] $readablePassword<br><br>$websiteText[3] <br><br>$websiteText[4] $loginUrl <br><br>$websiteText[5] $passwordUrl <br><br>$websiteText[6] <br><br>$websiteName";
  if (LibEmail::validate($email)) {
    LibEmail::sendMail($email, "$firstname $lastname", $emailSubject, $emailBody, $websiteEmail, $websiteName);
  }
  error_log($emailBody);
}

?>
