<?PHP

require_once("website.php");

$adminUtils->checkSuperAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");
  $address = LibEnv::getEnvHttpPOST("address");
  $zipCode = LibEnv::getEnvHttpPOST("zipCode");
  $city = LibEnv::getEnvHttpPOST("city");
  $country = LibEnv::getEnvHttpPOST("country");
  $email = LibEnv::getEnvHttpPOST("email");
  $login = LibEnv::getEnvHttpPOST("login");
  $password1 = LibEnv::getEnvHttpPOST("password1");
  $password2 = LibEnv::getEnvHttpPOST("password2");
  $superAdmin = LibEnv::getEnvHttpPOST("superAdmin");
  $preferenceAdmin = LibEnv::getEnvHttpPOST("preferenceAdmin");

  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);
  $address = LibString::cleanString($address);
  $zipCode = LibString::cleanString($zipCode);
  $city = LibString::cleanString($city);
  $country = LibString::cleanString($country);
  $email = LibString::cleanString($email);
  $login = LibString::cleanString($login);
  $password1 = LibString::cleanString($password1);
  $password2 = LibString::cleanString($password2);
  $superAdmin = LibString::cleanString($superAdmin);
  $preferenceAdmin = LibString::cleanString($preferenceAdmin);

  // The firstname and lastname are required
  if ((!$firstname) || (!$lastname)) {
    array_push($warnings, $mlText[11]);
  }

  // The email must have an email format
  if ($email && !LibEmail::validate($email)) {
    array_push($warnings, $mlText[13]);
  }

  // The login and password are required
  if ($password1 != $password2 || (!$login) || (!$password1)) {
    array_push($warnings, $mlText[14]);
  }
  // Check that the login name is not already used
  if ($admin = $adminUtils->selectByLogin($login)) {
    array_push($warnings, $mlText[16]);
  }

  // Check that the login name is not a reserved login name
  if ($adminUtils->isStaffLogin($login)) {
    array_push($warnings, $mlText[15]);
  }

  if (count($warnings) == 0) {
    $passwordSalt = LibUtils::generateUniqueId(ADMIN_PASSWORD_SALT_LENGTH);
    $hashedPassword = md5($password1 . $passwordSalt);

    $firstname = strtolower($firstname);
    $lastname = strtolower($lastname);
    $firstname = ucfirst($firstname);
    $lastname = ucfirst($lastname);

    $admin = new Admin();
    $admin->setFirstname($firstname);
    $admin->setLastname($lastname);
    $admin->setLogin($login);
    $admin->setPassword($hashedPassword);
    $admin->setPasswordSalt($passwordSalt);
    $admin->setSuperAdmin($superAdmin);
    $admin->setPreferenceAdmin($preferenceAdmin);
    $admin->setAddress($address);
    $admin->setZipCode($zipCode);
    $admin->setCity($city);
    $admin->setCountry($country);
    $admin->setEmail($email);
    $adminUtils->insert($admin);
    $adminId = $adminUtils->getLastInsertId();

    // Grant all the modules
    $adminModuleUtils->grantAllModules($adminId);

    // Save the login name in a cookie
    LibCookie::putCookie($adminUtils->cookieAdminLogin, $login, 3600);

    $str = LibHtml::urlRedirect("$gAdminUrl/list.php");
    printMessage($str);
    return;
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gAdminUrl/list.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$help = $popupUtils->getHelpPopup($mlText[10], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='firstname' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='lastname' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), "<input type='text' name='address' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='zipCode' size='10' maxlength='10'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='city' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='country' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='text' name='email' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), "<input type='text' name='login' size='10' maxlength='30'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[20], $mlText[19], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='superAdmin' value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[12], $mlText[17], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "rb"), "<input type='checkbox' name='preferenceAdmin' value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[9], $mlText[18], 300, 100);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='password' name='password1' size='10' maxlength='30'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), "<input type='password' name='password2' size='10' maxlength='30'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
