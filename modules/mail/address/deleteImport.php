<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  // Empty the previous list
  $mailAddressUtils->deleteImported();

  $str = LibHtml::urlRedirect("$gMailUrl/address/admin.php");
  printContent($str);
  return;

} else {

  $nbImported = $mailAddressUtils->countImported();

  if ($nbImported == 0) {
    $str = LibHtml::urlRedirect("$gMailUrl/address/admin.php");
    printContent($str);
    return;
  }

  $panelUtils->setHeader($mlText[0], "$gMailUrl/address/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell("$mlText[2]", "br"), $nbImported);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell("$mlText[1]", "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();

  // Display the email addresses of the last import
  if ($mailAddresses = $mailAddressUtils->selectImported()) {
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell("$mlText[3]", "nb"));
    $panelUtils->addLine();
    foreach($mailAddresses as $mailAddress) {
      $mailAddressId = $mailAddress->getId();
      $email = $mailAddress->getEmail();
      $panelUtils->addLine($panelUtils->addCell($email, "l"));
    }
  }

  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
