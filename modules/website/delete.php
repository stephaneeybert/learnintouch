<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$adminUtils->checkForStaffLogin();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $websiteId = LibEnv::getEnvHttpPOST("websiteId");

  $websiteUtils->deleteAccount($websiteId);

  $str = LibHtml::urlRedirect("$gWebsiteUrl/admin.php");
  printContent($str);
  return;

} else {

  $websiteId = LibEnv::getEnvHttpGET("websiteId");

  $firstname = '';
  $lastname = '';
  $domainName = '';
  if ($website = $websiteUtils->selectById($websiteId)) {
    $firstname = $website->getFirstname();
    $lastname = $website->getLastname();
    $name = $website->getName();
    $domainName = $website->getDomainName();
  }

  $panelUtils->setHeader($mlText[0], "$gWebsiteUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "$name $firstname $lastname");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $domainName);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('websiteId', $websiteId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
