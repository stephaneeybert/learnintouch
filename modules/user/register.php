<?php

require_once("website.php");

$preferenceUtils->init($userUtils->preferences);
if (!$preferenceUtils->getValue("USER_AUTO_REGISTER")) {
  $str = LibHtml::urlRedirect("$gUserUrl/login.php");
  printContent($str);
  return;
}

$websiteText = $languageUtils->getWebsiteText($gUserPath . "registerController.php");
require_once($gUserPath . "registerController.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

if (!$formSubmitted) {
  $email = '';
  $firstname = '';
  $lastname = '';
  $mobilePhone = '';
  $login = '';
  $subscribe = 1;
}

$securityCodeFontSize = $templateUtils->getSecurityCodeFontSize($gIsPhoneClient);

$preferenceUtils->init($userUtils->preferences);

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= "\n<div class='system_comment'>$websiteText[1]</div>";

if ($userUtils->preferenceUtils->getValue("USER_SEND_LOGIN")) {
  $str .= "\n<div class='system_comment'>$websiteText[8]</div>";
}

if (!$formSubmitted && $preferenceUtils->getValue("USER_CONFIRM_EMAIL")) {
    array_push($warnings, $websiteText[16]);
}

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='register_form' id='register_form' action='$gUserUrl/register.php' method='post'>";

$str .= "<div class='system_label'>$websiteText[3]</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='firstname' size='30' maxlength='255' value='$firstname' /></div>";

$str .= "<div class='system_label'>$websiteText[4]</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='lastname' size='30' maxlength='255' value='$lastname' /></div>";

$str .= "<div class='system_label'>$websiteText[12]</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='mobilePhone' size='30' maxlength='30' value='$mobilePhone' /></div>";

$str .= "<div class='system_label'>$websiteText[2]</div>";
$str .= "<div class='system_field'><input class='system_input' type='text' name='email' size='30' maxlength='255' value='$email' /></div>";

$str .= "<div class='system_label'>$websiteText[5]</div>";
$str .= "<div class='system_field'><input class='system_input' type='password' name='password1' size='10' maxlength='10' /></div>";

$str .= "<div class='system_label'>$websiteText[6]</div>";
$str .= "<div class='system_field'><input class='system_input' type='password' name='password2' size='10' maxlength='10' /></div>";

if ($preferenceUtils->getValue("USER_SECURITY_CODE")) {
  $randomSecurityCode = LibUtils::generateUniqueId();
  LibSession::putSessionValue(UTILS_SESSION_RANDOM_SECURITY_CODE, $randomSecurityCode);
  $url = $gUtilsUrl . "/printNumberImage.php?securityCodeFontSize=$securityCodeFontSize";
  $label = $userUtils->getTipPopup($websiteText[9], $websiteText[10], 300, 200);

  $str .= "\n<div class='system_field'><img src='$url' title='$websiteText[11]' alt='' /></div>";
  $str .= "\n<div class='system_label'>$label</div>";
  $str .= "\n<div class='system_field'><input class='system_input' type='text' name='securityCode' size='10' maxlength='10' value='' /></div>";
}

$str .= "<div class='system_field'><input type='checkbox' name='subscribe' checked='checked' value='1'> <span onclick=\"clickAdjacentInputElement(this);\" />$websiteText[7]</span></div>";

$terms = $profileUtils->getWebSiteTermsOfService();
if ($terms) {
$str .= "<div class='system_field'><input type='checkbox' name='termsOfService' value='1'> <span onclick=\"clickAdjacentInputElement(this);\" />$websiteText[13]</span> <a href='$gUserUrl/termsofservice.php' target='_blank' $gJSNoStatus>$websiteText[14]</a></div>";
}

$str .= "<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['register_form'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[15]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
