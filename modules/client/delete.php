<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CLIENT);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $clientId = LibEnv::getEnvHttpPOST("clientId");

  // Delete
  $clientUtils->deleteClient($clientId);

  $str = LibHtml::urlRedirect("$gClientUrl/admin.php");
  printContent($str);
  return;

} else {

  $clientId = LibEnv::getEnvHttpGET("clientId");

  if ($client = $clientUtils->selectById($clientId)) {
    $name = $client->getName();
    $description = $client->getDescription();
    $url = $client->getUrl();
  }

  $panelUtils->setHeader($mlText[0], "$gClientUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $url);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('clientId', $clientId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
