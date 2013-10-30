<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

$adminUtils->checkSuperAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  if ($contacts = $contactUtils->selectGarbage()) {
    foreach ($contacts as $contact) {
      $contactId = $contact->getId();
      $contactUtils->delete($contactId);
    }
  }

  $str = LibHtml::urlRedirect("$gContactUrl/garbage.php");
  printMessage($str);
  return;

} else {

  $panelUtils->setHeader($mlText[0], "$gContactUrl/garbage.php");
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
