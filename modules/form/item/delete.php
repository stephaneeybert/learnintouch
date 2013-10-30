<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FORM);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $formItemId = LibEnv::getEnvHttpPOST("formItemId");

  // Delete
  $formItemUtils->deleteFormItem($formItemId);

  $str = LibHtml::urlRedirect("$gFormUrl/item/admin.php");
  printContent($str);
  exit;

  } else {

  $formItemId = LibEnv::getEnvHttpGET("formItemId");

  $type = '';
  $name = '';
  $email = '';
  if ($form = $formItemUtils->selectById($formItemId)) {
    $type = $form->getType();
    $name = $form->getName();
    $help = $form->getHelp();
    }

  $typeName = $gFormItemTypes[$type];

  $panelUtils->setHeader($mlText[0], "$gFormUrl/item/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $typeName);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $help);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('formItemId', $formItemId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
