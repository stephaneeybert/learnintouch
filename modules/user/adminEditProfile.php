<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_USER);

$userId = LibEnv::getEnvHttpGET("userId");
if (!$userId) {
  $userId = LibEnv::getEnvHttpPOST("userId");
}

// Call the controller
$isAdminEdited = true;
$mlText = $languageUtils->getMlText($gUserPath . "profileController.php");
require_once($gUserPath . "profileController.php");

$mlText = $languageUtils->getMlText(__FILE__);

if (!$formSubmitted) {

  $firstname = '';
  $lastname = '';
  $organisation = '';
  $email = '';
  $homePhone = '';
  $workPhone = '';
  $fax = '';
  $mobilePhone = '';
  $profile = '';
  $subscribe = '';
  $smsSubscribe = '';
  $addressId = '';
  if ($user = $userUtils->selectById($userId)) {
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();
    $organisation = $user->getOrganisation();
    $email = $user->getEmail();
    $homePhone = $user->getHomePhone();
    $workPhone = $user->getWorkPhone();
    $fax = $user->getFax();
    $mobilePhone = $user->getMobilePhone();
    $profile = $user->getProfile();
    $subscribe = $user->getMailSubscribe();
    $smsSubscribe = $user->getSmsSubscribe();
    $addressId = $user->getAddressId();
  }

}

if ($subscribe == '1') {
  $checkedSubscribe = "CHECKED";
} else {
  $checkedSubscribe = '';
}

if ($smsSubscribe == '1') {
  $checkedSmsSubscribe = "CHECKED";
} else {
  $checkedSmsSubscribe = '';
}

// If an address exists then get current properties
$address1 = '';
$address2 = '';
$zipCode = '';
$city = '';
$state = '';
$country = '';
$postalBox = '';
if ($address = $addressUtils->selectById($addressId)) {
  $address1 = $address->getAddress1();
  $address2 = $address->getAddress2();
  $zipCode = $address->getZipCode();
  $city = $address->getCity();
  $state = $address->getState();
  $country = $address->getCountry();
  $postalBox = $address->getPostalBox();
}

$panelUtils->setHeader($mlText[0], "$gUserUrl/admin.php");

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
  }
}

$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[11], "nbr"), "<input type='text' name='firstname' size='30' maxlength='255' value='$firstname'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[12], "nbr"), "<input type='text' name='lastname' size='30' maxlength='255' value='$lastname'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[18], "nbr"), "<input type='text' name='organisation' size='30' maxlength='255' value='$organisation'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[13], "nbr"), "<input type='text' name='email' size='30' maxlength='255' value='$email'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[16], "nbr"), "<input type=checkbox name='subscribe' $checkedSubscribe value='1'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), "<input type='text' name='homePhone' size='20'
    maxlength='20' value='$homePhone'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[10], "nbr"), "<input type='text' name='workPhone' size='20'
    maxlength='20' value='$workPhone'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), "<input type='text' name='fax' size='20' maxlength='20' value='$fax'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[17], "nbr"), "<input type='text' name='mobilePhone' size='20' maxlength='20' value='$mobilePhone'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[14], "nbr"), "<input type=checkbox name='smsSubscribe' $checkedSmsSubscribe value='1'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[15], "nbr"), "<textarea name='profile' cols='28' rows='5'>$profile</textarea>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->addCell($mlText[1], "nb"));
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='address1' size='30' maxlength='255' value='$address1'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), "<input type='text' name='address2' size='30' maxlength='255' value='$address2'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='zipCode' size='10' maxlength='10' value='$zipCode'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='city' size='30' maxlength='255' value='$city'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='state' size='30' maxlength='255' value='$state'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='text' name='country' size='30' maxlength='255' value='$country'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[19], "nbr"), "<input type='text' name='postalBox' size='30' maxlength='255' value='$postalBox'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('userId', $userId);
$panelUtils->addHiddenField('addressId', $addressId);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
