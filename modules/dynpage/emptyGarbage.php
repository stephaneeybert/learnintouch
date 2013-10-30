<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$adminUtils->checkSuperAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  // Delete all pages from the garbage
  if ($dynpages = $dynpageUtils->selectGarbage()) {
    foreach ($dynpages as $dynpage) {
      $dynpageId = $dynpage->getId();
      $dynpageUtils->delete($dynpageId);
      }
    }

  $str = LibHtml::urlRedirect("$gDynpageUrl/garbage.php");
  printMessage($str);
  return;

  } else {

  $panelUtils->setHeader($mlText[0], "$gDynpageUrl/garbage.php");
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
