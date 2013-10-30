<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FORM);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $formId = LibEnv::getEnvHttpPOST("formId");

  // Delete
  $formUtils->deleteForm($formId);

  $str = LibHtml::urlRedirect("$gFormUrl/admin.php");
  printContent($str);
  exit;

  } else {

  $formId = LibEnv::getEnvHttpGET("formId");

  $name = '';
  $description = '';
  $email = '';
  if ($form = $formUtils->selectById($formId)) {
    $name = $form->getName();
    $description = $form->getDescription();
    $email = $form->getEmail();
    }

  $panelUtils->setHeader($mlText[0], "$gFormUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $email);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('formId', $formId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
