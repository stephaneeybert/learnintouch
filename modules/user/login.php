<?php

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $email = LibEnv::getEnvHttpPOST("email");
  $password = LibEnv::getEnvHttpPOST("password");
  $autologin = LibEnv::getEnvHttpPOST("autologin");

  $email = LibString::cleanString($email);
  $password = LibString::cleanString($password);

  // The email is required
  if (!$email) {
    array_push($warnings, $websiteText[9]);
  }

  // The password is required
  if (!$password) {
    array_push($warnings, $websiteText[12]);
  } else if ($userUtils->checkUserPassword($email, $password) == false) {
    // Check that the current password is correct
    array_push($warnings, $websiteText[13]);
  }

  // Check if the user account is temporary and not valid any longer
  if ($email && $userUtils->noLongerValidUserCannotLogin($email)) {
    $localValidUntil = $userUtils->temporaryUserIsNoLongerValid($email);
    $message = $websiteText[10] . ' ' . $localValidUntil;
    array_push($warnings, $message);
    $subject = $websiteText[16] . ' ' . $email . ' ' . $websiteText[17];
    $subject = urlencode($subject);
    $message = $websiteText[11] . " <a href='$gContactUrl/post.php?subject=$subject' $gJSNoStatus>" . $websiteText[2] . "</a> " . $websiteText[14];
    array_push($warnings, $message);
  }

  // Check if the user email address is not yet confirmed
  if ($email && $userUtils->userEmailAddressIsNotYetConfirmed($email)) {
    $message = $websiteText[4];
    array_push($warnings, $message);
    $message = $websiteText[15] . ' ' . $email;
    array_push($warnings, $message);
    $subject = $websiteText[16] . ' ' . $email . ' ' . $websiteText[8];
    $subject = urlencode($subject);
    $message = $websiteText[11] . " <a href='$gContactUrl/post.php?subject=$subject' $gJSNoStatus>" . $websiteText[2] . "</a> " . $websiteText[14];
    array_push($warnings, $message);
  }

  if (count($warnings) == 0) {
    // Open a session
    $userUtils->openUserSession($email);

    if ($user = $userUtils->selectByEmail($email)) {
      // Store the date and time of the last login of the user
      $systemDateTime = $clockUtils->getSystemDateTime();
      $user->setLastLogin($systemDateTime);
      // If the user was given a generated password
      // then remove the password stored in a readable format
      // For secutiry reasons the readable password is kept for as short a time as possible
      $user->setReadablePassword('');
      $userUtils->update($user);
    }

    // If auto login is on then save the user login and password in a cookie
    if ($autologin == 1) {
      // Get the password as stored in the database as the source for the php encrypting
      $storedPassword = $userUtils->getUserPassword($email);

      LibCookie::putCookie($userUtils->cookieAutoLogin, "$email:$storedPassword", $userUtils->getAutoLoginDuration());
    } else {
      LibCookie::deleteCookie($userUtils->cookieAutoLogin);
    }

    $url = $userUtils->getPostUserLoginUrl();

    $str = LibHtml::urlRedirect($url);
    printContent($str);
    exit;
  }

} else {

  $email = LibEnv::getEnvHttpGET("email");
  $password = LibEnv::getEnvHttpGET("password");
  $autologin = LibEnv::getEnvHttpGET("autologin");

}

$preferenceUtils->init($userUtils->preferences);

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[22]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<div class='system_comment'>";

$commonUtils->preventPageCaching();

$strFacebookLogin = $facebookUtils->renderLoginSetup(false);
if ($strFacebookLogin) {
  $str .= $strFacebookLogin;
  $str .= ' ' . $facebookUtils->renderLoginButton();
}

$strLinkedinLogin = $linkedinUtils->renderLoginSetup(false);
if ($strLinkedinLogin) {
  $str .= $strLinkedinLogin;
  $str .= ' ' . $linkedinUtils->renderLoginButton();
}

$withTwitter = $twitterUtils->setup(false);
if ($withTwitter) {
  $str .= ' ' . $twitterUtils->renderLoginButton();
}

$withGoogle = $googleUtils->setup(false);
if ($withGoogle) {
  $str .= ' ' . $googleUtils->renderLoginButton();
}

$str .= "\n</div>";

$str .= "\n<div class='system_comment'>$websiteText[23]</div>";

$str .= "\n<form name='login_form' id='login_form' action='$gUserUrl/login.php' method='post'>";

$str .= "\n<div class='system_label'>$websiteText[24]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='email' value='$email' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[25]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='password' name='password' value='$password' size='25' maxlength='20' /></div>";

if ($autologin == '1') {
  $checkedAutologin = "checked='checked'";
} else {
  $checkedAutologin = '';
}

$automaticLoginPeriod = $preferenceUtils->getValue("USER_LOGIN_DURATION");

$label = $userUtils->getTipPopup($websiteText[20], $websiteText[21], 300, 400);
$labelCookie = $userUtils->getTipPopup($websiteText[5], $websiteText[7], 300, 400);
$str .= "<div class='system_label'>$label $labelCookie</div>";
$str .= "\n<div class='system_field'>";
$str .= "\n<input type='checkbox' name='autologin' $checkedAutologin value='1' />";
if ($automaticLoginPeriod > 1) {
  $label = $websiteText[4];
} else {
  $label = $websiteText[1];
}
$str .= "\n <span onclick=\"clickAdjacentInputElement(this);\">$websiteText[0] $automaticLoginPeriod $label</span></div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['login_form'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[3]</a></div>";

if ($preferenceUtils->getValue("USER_AUTO_REGISTER")) {
  $str .= "\n<div class='system_comment'>$websiteText[28] <a href='$gUserUrl/register.php' $gJSNoStatus>$websiteText[27]</a></div>";
}

$str .= "\n<div class='system_comment'>$websiteText[26] <a href='$gUserUrl/getPassword.php' $gJSNoStatus>$websiteText[27]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);

require_once($gTemplatePath . "render.php");

?>
