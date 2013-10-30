<?PHP

require_once("website.php");

$adminUtils->checkAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $contentImportId = LibEnv::getEnvHttpPOST("contentImportId");

  $contentImportUtils->delete($contentImportId);

  $str = LibHtml::urlRedirect("$gContentImportUrl/exporters/admin.php");
  printContent($str);
  return;

  } else {

  $contentImportId = LibEnv::getEnvHttpGET("contentImportId");

  if ($contentImport = $contentImportUtils->selectById($contentImportId)) {
    $domainName = $contentImport->getDomainName();
    }

  }

  $panelUtils->setHeader($mlText[0], "$gContentImportUrl/exporters/admin.php");
  $panelUtils->openForm($PHP_SELF, "edit");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $panelUtils->addCell($domainName, "n"));
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('contentImportId', $contentImportId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);

?>
