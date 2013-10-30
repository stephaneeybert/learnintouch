<?PHP

require_once("website.php");

// The administrator may access this page without being logged in if a unique token is used
// This allows an administrator to access this page by clicking on a link in an email
$tokenName = LibEnv::getEnvHttpGET("tokenName");
$tokenValue = LibEnv::getEnvHttpGET("tokenValue");
if ($uniqueTokenUtils->isValid($tokenName, $tokenValue)) {
  // In case the website email is also the one of a registered admin then log in the admin
  $siteEmail = LibEnv::getEnvHttpGET("siteEmail");
  if ($admin = $adminUtils->selectByEmail($siteEmail)) {
    $login = $admin->getLogin();
    $adminUtils->logIn($login);
  }
} else {
  // If no token is used, then
  // check that the administrator is allowed to use the module
  $adminModuleUtils->checkAdminModule(MODULE_USER);
}

$mlText = $languageUtils->getMlText(__FILE__);

$systemDateTime = $clockUtils->getSystemDateTime();

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $userId = LibEnv::getEnvHttpPOST("userId");
  $validate = LibEnv::getEnvHttpPOST("validate");
  $invalidate = LibEnv::getEnvHttpPOST("invalidate");

  if (count($warnings) == 0) {
    if ($user = $userUtils->selectById($userId)) {
      if ($validate) {
        $user->setValidUntil('');
        $user->setUnconfirmedEmail('');
        $userUtils->update($user);
      } else if ($invalidate) {
        $validUntil = $clockUtils->incrementDays($systemDateTime, -1);
        $user->setValidUntil($validUntil);
        $userUtils->update($user);
      }
    }

    $str = LibHtml::urlRedirect("$gUserUrl/admin.php");
    printContent($str);
    return;
  }

} else {

  $userId = LibEnv::getEnvHttpGET("userId");
  $validate = LibEnv::getEnvHttpGET("validate");
  $invalidate = LibEnv::getEnvHttpGET("invalidate");

}

$firstname = '';
$lastname = '';
$validUntil = '';
if ($user = $userUtils->selectById($userId)) {
  $firstname = $user->getFirstname();
  $lastname = $user->getLastname();
  $validUntil = $user->getValidUntil();

  $validUntil = $clockUtils->systemToLocalNumericDate($validUntil);
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

if ($validate) {
  $labelQuestion = $mlText[2];
} else if ($invalidate) {
  $labelQuestion = $mlText[3];
} else {
  $labelQuestion = '';
}

$panelUtils->setHeader($mlText[0], "$gUserUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $firstname . ' ' . $lastname);
$panelUtils->addLine();
if (!$clockUtils->systemDateIsSet($validUntil)) {
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "br"), '');
} else if ($clockUtils->systemDateIsGreaterOrEqual($validUntil, $systemDateTime)) {
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "br"), $validUntil);
} else {
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "br"), $validUntil);
}

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($labelQuestion, "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('validate', $validate);
$panelUtils->addHiddenField('invalidate', $invalidate);
$panelUtils->addHiddenField('userId', $userId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
