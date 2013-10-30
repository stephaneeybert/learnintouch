<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $str = LibHtml::urlRedirect("$gGuestbookUrl/admin.php");
  printContent($str);
  return;

  } else {

  $guestbookId = LibEnv::getEnvHttpGET("guestbookId");

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
    $adminModuleUtils->checkAdminModule(MODULE_GUESTBOOK);
    }

  if (!$guestbook = $guestbookUtils->selectById($guestbookId)) {
    $str = LibHtml::urlRedirect("$gGuestbookUrl/admin.php", $gRedirectDelay);
    printMessage($str);
    return;
    }

  $guestbookId = $guestbook->getId();
  $firstname = $guestbook->getFirstname();
  $lastname = $guestbook->getLastname();
  $email = $guestbook->getEmail();
  $body = $guestbook->getBody();

  $panelUtils->setHeader($mlText[0], "$gGuestbookUrl/admin.php");
  $panelUtils->openForm($PHP_SELF);
  if ($firstname || $lastname) {
    $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "$firstname $lastname");
    $panelUtils->addLine();
    }
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<a href='mailto:$email'>$email</a>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $body);
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('guestbookId', $guestbookId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
