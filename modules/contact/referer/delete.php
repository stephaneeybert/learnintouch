<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $contactRefererId = LibEnv::getEnvHttpPOST("contactRefererId");

  // Delete
  $contactRefererUtils->deleteReferer($contactRefererId);

  $str = LibHtml::urlRedirect("$gContactUrl/referer/admin.php");
  printContent($str);
  return;

  } else {

  $contactRefererId = LibEnv::getEnvHttpGET("contactRefererId");

  if ($contactReferer = $contactRefererUtils->selectById($contactRefererId)) {
    $description = $contactReferer->getDescription();
    }

  $panelUtils->setHeader($mlText[0], "$gContactUrl/referer/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('contactRefererId', $contactRefererId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
