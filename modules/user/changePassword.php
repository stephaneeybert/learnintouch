<?php

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$email = $userUtils->checkUserLogin();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ( $formSubmitted == 1 ) {

  $email = LibEnv::getEnvHttpPOST("email");
  $oldpassword = LibEnv::getEnvHttpPOST("oldpassword");
  $newpassword1 = LibEnv::getEnvHttpPOST("newpassword1");
  $newpassword2 = LibEnv::getEnvHttpPOST("newpassword2");

  $email = LibString::cleanString($email);
  $oldpassword = LibString::cleanString($oldpassword);
  $newpassword1 = LibString::cleanString($newpassword1);
  $newpassword2 = LibString::cleanString($newpassword2);

  // The email is required
  if (!$email) {
    array_push($warnings, $websiteText[25]);
  }

  // The password is required
  if (!$oldpassword) {
    array_push($warnings, $websiteText[27]);
  } else if ($userUtils->checkUserPassword($email, $oldpassword) == false) {
    // Check that the current password is correct
    array_push($warnings, $websiteText[28]);
  }

  // The new password is required
  if (!$newpassword1) {
    array_push($warnings, $websiteText[20]);
  } else if ($newpassword1 != $newpassword2) {
    // The new password must be confirmed
    array_push($warnings, $websiteText[21]);
  }

  if (count($warnings) == 0) {

    // Update the new password into the database
    if ($user = $userUtils->selectByEmail($email)) {
      $passwordSalt = LibUtils::generateUniqueId(USER_PASSWORD_SALT_LENGTH);
      $hashedPassword = md5($newpassword1 . $passwordSalt);
      $user->setPassword($hashedPassword);
      $user->setPasswordSalt($passwordSalt);
      $userUtils->updatePassword($user);
    }

    $str = LibHtml::urlRedirect("$gHomeUrl");
    printContent($str);
    return;
  }

} else {

  $email = LibEnv::getEnvHttpGET("email");

}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= "\n<div class='system_comment'>$websiteText[99]</div>";

$str .= "\n<div class='system_comment'>$websiteText[3]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gUserUrl/changePassword.php' method='post'>";

$str .= "\n<div class='system_label'>$websiteText[1]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='email' size='20' maxlength='255' value='$email' /></div>";

$str .= "\n<div class='system_label'>$websiteText[2]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='password' name='oldpassword' size='10' maxlength='10' /></div>";

$str .= "\n<div class='system_label'>$websiteText[4]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='password' name='newpassword1' size='10' maxlength='10' /></div>";

$str .= "\n<div class='system_label'>$websiteText[5]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='password' name='newpassword2' size='10' maxlength='10' /></div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[6]</a></div>";

$str .= "\n<div><input type='hidden' name='formSubmitted' value='1' /></div>";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
