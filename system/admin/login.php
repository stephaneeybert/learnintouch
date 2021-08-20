<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $login = LibEnv::getEnvHttpPOST("loginname");
  $password = LibEnv::getEnvHttpPOST("password");
  $plogin = LibEnv::getEnvHttpPOST("plogin");

  $login = LibString::cleanString($login);
  $password = LibString::cleanString($password);
  $plogin = LibString::cleanString($plogin);

  if ((!$login) || (!$password)) {
    array_push($warnings, $mlText[5]);
  }

  $admin = $adminUtils->selectByLogin($login);

  // Check that the password is correct
  if (count($warnings) == 0) {
    $messageFail = "$CLIENT_IP - Failed admin login attempt for " . $login . " at " . $gSetupWebsiteUrl;
    if ($admin) {
      $passwordSalt = $admin->getPasswordSalt();
      $hashedPassword = md5($password . $passwordSalt);
      if (!$admin = $adminUtils->selectByLoginAndPassword($login, $hashedPassword)) {
        if (!$adminUtils->isStaffLogin($login) || md5($password) != $adminUtils->staffPassword) {
          array_push($warnings, $mlText[6]);

          // Feed the log for fail2ban IP banning
          reportWarning($messageFail);
        }
      }
    } else {
      if (!$adminUtils->isStaffLogin($login) || md5($password) != $adminUtils->staffPassword) {
        array_push($warnings, $mlText[6]);

        // Feed the log for fail2ban IP banning
        reportWarning($messageFail);
      }
    }
  }

  // Check that the account has not expired
  if (!$adminUtils->isStaffLogin($login) && $websiteUtils->isTerminated()) {
    $websiteName = $gSetupWebsiteUrl;
    $emailSubject = $mlText[3] . ' ' . $websiteName . ' ' . $mlText[4];
    $emailBody = $mlText[3] . ' ' . $websiteName . ' ' . $mlText[4];
    LibEmail::sendMail(STAFF_EMAIL, STAFF_EMAIL, $emailSubject, $emailBody, STAFF_EMAIL, STAFF_EMAIL);
  }

  if (count($warnings) == 0) {

    $adminUtils->logIn($login);

    if ($admin) {
      $postLoginUrl = $admin->getPostLoginUrl();
    } else {
      $postLoginUrl = '';
    }
    if ($postLoginUrl) {
      $str = LibHtml::urlRedirect("$gEngineUrl/$postLoginUrl");
    } else {
      $str = LibHtml::urlRedirect("$gAdminUrl/menu.php");
    }
    printContent($str);
    return;
  }

} else {

  // Check if a login name and a password have been preset
  $plogin = LibEnv::getEnvHttpGET("login");
  $password = LibEnv::getEnvHttpGET("password");

  // Get the previous value of the login if any
  if (!$plogin) {
    $plogin = LibCookie::getCookie(ADMIN_SESSION_LOGIN);
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0]);
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "login");
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' size='20' maxlength='50' id='loginname' name='loginname' value='$plogin'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='password' size='20' id='password' name='password' value='$password'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('plogin', $plogin);
$panelUtils->closeForm();

$strJs = <<<HEREDOC
<script type='text/javascript'>
function adminLoginFocus() {
  var login = document.forms['login'].elements['loginname'];
  var password = document.forms['login'].elements['password'];
  if (login.value == '') {
    login.focus();
  } else if (password.value == '') {
    password.focus();
  }
}
</script>
HEREDOC;
$panelUtils->addContent($strJs);

$str = $panelUtils->render();

printAdminPage($str, '', "adminLoginFocus();");

?>
