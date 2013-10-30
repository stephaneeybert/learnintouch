<?php

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $mobilePhone = LibEnv::getEnvHttpPOST("mobilePhone");
  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");

  $mobilePhone = LibString::cleanString($mobilePhone);
  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);

  // The mobile phone is required
  if (!$mobilePhone) {
    array_push($warnings, $websiteText[8]);
  } else if ($mobilePhone && !is_numeric($mobilePhone)) {
    array_push($warnings, $websiteText[9]);
  } else if ($smsNumber = $smsNumberUtils->selectByMobilePhone($mobilePhone)) {
    array_push($warnings, $websiteText[10]);
  }

  if (count($warnings) == 0) {

    if (!$smsNumber = $smsNumberUtils->selectByMobilePhone($mobilePhone)) {
      $smsNumber = new SmsNumber();
      $smsNumber->setMobilePhone($mobilePhone);
      $smsNumber->setFirstname($firstname);
      $smsNumber->setLastname($lastname);
      $smsNumber->setSubscribe(1);
      $smsNumberUtils->insert($smsNumber);
      $smsNumberId = $smsNumberUtils->getLastInsertId();
    } else {
      $smsNumberId = $smsNumber->getId();
    }

    $smsLists = $smsListUtils->selectAutoSubscribe();
    foreach ($smsLists as $smsList) {
      $smsListId = $smsList->getId();
      $autoSubscribe = LibEnv::getEnvHttpPOST("autoSubscribe_$smsListId");
      $autoSubscribe = LibString::cleanString($autoSubscribe);

      if ($autoSubscribe) {
        if (!$smsListNumber = $smsListNumberUtils->selectBySmsListIdAndSmsNumberId($smsListId, $smsNumberId)) {
          $smsListNumber = new SmsListNumber();
          $smsListNumber->setSmsNumberId($smsNumberId);
          $smsListNumber->setSmsListId($smsListId);
          $smsListNumberUtils->insert($smsListNumber);
        }
      }
    }

    $preferenceUtils->init($smsUtils->preferences);
    $acknowledge = $preferenceUtils->getValue("SMS_SUBSCRIPTION_ACKNOWLEDGE");
    $str = "\n<div class='sms_subscription_comment'>$acknowledge</div>";

    $gTemplate->setPageContent($str);
    require_once($gTemplatePath . "render.php");
  }

} else {

  $mobilePhone = '';
  $firstname = '';
  $lastname = '';

}

$preferenceUtils->init($smsUtils->preferences);

$title = $preferenceUtils->getValue("SMS_REGISTER_TITLE");
if (!$title) {
  $title = $websiteText[0];
}

$description = $preferenceUtils->getValue("SMS_REGISTER_DESCRIPTION");
if (!$description) {
  $description = $websiteText[1];
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$title</div>";

$str .= "\n<div class='system_comment'>$description</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form id='number_subscribe' name='number_subscribe' action='$gSmsUrl/number/subscribe.php' method='post'>";

$str .= "\n<div class='system_label'>$websiteText[2]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='mobilePhone'
  size='25' maxlength='20' value='$mobilePhone' /></div>";

$str .= "\n<div class='system_label'>$websiteText[4]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='firstname' size='25' maxlength='255' value='$firstname' /></div>";

$str .= "\n<div class='system_label'>$websiteText[5]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='lastname' size='25' maxlength='255' value='$lastname' /></div>";

$label = $popupUtils->getUserTipPopup($websiteText[6], $websiteText[7], 300, 400);
$smsLists = $smsListUtils->selectAutoSubscribe();
if (count($smsLists) > 0) {
  $strLists = '';
  foreach ($smsLists as $smsList) {
    $smsListId = $smsList->getId();
    $name = $smsList->getName();
    $strLists .= "<div><input type='checkbox' name='autoSubscribe_$smsListId' value='1' style='vertical-align:middle;'> <span onclick=\"clickAdjacentInputElement(this);\">$name</span></div>";
  }
  $str .= "\n<div class='system_label'>$label</div>";
  $str .= "\n<div class='system_field'>$strLists</div>";
}

$str .= "<div class='system_okay_button'>"
  // An input field is required to have the browser submit the form on Enter key press
  // Otherwise a form with more than one input field is not submitted
  . "<input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['number_subscribe'].submit(); return false;\">" . $websiteText[3] . "</a>"
  . "</div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>

