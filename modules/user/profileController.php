<?php

// The controller for the profile update.

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ( $formSubmitted == 1 ) {

  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");
  $organisation = LibEnv::getEnvHttpPOST("organisation");
  $email = LibEnv::getEnvHttpPOST("email");
  $homePhone = LibEnv::getEnvHttpPOST("homePhone");
  $workPhone = LibEnv::getEnvHttpPOST("workPhone");
  $fax = LibEnv::getEnvHttpPOST("fax");
  $mobilePhone = LibEnv::getEnvHttpPOST("mobilePhone");
  $profile = LibEnv::getEnvHttpPOST("profile");
  $subscribe = LibEnv::getEnvHttpPOST("subscribe");
  $smsSubscribe = LibEnv::getEnvHttpPOST("smsSubscribe");
  $addressId = LibEnv::getEnvHttpPOST("addressId");
  $address1 = LibEnv::getEnvHttpPOST("address1");
  $address2 = LibEnv::getEnvHttpPOST("address2");
  $zipCode = LibEnv::getEnvHttpPOST("zipCode");
  $city = LibEnv::getEnvHttpPOST("city");
  $state = LibEnv::getEnvHttpPOST("state");
  $country = LibEnv::getEnvHttpPOST("country");
  $postalBox = LibEnv::getEnvHttpPOST("postalBox");

  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);
  $organisation = LibString::cleanString($organisation);
  $email = LibString::cleanString($email);
  $homePhone = LibString::cleanString($homePhone);
  $workPhone = LibString::cleanString($workPhone);
  $fax = LibString::cleanString($fax);
  $mobilePhone = LibString::cleanString($mobilePhone);
  $profile = LibString::cleanString($profile);
  $subscribe = LibString::cleanString($subscribe);
  $smsSubscribe = LibString::cleanString($smsSubscribe);
  $address1 = LibString::cleanString($address1);
  $address2 = LibString::cleanString($address2);
  $zipCode = LibString::cleanString($zipCode);
  $city = LibString::cleanString($city);
  $state = LibString::cleanString($state);
  $country = LibString::cleanString($country);
  $postalBox = LibString::cleanString($postalBox);

  // The firstname is required
  if (!$firstname) {
    array_push($warnings, $mlText[20]);
  }

  // The lastname is required
  if (!$lastname) {
    array_push($warnings, $mlText[21]);
  }

  // The email is required
  if (!$email) {
    array_push($warnings, $mlText[0]);
  }

  if (!LibEmail::validate($email)) {
    array_push($warnings, $mlText[1]);
  }

  // Check that the email does not already exist
  if ($user = $userUtils->selectByEmail($email)) {
    if ($userId != $user->getId()) {
      array_push($warnings, $mlText[2]);
    }
  }

  $homePhone = LibString::cleanupPhoneNumber($homePhone);
  $workPhone = LibString::cleanupPhoneNumber($workPhone);
  $fax = LibString::cleanupPhoneNumber($fax);
  $mobilePhone = LibString::cleanupPhoneNumber($mobilePhone);

  // The phone must be a numerical string
  if ($homePhone && !is_numeric(LibString::stripSpaces($homePhone))) {
    array_push($warnings, $mlText[36]);
  }

  // The phone must be a numerical string
  if ($workPhone && !is_numeric(LibString::stripSpaces($workPhone))) {
    array_push($warnings, $mlText[36]);
  }

  // The fax must be a numerical string
  if ($fax && !is_numeric(LibString::stripSpaces($fax))) {
    array_push($warnings, $mlText[35]);
  }

  // The mobile phone must be a numerical string
  if ($mobilePhone && !is_numeric(LibString::stripSpaces($mobilePhone))) {
    array_push($warnings, $mlText[34]);
  }

  // The mobile phone must specified if the subscription to sms is specified
  if (!$mobilePhone && $smsSubscribe) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    $addressId = '';

    if ($user = $userUtils->selectById($userId)) {
      $user->setFirstname($firstname);
      $user->setLastname($lastname);
      $user->setOrganisation($organisation);
      $user->setEmail($email);
      $user->setHomePhone($homePhone);
      $user->setWorkPhone($workPhone);
      $user->setFax($fax);
      $user->setMobilePhone($mobilePhone);
      $user->setProfile($profile);
      $user->setMailSubscribe($subscribe);
      $user->setSmsSubscribe($smsSubscribe);
      $userUtils->update($user);

      $addressId = $user->getAddressId();
    }

    if ($address = $addressUtils->selectById($addressId)) {
      $address->setAddress1($address1);
      $address->setAddress2($address2);
      $address->setZipCode($zipCode);
      $address->setCity($city);
      $address->setState($state);
      $address->setCountry($country);
      $address->setPostalBox($postalBox);
      $addressUtils->update($address);
    } else {
      $address = new Address();
      $address->setAddress1($address1);
      $address->setAddress2($address2);
      $address->setZipCode($zipCode);
      $address->setCity($city);
      $address->setState($state);
      $address->setCountry($country);
      $address->setPostalBox($postalBox);
      $addressUtils->insert($address);
      $addressId = $addressUtils->getLastInsertId();

      $user->setAddressId($addressId);
      $userUtils->update($user);
    }

    // This page can be used by the users or by the administrators
    // They each need to be redirected to different pages
    // Check for a user
    if (isset($isUserEdited) && $isUserEdited) {
      $str = LibHtml::urlRedirect("$gHomeUrl");
    } else if (isset($isAdminEdited) && $isAdminEdited) {
      $str = LibHtml::urlRedirect("$gUserUrl/admin.php");
    } else {
      $str = LibHtml::urlRedirect("$gHomeUrl");
    }
    printContent($str);
    exit;
  }

}

?>
