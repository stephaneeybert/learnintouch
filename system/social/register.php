<?php

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$preferenceUtils->init($userUtils->preferences);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $facebookUserId = LibEnv::getEnvHttpPOST("facebookUserId");
  $linkedinUserId = LibEnv::getEnvHttpPOST("linkedinUserId");
  $twitterUserId = LibEnv::getEnvHttpPOST("twitterUserId");
  $googleUserId = LibEnv::getEnvHttpPOST("googleUserId");
  $email = LibEnv::getEnvHttpPOST("email");
  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");
  $subscribe = LibEnv::getEnvHttpPOST("subscribe");

  $email = LibString::cleanString($email);
  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);
  $subscribe = LibString::cleanString($subscribe);

  // The email is case insensitive
  $email = strtolower($email);

  // The email is required
  if (!$email) {
    array_push($warnings, $websiteText[4]);
  } else if (!LibEmail::validate($email)) {
    // The email must have an email format
    array_push($warnings, $websiteText[5]);
  }

  // The firstname is required
  if (!$firstname) {
    array_push($warnings, $websiteText[7]);
  }

  // The lastname is required
  if (!$lastname) {
    array_push($warnings, $websiteText[8]);
  }

  $password = LibUtils::generateUniqueId(USER_NEW_PASSWORD_LENGTH);

  if (count($warnings) == 0) {

    if ($user = $userUtils->selectByEmail($email)) {
      $userId = $user->getId();
    } else {
      $passwordSalt = LibUtils::generateUniqueId(USER_PASSWORD_SALT_LENGTH);
      $hashedPassword = md5($password . $passwordSalt);

      $user = new User();
      $user->setFirstname($firstname);
      $user->setLastname($lastname);
      $user->setEmail($email);
      $user->setPassword($hashedPassword);
      $user->setPasswordSalt($passwordSalt);
      $user->setMailSubscribe($subscribe);
      $userUtils->insert($user);
      $userId = $userUtils->getLastInsertId();

      // Send the email address and password to the user
      if ($userUtils->preferenceUtils->getValue("USER_SEND_LOGIN")) {
        $userUtils->sendLoginPassword($email, $password);
      }

      // Set a period of validity for the user account
      // A user that registers himself can log in for a period of time only
      $userUtils->setValidityPeriod($userId);
    }

    if ($userId) {
      if (!$socialUser = $facebookUtils->selectByUserId($userId)) {
        $socialUser = new SocialUser();
        if ($facebookUserId) {
          $socialUser->setFacebookUserId($facebookUserId);
        }
        if ($linkedinUserId) {
          $socialUser->setLinkedinUserId($linkedinUserId);
        }
        if ($twitterUserId) {
          $socialUser->setTwitterUserId($twitterUserId);
        }
        if ($googleUserId) {
          $socialUser->setGoogleUserId($googleUserId);
        }
        $socialUser->setUserId($userId);
        $facebookUtils->insert($socialUser);
      } else {
        if ($facebookUserId) {
          $socialUser->setFacebookUserId($facebookUserId);
        }
        if ($linkedinUserId) {
          $socialUser->setLinkedinUserId($linkedinUserId);
        }
        if ($twitterUserId) {
          $socialUser->setTwitterUserId($twitterUserId);
        }
        if ($googleUserId) {
          $socialUser->setGoogleUserId($googleUserId);
        }
        $facebookUtils->update($socialUser);
      }

      $userUtils->openUserSession($email);
    }

    $url = $userUtils->getPostUserLoginUrl();
    $str = LibHtml::urlRedirect($url);
    printContent($str);
    exit;
  }

}

// Init the unset variables
if (!$formSubmitted) {
  $subscribe = '';
  $facebookUserId = LibEnv::getEnvHttpGET("facebookUserId");
  $linkedinUserId = LibEnv::getEnvHttpGET("linkedinUserId");
  $twitterUserId = LibEnv::getEnvHttpGET("twitterUserId");
  $googleUserId = LibEnv::getEnvHttpGET("googleUserId");
  $email = LibEnv::getEnvHttpGET("email");
  $firstname = LibEnv::getEnvHttpGET("firstname");
  $lastname = LibEnv::getEnvHttpGET("lastname");
}

if ($email == 'null' || $email == 'undefined') {
  $email = '';
}

if ($firstname == 'null' || $firstname == 'undefined') {
  $firstname = '';
}

if ($lastname == 'null' || $lastname == 'undefined') {
  $lastname = '';
}

$email = utf8_decode($email);
$firstname = utf8_decode($firstname);
$lastname = utf8_decode($lastname);

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

if ($facebookUserId) {
  $str .= "\n<div class='system_comment'>$websiteText[11]</div>";
} else if ($linkedinUserId) {
  $str .= "\n<div class='system_comment'>$websiteText[12]</div>";
} else if ($twitterUserId) {
  $str .= "\n<div class='system_comment'>$websiteText[14]</div>";
} else if ($googleUserId) {
  $str .= "\n<div class='system_comment'>$websiteText[13]</div>";
}

$str .= "\n<div class='system_comment'>$websiteText[1]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form id='userRegister' name='userRegister' action='$PHP_SELF' method='post'>";

$str .= "<div class='system_label'>$websiteText[2]</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='email' size='25' maxlength='255' value='$email' /></div>";

$str .= "<div class='system_label'>$websiteText[9]</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='firstname' size='25' maxlength='255' value='$firstname' /></div>";

$str .= "<div class='system_label'>$websiteText[10]</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='lastname' size='25' maxlength='255' value='$lastname' /></div>";

$str .= "<div class='system_label'>$websiteText[3]</div>";
$str .= "<div class='system_field'><input type='checkbox' name='subscribe' checked='checked' value='$subscribe' /></div>";

$str .= "<div class='system_okay_button'>"
  . "<input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' />"
  // An input field is required to have the browser submit the form on Enter key press
  // Otherwise a form with more than one input field is not submitted
  . "<input type='submit' value='' style='display:none;' />"
  . " <a href='#' onclick=\"document.forms['userRegister'].submit(); return false;\">" . $websiteText[6] . "</a>"
  . "</div>";

$str .= "\n<input type='hidden' name='facebookUserId' value='$facebookUserId' />";
$str .= "\n<input type='hidden' name='linkedinUserId' value='$linkedinUserId' />";
$str .= "\n<input type='hidden' name='twitterUserId' value='$twitterUserId' />";
$str .= "\n<input type='hidden' name='googleUserId' value='$googleUserId' />";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
