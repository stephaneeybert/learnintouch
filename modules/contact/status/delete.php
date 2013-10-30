<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $contactStatusId = LibEnv::getEnvHttpPOST("contactStatusId");

  $contactStatusUtils->deleteStatus($contactStatusId);

  $str = LibHtml::urlRedirect("$gContactUrl/status/admin.php");
  printContent($str);
  return;

} else {

  $contactStatusId = LibEnv::getEnvHttpGET("contactStatusId");

  if ($contactStatus = $contactStatusUtils->selectById($contactStatusId)) {
    $name = $contactStatus->getName();
    $description = $contactStatus->getDescription();
  }

  $panelUtils->setHeader($mlText[0], "$gContactUrl/status/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('contactStatusId', $contactStatusId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
