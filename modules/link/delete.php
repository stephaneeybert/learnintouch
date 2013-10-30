<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LINK);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $linkId = LibEnv::getEnvHttpPOST("linkId");

  $linkUtils->deleteLink($linkId);

  $str = LibHtml::urlRedirect("$gLinkUrl/admin.php");
  printContent($str);
  return;

} else {

  $linkId = LibEnv::getEnvHttpGET("linkId");

  if ($link = $linkUtils->selectById($linkId)) {
    $name = $link->getName();
    $url = $link->getUrl();
    $description = $link->getDescription();
  }

  $panelUtils->setHeader($mlText[0], "$gLinkUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $url);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('linkId', $linkId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
