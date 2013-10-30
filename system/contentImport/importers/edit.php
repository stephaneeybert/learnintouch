<?PHP

require_once("website.php");

$adminUtils->checkAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $contentImportId = LibEnv::getEnvHttpPOST("contentImportId");
  $domainName = LibEnv::getEnvHttpPOST("domainName");

  // The domain name is required
  if (!$domainName) {
    array_push($warnings, $mlText[2]);
    }

  if (count($warnings) == 0) {

  // Format the domain name
  $domainName = LibUtils::formatUrl($domainName);
  $domainName = LibString::stripTraillingSlash($domainName);

  if ($contentImport = $contentImportUtils->selectById($contentImportId)) {
    $contentImport->setDomainName($domainName);
    $contentImportUtils->update($contentImport);
    } else {
    $contentImport = new ContentImport();
    $contentImport->setDomainName($domainName);
    $contentImport->setIsImporting(true);
    $contentImportUtils->insert($contentImport);
    }

  $str = LibHtml::urlRedirect("$gContentImportUrl/importers/admin.php");
  printContent($str);
  return;

  }

  } else {

  $contentImportId = LibEnv::getEnvHttpGET("contentImportId");

  $domainName = '';
  if ($contentImportId) {
    if ($contentImport = $contentImportUtils->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();
      }
    }

  }

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
    }
  }

  $panelUtils->setHeader($mlText[0], "$gContentImportUrl/importers/admin.php");
  $panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
  $panelUtils->openForm($PHP_SELF, "edit");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $panelUtils->addCell("<input type='text' name='domainName' value='$domainName' size='30' maxlength='255'>", "n"));
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('contentImportId', $contentImportId);
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);

?>
