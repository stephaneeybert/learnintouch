<?php

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $email = LibEnv::getEnvHttpPOST("email");

  $email = LibString::cleanString($email);

  // The email is required
  if (!$email) {
    array_push($warnings, $websiteText[12]);
  } else if (!LibEmail::validate($email)) {
    // The email must have an email format
    array_push($warnings, $websiteText[13]);
  } else if (!$user = $userUtils->selectByEmail($email)) {
    // A user must exist for the specified email
    array_push($warnings, $websiteText[14]);
  }

  if (count($warnings) == 0) {

    if ($user = $userUtils->selectByEmail($email)) {
      // Create a new user password
      $password = LibUtils::generateUniqueId(USER_NEW_PASSWORD_LENGTH);
      $passwordSalt = LibUtils::generateUniqueId(USER_PASSWORD_SALT_LENGTH);
      $hashedPassword = md5($password . $passwordSalt);

      $user->setReadablePassword($password);
      $user->setPassword($hashedPassword);
      $user->setPasswordSalt($passwordSalt);
      error_log("In getPassword.php updating readablePassword: $password passwordSalt: $passwordSalt hashedPassword: $hashedPassword");
      LibEmail::sendMail(STAFF_EMAIL, STAFF_EMAIL, "Modifying user password (getPassword) for $email", "Modifying user password with email: $email and password: $password with passwordSalt: $passwordSalt and hashedPassword: $hashedPassword");
      $userUtils->updatePassword($user);

      // Send an email to the user
      $websiteName = $profileUtils->getProfileValue("website.name");
      $websiteEmail = $profileUtils->getProfileValue("website.email");
      $emailSubject = "$websiteText[0] $websiteName";
      $emailBody = "$websiteText[3] $email<br><br>$websiteText[1] $password<br><br>$websiteName";
      if (LibEmail::validate($email)) {
        LibEmail::sendMail($email, $email, $emailSubject, $emailBody, $websiteEmail, $websiteName);
      }

      $str = '';

      $str .= "\n<div class='system'>";

      $str .= "\n<div class='system_title'>$websiteText[9]</div>";

      $str .= "\n<div class='system_comment'>$websiteText[5] $email</div>";

      $str .= "\n</div>";
    }

    $gTemplate->setPageContent($str);
    require_once($gTemplatePath . "render.php");
  }

} else {

  // Init the unset variables
  $email = '';

}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[9]</div>";

$str .= "\n<div class='system_comment'>$websiteText[10]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$PHP_SELF' method='post'>";

$str .= "\n<div class='system_label'>$websiteText[11]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='email' size='20' maxlength='255'></div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[2]</a></div>";

$str .= "\n<input type=hidden name='formSubmitted' value='1'>";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
