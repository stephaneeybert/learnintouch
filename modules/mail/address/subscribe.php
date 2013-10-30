<?php

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $email = LibEnv::getEnvHttpPOST("email");
  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");

  $email = LibString::cleanString($email);
  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);

  // The email is required
  if (!$email) {
    array_push($warnings, $websiteText[8]);
  } else if (!LibEmail::validate($email)) {
    // The email must have an email format
    array_push($warnings, $websiteText[9]);
  } else if ($email && !LibEmail::validateDomain($email)) {
    // The email domain must be registered as a mail domain
    array_push($warnings, $websiteText[10]);
  }

  if (count($warnings) == 0) {

    if (!$mailAddress = $mailAddressUtils->selectByEmail($email)) {
      $mailAddress = new MailAddress();
      $mailAddress->setEmail($email);
      $mailAddress->setFirstname($firstname);
      $mailAddress->setLastname($lastname);
      $mailAddress->setSubscribe(1);
      $mailAddressUtils->insert($mailAddress);
      $mailAddressId = $mailAddressUtils->getLastInsertId();
    } else {
      $mailAddressId = $mailAddress->getId();
    }

    $mailLists = $mailListUtils->selectAutoSubscribe();
    foreach ($mailLists as $mailList) {
      $mailListId = $mailList->getId();
      $autoSubscribe = LibEnv::getEnvHttpPOST("autoSubscribe_$mailListId");
      $autoSubscribe = LibString::cleanString($autoSubscribe);

      if ($autoSubscribe) {
        if (!$mailListAddress = $mailListAddressUtils->selectByMailListIdAndMailAddressId($mailListId, $mailAddressId)) {
          $mailListAddress = new MailListAddress();
          $mailListAddress->setMailAddressId($mailAddressId);
          $mailListAddress->setMailListId($mailListId);
          $mailListAddressUtils->insert($mailListAddress);
        }
      }
    }

    $preferenceUtils->init($mailUtils->preferences);
    $acknowledge = $preferenceUtils->getValue("MAIL_SUBSCRIPTION_ACKNOWLEDGE");
    $str = "\n<div class='mail_subscription_comment'>$acknowledge</div>";

    $gTemplate->setPageContent($str);
    require_once($gTemplatePath . "render.php");

  }

} else {

  $email = '';
  $firstname = '';
  $lastname = '';

}

$title = $websiteText[0];
$description = $websiteText[1];

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$title</div>";

$str .= "\n<div class='system_comment'>$description</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form id='address_subscribe' name='address_subscribe' action='$gMailUrl/address/subscribe.php' method='post'>";

$label = $popupUtils->getUserTipPopup($websiteText[2], $websiteText[8], 300, 400);
$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='email' size='25' maxlength='255' value='$email' /></div>";

$str .= "\n<div class='system_label'>$websiteText[4]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='firstname' size='25' maxlength='255' value='$firstname' /></div>";

$str .= "\n<div class='system_label'>$websiteText[5]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='lastname' size='25' maxlength='255' value='$lastname' /></div>";

$label = $popupUtils->getUserTipPopup($websiteText[6], $websiteText[7], 300, 400);
$mailLists = $mailListUtils->selectAutoSubscribe();
if (count($mailLists) > 0) {
  $strLists = '';
  foreach ($mailLists as $mailList) {
    $mailListId = $mailList->getId();
    $name = $mailList->getName();
    $strLists .= "<div><input type='checkbox' name='autoSubscribe_$mailListId' value='1' style='vertical-align:middle;'> <span onclick=\"clickAdjacentInputElement(this);\">$name</span></div>";
  }
  $str .= "\n<div class='system_label'>$label</div>";
  $str .= "\n<div class='system_field'>$strLists</div>";
}

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['address_subscribe'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[3]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>

