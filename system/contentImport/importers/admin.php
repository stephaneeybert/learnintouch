<?PHP

require_once("website.php");

$adminUtils->checkAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$parentUrl = LibSession::getSessionValue(UTILS_SESSION_PARENT_URL);

$panelUtils->setHeader($mlText[0], $parentUrl);

$help = $popupUtils->getHelpPopup($mlText[2], 300, 200);
$panelUtils->setHelp($help);

$strCommand = "<a href='$gContentImportUrl/importers/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[4]'></a>"
  . " <a href='$gContentImportUrl/history.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageHistory' title='$mlText[10]'></a>";
$panelUtils->addLine('', '', $panelUtils->addCell("$mlText[5]", "nr"));
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nb"), '', $panelUtils->addCell($strCommand, "nr"));

$contentImports = $contentImportUtils->selectImporting();
foreach ($contentImports as $contentImport) {
  $contentImportId = $contentImport->getId();
  $domainName = $contentImport->getDomainName();

  if ($contentImportUtils->permissionRequestIsPending($contentImportId)) {
    $permissionStatus = "<a href='$gContentImportUrl/importers/handlePermissionRequest.php?contentImportId=$contentImportId&grant=1' $gJSNoStatus title='" . $mlText[8] . "'>" . $mlText[7] . "</a>";
  } else if ($contentImportUtils->permissionRequestWasDenied($contentImportId)) {
    $permissionStatus = $mlText[9];
  } else {
    $permissionStatus = '';
  }

  $strCommand = " <a href='$gContentImportUrl/importers/delete.php?contentImportId=$contentImportId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($domainName, $permissionStatus, $panelUtils->addCell($strCommand, "nr"));
}

$str = $panelUtils->render();

printAdminPage($str);

?>
