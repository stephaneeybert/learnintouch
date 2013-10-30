<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $mailAddressId = LibEnv::getEnvHttpPOST("mailAddressId");

  $mailAddressUtils->deleteAddress($mailAddressId);

  $str = LibHtml::urlRedirect("$gMailUrl/address/admin.php");
  printContent($str);
  return;

  } else {

  $mailAddressId = LibEnv::getEnvHttpGET("mailAddressId");

  $email = '';
  if ($mailAddress = $mailAddressUtils->selectById($mailAddressId)) {
    $email = $mailAddress->getEmail();
    $firstname = $mailAddress->getFirstname();
    $lastname = $mailAddress->getLastname();
    }

  $panelUtils->setHeader($mlText[0], "$gMailUrl/address/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $email);
  $panelUtils->addLine();
  if ($firstname || $lastname) {
    $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $firstname . ' ' . $lastname);
    $panelUtils->addLine();
    }
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('mailAddressId', $mailAddressId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
