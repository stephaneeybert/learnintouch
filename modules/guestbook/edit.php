<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_GUESTBOOK);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $guestbookId = LibEnv::getEnvHttpPOST("guestbookId");
  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");
  $email = LibEnv::getEnvHttpPOST("email");
  $body = LibEnv::getEnvHttpPOST("body");

  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);
  $email = LibString::cleanString($email);
  $body = LibString::cleanString($body);

  // The firstname is required
  if (!$firstname) {
    array_push($warnings, $mlText[39]);
  }

  // The lastname is required
  if (!$lastname) {
    array_push($warnings, $mlText[40]);
  }

  // The email must have an email format
  if ($email && !LibEmail::validate($email)) {
    array_push($warnings, $mlText[38]);
  }

  // The email is case insensitive
  $email = strtolower($email);

  if (count($warnings) == 0) {

    if ($guestbook = $guestbookUtils->selectById($guestbookId)) {
      $guestbook->setFirstname($firstname);
      $guestbook->setLastname($lastname);
      $guestbook->setEmail($email);
      $guestbook->setBody($body);
      $guestbookUtils->update($guestbook);
    }

    $str = LibHtml::urlRedirect("$gGuestbookUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $guestbookId = LibEnv::getEnvHttpGET("guestbookId");

  $firstname = '';
  $lastname = '';
  $email = '';
  $body = '';
  if ($guestbookId) {
    if ($guestbook = $guestbookUtils->selectById($guestbookId)) {
      $firstname = $guestbook->getFirstname();
      $lastname = $guestbook->getLastname();
      $email = $guestbook->getEmail();
      $body = $guestbook->getBody();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gGuestbookUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='text' name='firstname'  value='$firstname' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='lastname' value='$lastname' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), "<input type='text' name='email' value='$email' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<textarea name='body' cols='40' rows='6'>$body</textarea>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('guestbookId', $guestbookId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
