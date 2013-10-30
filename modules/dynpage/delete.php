<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $dynpageId = LibEnv::getEnvHttpPOST("dynpageId");

  $dynpageUtils->putInGarbage($dynpageId);

  $str = LibHtml::urlRedirect("$gDynpageUrl/admin.php");
  printContent($str);
  return;

} else {

  $dynpageId = LibEnv::getEnvHttpGET("dynpageId");

  if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
    $name = $dynpage->getName();
  }

  $panelUtils->setHeader($mlText[0], "$gDynpageUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('dynpageId', $dynpageId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
