<?PHP

require_once("website.php");

// The administrator may access this page without being logged in if a unique token is used
// This allows a administrator to access this page by clicking on a link in an email
$tokenName = LibEnv::getEnvHttpGET("tokenName");
$tokenValue = LibEnv::getEnvHttpGET("tokenValue");
if (!$tokenName) {
  $tokenName = LibEnv::getEnvHttpPOST("tokenName");
  $tokenValue = LibEnv::getEnvHttpPOST("tokenValue");
}
if (!$uniqueTokenUtils->isValid($tokenName, $tokenValue)) {
  $adminUtils->checkAdminLogin();
}

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $contentImportId = LibEnv::getEnvHttpPOST("contentImportId");
  $grant = LibEnv::getEnvHttpPOST("grant");

  if (!$websiteUtils->isCurrentWebsiteOption('OPTION_ELEARNING_EXPORT')) {
    $contentImportUtils->denyPermissionRequest($contentImportId);
    $str = $mlText[7];
  } else if ($grant) {
    $contentImportUtils->grantPermissionRequest($contentImportId);
    $str = $mlText[5];
  } else {
    $contentImportUtils->denyPermissionRequest($contentImportId);
    $str = $mlText[6];
  }

  $str = LibHtml::urlRedirect("$gContentImportUrl/importers/admin.php");
  printMessage($str);
  return;

} else {

  $contentImportId = LibEnv::getEnvHttpGET("contentImportId");
  $grant = LibEnv::getEnvHttpGET("grant");

  $grantList = array(
    0 => $mlText[4],
    1 => $mlText[3],
  );
  $strSelectGrant = LibHtml::getSelectList("grant", $grantList, $grant);

  if ($contentImport = $contentImportUtils->selectById($contentImportId)) {
    $domainName = $contentImport->getDomainName();
  }

}

$panelUtils->setHeader($mlText[0], "$gContentImportUrl/importers/admin.php");
$help = $popupUtils->getHelpPopup($mlText[1], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
if (!$websiteUtils->isCurrentWebsiteOption('OPTION_ELEARNING_EXPORT')) {
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbc"));
} else {
  $label = $strSelectGrant . ' ' . $mlText[2];
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $domainName);
}
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('contentImportId', $contentImportId);
$panelUtils->addHiddenField('tokenName', $tokenName);
$panelUtils->addHiddenField('tokenValue', $tokenValue);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
