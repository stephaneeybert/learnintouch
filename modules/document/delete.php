<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DOCUMENT);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $documentId = LibEnv::getEnvHttpPOST("documentId");

  // Delete
  $documentUtils->deleteDocument($documentId);

  $str = LibHtml::urlRedirect("$gDocumentUrl/admin.php");
  printContent($str);
  return;

} else {

  $documentId = LibEnv::getEnvHttpGET("documentId");

  if ($document = $documentUtils->selectById($documentId)) {
    $file = $document->getFile();
    $reference = $document->getReference();
    $description = $document->getDescription();
  }

  $panelUtils->setHeader($mlText[0], "$gDocumentUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $file);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $reference);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('documentId', $documentId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
