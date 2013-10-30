<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $contactId = LibEnv::getEnvHttpPOST("contactId");

  // Delete
  $contactUtils->putInGarbage($contactId);

  $str = LibHtml::urlRedirect("$gContactUrl/admin.php");
  printContent($str);
  return;

  } else {

  $contactId = LibEnv::getEnvHttpGET("contactId");

  if ($contact = $contactUtils->selectById($contactId)) {
    $contactId = $contact->getId();
    $firstname = $contact->getFirstname();
    $lastname = $contact->getLastname();
    $email = $contact->getEmail();
    $subject = $contact->getSubject();
    }

  $panelUtils->setHeader($mlText[0], "$gContactUrl/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $subject);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "$firstname $lastname $email");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('contactId', $contactId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
