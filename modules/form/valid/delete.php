<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FORM);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $formValidId = LibEnv::getEnvHttpPOST("formValidId");

  // Delete
  $formValidUtils->deleteFormValidator($formValidId);

  $str = LibHtml::urlRedirect("$gFormUrl/valid/admin.php");
  printContent($str);
  exit;

  } else {

  $formValidId = LibEnv::getEnvHttpGET("formValidId");

  $currentLanguageCode = $languageUtils->getCurrentAdminLanguageCode();

  $type = '';
  $message = '';
  $boundary = '';
  if ($formValid = $formValidUtils->selectById($formValidId)) {
    $type = $formValid->getType();
    $message = $languageUtils->getTextForLanguage($formValid->getMessage(), $currentLanguageCode);
    $boundary = $formValid->getBoundary();
    }

  $typeName = $gFormValidTypes[$type];

  $panelUtils->setHeader($mlText[0], "$gFormUrl/valid/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $typeName);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $message);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $boundary);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('formValidId', $formValidId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
