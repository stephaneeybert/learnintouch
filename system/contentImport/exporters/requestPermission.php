<?PHP

require_once("website.php");

$adminUtils->checkAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $contentImportId = LibEnv::getEnvHttpPOST("contentImportId");

  $xmlResponse = $contentImportUtils->getPermissionRequestXML($contentImportId);

  if ($contentImportUtils->permissionKeyIsValid($xmlResponse)) {
    $contentImportUtils->registerPendingPermissionRequest($contentImportId, $xmlResponse);

    $str = LibHtml::urlRedirect("$gContentImportUrl/exporters/admin.php", $gRedirectDelay);
    printMessage($str);
    return;
  } else {
    $str = $contentImportUtils->renderPermissionRequestMessage($xmlResponse);

    array_push($warnings, $str);
  }

} else {

  $contentImportId = LibEnv::getEnvHttpGET("contentImportId");

}

if ($contentImport = $contentImportUtils->selectById($contentImportId)) {
  $domainName = $contentImport->getDomainName();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gContentImportUrl/exporters/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$help = $popupUtils->getHelpPopup($mlText[1], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $domainName);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('contentImportId', $contentImportId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
