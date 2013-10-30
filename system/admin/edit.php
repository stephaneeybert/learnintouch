<?PHP

require_once("website.php");

$adminUtils->checkAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $adminId = LibEnv::getEnvHttpPOST("adminId");
  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");
  $address = LibEnv::getEnvHttpPOST("address");
  $zipCode = LibEnv::getEnvHttpPOST("zipCode");
  $city = LibEnv::getEnvHttpPOST("city");
  $country = LibEnv::getEnvHttpPOST("country");
  $email = LibEnv::getEnvHttpPOST("email");
  $profile = LibEnv::getEnvHttpPOST("profile");
  $login = LibEnv::getEnvHttpPOST("login");
  $superAdmin = LibEnv::getEnvHttpPOST("superAdmin");
  $preferenceAdmin = LibEnv::getEnvHttpPOST("preferenceAdmin");
  $postLoginUrl = LibEnv::getEnvHttpPOST("postLoginUrl");

  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);
  $address = LibString::cleanString($address);
  $zipCode = LibString::cleanString($zipCode);
  $city = LibString::cleanString($city);
  $country = LibString::cleanString($country);
  $email = LibString::cleanString($email);
  $profile = LibString::cleanString($profile);
  $login = LibString::cleanString($login);
  $superAdmin = LibString::cleanString($superAdmin);
  $preferenceAdmin = LibString::cleanString($preferenceAdmin);
  $postLoginUrl = LibString::cleanString($postLoginUrl);

  // The firstname and lastname are required
  if ((!$firstname) || (!$lastname)) {
    array_push($warnings, $mlText[12]);
  }

  // The email must have an email format
  if ($email && !LibEmail::validate($email)) {
    array_push($warnings, $mlText[13]);
  }

  // Check that the login name is not a reserved login name
  if ($adminUtils->isStaffLogin($login)) {
    array_push($warnings, $mlText[15]);
  }

  if (count($warnings) == 0) {

    if ($admin = $adminUtils->selectById($adminId)) {
      $firstname = strtolower($firstname);
      $lastname = strtolower($lastname);
      $firstname = ucfirst($firstname);
      $lastname = ucfirst($lastname);

      $admin->setFirstname($firstname);
      $admin->setLastname($lastname);
      if ($login) {
        $admin->setLogin($login);
      }
      $admin->setSuperAdmin($superAdmin);
      $admin->setPreferenceAdmin($preferenceAdmin);
      $admin->setAddress($address);
      $admin->setZipCode($zipCode);
      $admin->setCity($city);
      $admin->setCountry($country);
      $admin->setEmail($email);
      $admin->setProfile($profile);
      $admin->setPostLoginUrl($postLoginUrl);
      $adminUtils->update($admin);
    }

    $str = LibHtml::urlRedirect("$gAdminUrl/list.php");
    printContent($str);
    return;

  }

} else {

  $adminId = LibEnv::getEnvHttpGET("adminId");

  if ($admin = $adminUtils->selectById($adminId)) {
    $firstname = $admin->getFirstname();
    $lastname = $admin->getLastname();
    $login = $admin->getLogin();
    $superAdmin = $admin->getSuperAdmin();
    $preferenceAdmin = $admin->getPreferenceAdmin();
    $address = $admin->getAddress();
    $zipCode = $admin->getZipCode();
    $city = $admin->getCity();
    $country = $admin->getCountry();
    $email = $admin->getEmail();
    $profile = $admin->getProfile();
    $postLoginUrl = $admin->getPostLoginUrl();
  }

}

// An non super admin can edit only itself
// A super admin can edit itself and other non super admins
$loginSession = $adminUtils->getSessionLogin();
if (!$adminUtils->isStaffLogin($loginSession) && $loginSession != $login && !($adminUtils->isSuperAdmin($loginSession) && !$adminUtils->isSuperAdmin($login))) {
  $str = $mlText[16];
  $str .= LibHtml::urlDisplayRedirect("$gAdminUrl/list.php", $gRedirectDelay);
  printMessage($str);
  return;
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$postLoginUrls = $adminUtils->getPostLoginUrls();
$postLoginUrlList = Array('' => '');
foreach ($postLoginUrls as $wPostLoginUrl => $page) {
  $postLoginUrlList[$wPostLoginUrl] = $page;
}
$strSelectPostLoginUrl = LibHtml::getSelectList("postLoginUrl", $postLoginUrlList, $postLoginUrl);

$panelUtils->setHeader($mlText[0], "$gAdminUrl/list.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "rb"), "<input type='text' name='firstname' value='$firstname' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "rb"), "<input type='text' name='lastname' value='$lastname' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "rb"), "<input type='text' name='email' value='$email' size='30' maxlength='255'>");
$panelUtils->addLine();
if ($adminUtils->isLoggedSuperAdmin() && $loginSession != $login) {
  $panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), "<input type='text' name='login' value='$login' size='10' maxlength='10'>");
  $panelUtils->addLine();
}
$panelUtils->addLine($panelUtils->addCell($mlText[3], "rb"), "<input type='text' name='address' value='$address' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[4], "rb"), "<input type='text' name='zipCode' value='$zipCode' size='10' maxlength='10'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "rb"), "<input type='text' name='city' value='$city' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[6], "rb"), "<input type='text' name='country' value='$country' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[9], "rb"), "<textarea name='profile' cols='40' rows='6'>$profile</textarea>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[17], $mlText[18], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "rb"), $strSelectPostLoginUrl);

if ($superAdmin == '1') {
  $checkedSuper = "CHECKED";
}  else {
  $checkedSuper = '';
}

if ($adminUtils->isLoggedSuperAdmin() && $loginSession != $login) {
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[20], $mlText[19], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "rb"), "<input type='checkbox' name='superAdmin' $checkedSuper value='1'>");
}  else {
  $panelUtils->addHiddenField('superAdmin', $superAdmin);
}

if ($preferenceAdmin == '1') {
  $checkedPreferenceAdmin = "CHECKED";
}  else {
  $checkedPreferenceAdmin = '';
}

$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[10], $mlText[11], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "rb"), "<input type='checkbox' name='preferenceAdmin' $checkedPreferenceAdmin value='1'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('adminId', $adminId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
