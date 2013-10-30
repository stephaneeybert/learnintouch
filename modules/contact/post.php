<?php

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$preferenceUtils->init($contactUtils->preferences);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ( $formSubmitted == 1 ) {

  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");
  $email = LibEnv::getEnvHttpPOST("email");
  $organisation = LibEnv::getEnvHttpPOST("organisation");
  $telephone = LibEnv::getEnvHttpPOST("telephone");
  $subject = LibEnv::getEnvHttpPOST("subject");
  $message = LibEnv::getEnvHttpPOST("message");
  $securityCode = LibEnv::getEnvHttpPOST("securityCode");
  $contactRefererId = LibEnv::getEnvHttpPOST("contactRefererId");

  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);
  $email = LibString::cleanString($email);
  $organisation = LibString::cleanString($organisation);
  $telephone = LibString::cleanString($telephone);
  $subject = LibString::cleanString($subject);
  $message = LibString::cleanString($message);
  $securityCode = LibString::cleanString($securityCode);

  // The email is required
  if (!$email) {
    array_push($warnings, $websiteText[10]);
  } else if ($email && !LibEmail::validate($email)) {
    array_push($warnings, $websiteText[11]);
  } else if ($email && !LibEmail::validateDomain($email)) {
    // The email domain must be registered as a mail domain
    array_push($warnings, $websiteText[18]);
  }

  // Check for a security code
  if (!$preferenceUtils->getValue("CONTACT_NO_SECURITY_CODE")) {
    $randomSecurityCode = LibSession::getSessionValue(UTILS_SESSION_RANDOM_SECURITY_CODE);

    if (!$securityCode) {
      // The security code is required
      array_push($warnings, $websiteText[33]);
    } else if ($securityCode != $randomSecurityCode) {
      // The security code is incorrect
      array_push($warnings, $websiteText[34]);
    }
  }

  // The message is required
  if (!$message) {
    array_push($warnings, $websiteText[12]);
  }

  if (count($warnings) == 0) {
    $contactUtils->registerMessage($email, $subject, $message, $firstname, $lastname, $organisation, $telephone, $contactRefererId);

    $acknowledge = $preferenceUtils->getValue("CONTACT_ACKNOWLEDGE");
    if (!$acknowledge) {
      $acknowledge = $websiteText[15];
    }

    $acknowledgementPage = $preferenceUtils->getValue("CONTACT_ACKNOWLEDGEMENT_PAGE");
    if ($acknowledgementPage) {
      $url = $templateUtils->renderPageUrl($acknowledgementPage);
      $str = LibHtml::urlRedirect($url);
    } else {
      $str = "\n<div class='system'>"
        . "\n<div class='system_comment'>"
        . $acknowledge
        . "</div>"
        . "\n</div>";
    }

    $gTemplate->setPageContent($str);
    require_once($gTemplatePath . "render.php");
    exit;
  }

} else {

  // Some variables can be preset when calling the form
  $firstname = LibEnv::getEnvHttpGET("firstname");
  $firstname = urldecode($firstname);
  $firstname = LibString::cleanString($firstname);
  $lastname = LibEnv::getEnvHttpGET("lastname");
  $lastname = urldecode($lastname);
  $lastname = LibString::cleanString($lastname);
  $email = LibEnv::getEnvHttpGET("email");
  $email = urldecode($email);
  $email = LibString::cleanString($email);
  $organisation = '';
  $telephone = '';
  $subject = LibEnv::getEnvHttpGET("subject");
  $subject = urldecode($subject);
  $subject = LibString::cleanString($subject);
  $message = LibEnv::getEnvHttpGET("message");
  $message = urldecode($message);
  $message = LibString::cleanString($message);
  $status = '';
}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$preferenceUtils->init($contactUtils->preferences);
$comment = $preferenceUtils->getValue("CONTACT_COMMENT");
if (!$comment) {
  $comment = $websiteText[8];
}

$str .= "\n<div class='system_comment'>$comment</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$securityCodeFontSize = $templateUtils->getSecurityCodeFontSize($gIsPhoneClient);

$str .= "\n<form id='post_form' name='post_form' action='$gContactUrl/post.php' method='post'>";

$str .= "\n<div class='system_form'>";

$str .= "\n<div class='system_label'>$websiteText[1]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='firstname' value='$firstname' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[2]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='lastname' value='$lastname' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[3] *</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='email' value='$email' size='25' maxlength='255' /></div>";

if (!$preferenceUtils->getValue("CONTACT_NO_SECURITY_CODE")) {
  $randomSecurityCode = LibUtils::generateUniqueId();
  LibSession::putSessionValue(UTILS_SESSION_RANDOM_SECURITY_CODE, $randomSecurityCode);

  $url = $gUtilsUrl . "/printNumberImage.php?securityCodeFontSize=$securityCodeFontSize";
  $label = $userUtils->getTipPopup($websiteText[23], $websiteText[24], 300, 200);
  $str .= "\n<div class='system_label'>$label</div>";
  $str .= "\n<div class='system_field'><input class='system_input' type='text' name='securityCode' size='5' maxlength='5' value='' /> <img src='$url' title='$websiteText[22]' alt='' style='vertical-align:middle;' /></div>";
}

$str .= "\n<div class='system_label'>$websiteText[6]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='subject' value='$subject' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[7] *</div>";
$str .= "\n<div class='system_field'><textarea class='system_input' name='message' cols='23' rows='5'>$message</textarea></div>";

$str .= "\n<div class='system_label'>$websiteText[4]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='organisation' value='$organisation' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$websiteText[5]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='telephone' value='$telephone' size='25' maxlength='20' /></div>";

$contactRefererList = $contactRefererUtils->getList();
if (count($contactRefererList) > 0) {
  $comment = $preferenceUtils->getValue("CONTACT_REFERER_COMMENT");
  if (!$comment) {
    $comment = $websiteText[13];
  }
  $strContactRefererList = LibHtml::getRadioList('contactRefererId', $contactRefererList, '', true, '');
  $str .= "\n<div class='system_comment'>$comment</div>";
  $str .= "\n<div class='system_label'>$websiteText[9]</div>";
  $str .= "\n<div class='system_field'>$strContactRefererList</div>";
}

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['post_form'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[14]</a></div>";

$str .= "\n</div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
