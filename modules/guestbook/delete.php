<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_GUESTBOOK);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $guestbookId = LibEnv::getEnvHttpPOST("guestbookId");

  // Delete
  $guestbookUtils->delete($guestbookId);

  $str = LibHtml::urlRedirect("$gGuestbookUrl/admin.php");
  printContent($str);
  return;

  } else {

  $guestbookId = LibEnv::getEnvHttpGET("guestbookId");

  if ($guestbook = $guestbookUtils->selectById($guestbookId)) {
    $body = $guestbook->getBody();
    $releaseDate = $guestbook->getReleaseDate();
    $userId = $guestbook->getUserId();
    $email = $guestbook->getEmail();
    $firstname = $guestbook->getFirstname();
    $lastname = $guestbook->getLastname();
    } else {
    $body = '';
    $releaseDate = '';
    $userId = '';
    $firstname = '';
    $lastname = '';
    $email = '';
    }

  if ($user = $userUtils->selectById($userId)) {
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();
    $email = $user->getEmail();
    }

  $panelUtils->setHeader($mlText[0], "$gGuestbookUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $body);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<a href='mailto:$email'>$firstname $lastname</a>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "$releaseDate");
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('guestbookId', $guestbookId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
