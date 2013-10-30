<?php

require_once("website.php");

$email = $userUtils->checkUserLogin();

$userId = '';
if ($user = $userUtils->selectByEmail($email)) {
  $userId = $user->getId();
}

$isUserEdited = true;
$mlText = $languageUtils->getWebsiteText($gUserPath . "profileController.php");
require_once($gUserPath . "profileController.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

if (!$formSubmitted) {

  $homePhone = '';
  $workPhone = '';
  $fax = '';
  $mobilePhone = '';
  $firstname = '';
  $lastname = '';
  $organisation = '';
  $profile = '';
  $subscribe = '';
  $smsSubscribe = '';
  $addressId = '';
  $address1 = '';
  $address2 = '';
  $zipCode = '';
  $city = '';
  $state = '';
  $country = '';
  $postalBox = '';

  if ($user = $userUtils->selectByEmail($email)) {
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();
    $organisation = $user->getOrganisation();
    $homePhone = $user->getHomePhone();
    $workPhone = $user->getWorkPhone();
    $fax = $user->getFax();
    $mobilePhone = $user->getMobilePhone();
    $profile = $user->getProfile();
    $subscribe = $user->getMailSubscribe();
    $smsSubscribe = $user->getSmsSubscribe();
    $addressId = $user->getAddressId();

    if ($address = $addressUtils->selectById($addressId)) {
      $address1 = $address->getAddress1();
      $address2 = $address->getAddress2();
      $zipCode = $address->getZipCode();
      $city = $address->getCity();
      $state = $address->getState();
      $country = $address->getCountry();
      $postalBox = $address->getPostalBox();
    }

  }

}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<div class='system_comment'>"
  . "\n<a href='$gUserUrl/changePassword.php' $gJSNoStatus>$websiteText[22]</a>"
  . "\n - <a href='$gUserUrl/image.php?userId=$userId.php' $gJSNoStatus>$websiteText[2]</a>"
  . "\n</div>";

$str .= "\n<div class='system_comment'>$websiteText[23]</div>";

$str .= "\n<form name='edit' id='edit' action='$gUserUrl/editProfile.php' method='post'>";

$strImage = $userUtils->renderImage($userId);

$str .= "\n<div class='system_label'>$websiteText[4]</div>";
$str .= "\n<div class='system_field'>$strImage</div>";

$str .= "\n<div class='system_label'>$websiteText[25]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='firstname' size='30' maxlength='255' value='$firstname' /></div>";

$str .= "\n<div class='system_label'>$websiteText[26]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='lastname' size='30' maxlength='255' value='$lastname' /></div>";

$str .= "\n<div class='system_label'>$websiteText[11]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='organisation' size='30' maxlength='255' value='$organisation' /></div>";

$str .= "\n<div class='system_label'>$websiteText[5]</div>";
$str .= "\n<div class='system_field'>$email</div>";

$str .= "\n<div class='system_label'>$websiteText[8]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='homePhone' size='20'
  maxlength='20' value='$homePhone' /></div>";

$str .= "\n<div class='system_label'>$websiteText[10]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='workPhone' size='20'
  maxlength='20' value='$workPhone' /></div>";

$str .= "\n<div class='system_label'>$websiteText[9]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='fax' size='20' maxlength='20' value='$fax' /></div>";

$str .= "\n<div class='system_label'>$websiteText[7]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='mobilePhone' size='20' maxlength='20' value='$mobilePhone' /></div>";

$str .= "\n<div class='system_label'>$websiteText[3]</div>";
$str .= "\n<div class='system_field'><textarea class='system_input' name='profile' cols='30' rows='5'>$profile</textarea></div>";

if ($subscribe == '1') {
  $checkedSubscribe = "checked='checked'";
} else {
  $checkedSubscribe = '';
}

if ($smsSubscribe == '1') {
  $checkedSmsSubscribe = "checked='checked'";
} else {
  $checkedSmsSubscribe = '';
}

$str .= "\n<div class='system_label'>$websiteText[1]</div>";
$str .= "\n<div class='system_field'><input type='checkbox' name='subscribe' $checkedSubscribe value='1'
  /> $websiteText[24]</div>";

$str .= "\n<div class='system_label'>$websiteText[19]</div>";
$str .= "\n<div class='system_field'><input type='checkbox' name='smsSubscribe' $checkedSmsSubscribe value='1'
  /> $websiteText[20]</div>";

$str .= "\n<div class='system_label'>$websiteText[13]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='address1' size='30' maxlength='255' value='$address1' /></div>";

$str .= "\n<div class='system_label'>$websiteText[14]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='address2' size='30' maxlength='255' value='$address2' /></div>";

$str .= "\n<div class='system_label'>$websiteText[15]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='zipCode' size='10' maxlength='10' value='$zipCode' /></div>";

$str .= "\n<div class='system_label'>$websiteText[16]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='city' size='30' maxlength='255' value='$city' /></div>";

$str .= "\n<div class='system_label'>$websiteText[17]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='state' size='30' maxlength='255' value='$state' /></div>";

$str .= "\n<div class='system_label'>$websiteText[18]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='country' size='30' maxlength='255' value='$country' /></div>";

$str .= "\n<div class='system_label'>$websiteText[12]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='postalBox' size='30' maxlength='255' value='$postalBox' /></div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[21]</a></div>";

$str .= "\n<div><input type='hidden' name='formSubmitted' value='1' /></div>";
$str .= "\n<div><input type='hidden' name='userId' value='$userId' /></div>";
$str .= "\n<div><input type='hidden' name='email' value='$email' /></div>";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);

require_once($gTemplatePath . "render.php");

?>
