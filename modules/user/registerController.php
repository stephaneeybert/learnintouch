<?php

// The controller for the user registration.

$preferenceUtils->init($userUtils->preferences);

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $email = LibEnv::getEnvHttpPOST("email");
  $subscribe = LibEnv::getEnvHttpPOST("subscribe");
  $termsOfService = LibEnv::getEnvHttpPOST("termsOfService");
  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");
  $mobilePhone = LibEnv::getEnvHttpPOST("mobilePhone");
  $securityCode = LibEnv::getEnvHttpPOST("securityCode");
  $password1 = LibEnv::getEnvHttpPOST("password1");
  $password2 = LibEnv::getEnvHttpPOST("password2");

  $email = LibString::cleanString($email);
  $subscribe = LibString::cleanString($subscribe);
  $termsOfService = LibString::cleanString($termsOfService);
  $mobilePhone = LibString::cleanString($mobilePhone);
  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);
  $securityCode = LibString::cleanString($securityCode);
  $password1 = LibString::cleanString($password1);
  $password2 = LibString::cleanString($password2);

  // The email is case insensitive
  $email = strtolower($email);

  // The email is required
  if (!$email) {
    array_push($warnings, $websiteText[37]);
  } else if (!LibEmail::validate($email)) {
    // The email must have an email format
    array_push($warnings, $websiteText[38]);
  } else {
    $validateDomainName = $preferenceUtils->getValue("USER_VALIDATE_DOMAIN_NAME");

    if ($validateDomainName) {
      if ($email && !LibEmail::validateDomain($email)) {
        // The email domain must be registered as a mail domain
        array_push($warnings, $websiteText[44]);
      }
    }
  }

  // Check that the email is not already used
  if ($user = $userUtils->selectByEmail($email)) {
    array_push($warnings, $websiteText[46]);
  }

  // The firstname is required
  if (!$firstname) {
    array_push($warnings, $websiteText[39]);
  }

  // The lastname is required
  if (!$lastname) {
    array_push($warnings, $websiteText[40]);
  }

  // The terms of service must be accepted
  $terms = $profileUtils->getWebSiteTermsOfService();
  if ($terms) {
    if (!$termsOfService) {
      array_push($warnings, $websiteText[2]);
    }
  }

  // Check that the password contains only alphanumerical characters
  $cleanedPassword = LibString::stripNonFilenameChar($password1);
  if ($cleanedPassword != $password1) {
    array_push($warnings, "$websiteText[43] $cleanedPassword");
  }

  // Check that the password is correct
  if (!$password1) {
    // The password is required
    array_push($warnings, $websiteText[41]);
  } else if ($password1 != $password2) {
    // The password must be confirmed
    array_push($warnings, $websiteText[42]);
  }

  // Remove the non numeric characters
  $mobilePhone = LibString::cleanupPhoneNumber($mobilePhone);

  if ($preferenceUtils->getValue("USER_SECURITY_CODE")) {
    $randomSecurityCode = LibSession::getSessionValue(UTILS_SESSION_RANDOM_SECURITY_CODE);
    if (!$securityCode) {
      // The security code is required
      array_push($warnings, $websiteText[33]);
    } else if ($securityCode != $randomSecurityCode) {
      // The security code is incorrect
      array_push($warnings, $websiteText[34]);
    }
  }

  if (count($warnings) == 0) {

    $systemDateTime = $clockUtils->getSystemDateTime();

    $passwordSalt = LibUtils::generateUniqueId(USER_PASSWORD_SALT_LENGTH);
    $hashedPassword = md5($password1 . $passwordSalt);

    $firstname = strtolower($firstname);
    $lastname = strtolower($lastname);
    $firstname = ucfirst($firstname);
    $lastname = ucfirst($lastname);

    $user = new User();
    $user->setEmail($email);
    $user->setMailSubscribe($subscribe);
    $user->setPassword($hashedPassword);
    $user->setPasswordSalt($passwordSalt);
    // The password is also stored in a readable format (non encrypted)
    // so that it'll be possible to mail it later to the user
    // The readable password is then removed at the first user login
    $user->setReadablePassword($password1);
    $user->setFirstname($firstname);
    $user->setLastname($lastname);
    $user->setMobilePhone($mobilePhone);
    $user->setLastLogin($systemDateTime);
    $user->setCreationDateTime($systemDateTime);
    $userUtils->insert($user);
    $userId = $userUtils->getLastInsertId();

    // Send an email address confirmation link to the user
    if ($userUtils->preferenceUtils->getValue("USER_CONFIRM_EMAIL")) {
      if ($user = $userUtils->selectById($userId)) {
        $user->setUnconfirmedEmail(true);
        $userUtils->update($user);
        $userUtils->sendConfirmationEmail($userId);
      }
    }

    // Send the email address and password to the user
    if ($userUtils->preferenceUtils->getValue("USER_SEND_LOGIN")) {
      $userUtils->sendLoginPassword($email, $password1);
    }

    // This page can be used by the users or by the administrators
    // They each need to be redirected to different pages
    $referer = LibEnv::getEnvSERVER('HTTP_REFERER');
    // Check if a user registered himself
    if (strstr($referer, "register.php")) {

      // Set a period of validity for the user account
      // A user that registers himself can log in for a period of time only
      $userUtils->setValidityPeriod($userId);

      // User auto login after registration process
      if ($preferenceUtils->getValue("USER_DIRECT_LOGIN" && !$userUtils->preferenceUtils->getValue("USER_CONFIRM_EMAIL"))) {
        $userUtils->openUserSession($email);

        $postLoginUrl = $gHomeUrl;
      } else {
        $postLoginUrl = "$gUserUrl/login.php";
      }

    } else if (strstr($referer, "add.php")) {
      $postLoginUrl = "$gUserUrl/adminEditProfile.php?userId=$userId";
    } else {
      $postLoginUrl = "$gUserUrl/login.php";
    }

    $str = LibHtml::urlRedirect($postLoginUrl);
    printContent($str);
    exit;

  }

}

?>
