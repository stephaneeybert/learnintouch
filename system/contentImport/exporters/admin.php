<?PHP

require_once("website.php");

$adminUtils->checkAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$parentUrl = LibSession::getSessionValue(UTILS_SESSION_PARENT_URL);

$panelUtils->setHeader($mlText[0], $parentUrl);
$help = $popupUtils->getHelpPopup($mlText[2], 300, 200);
$panelUtils->setHelp($help);

$strCommand = "<a href='$gContentImportUrl/exporters/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[4]'></a>";
$panelUtils->addLine('', '', $panelUtils->addCell("$mlText[5]", "nr"));
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell("$mlText[1]", "nb"), '', $panelUtils->addCell($strCommand, "nr"));

$contentImports = $contentImportUtils->selectExporting();
foreach ($contentImports as $contentImport) {
  $contentImportId = $contentImport->getId();
  $domainName = $contentImport->getDomainName();

  if ($contentImportUtils->permissionRequestIsPending($contentImportId)) {
    $pending = $mlText[7];
    } else if ($contentImportUtils->permissionRequestWasDenied($contentImportId)) {
    $pending = $mlText[8];
    } else {
    $pending = '';
    }

  $strCommand = " <a href='$gContentImportUrl/exporters/requestPermission.php?contentImportId=$contentImportId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImagePassword' title='$mlText[6]'></a>"
  . " <a href='$gContentImportUrl/exporters/delete.php?contentImportId=$contentImportId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($domainName, $pending, $panelUtils->addCell($strCommand, "nr"));
  }

$str = $panelUtils->render();

printAdminPage($str);

?>
