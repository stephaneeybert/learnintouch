<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $smsListId = LibEnv::getEnvHttpPOST("smsListId");

  $smsListUtils->deleteSmsList($smsListId);

  $str = LibHtml::urlRedirect("$gSmsUrl/list/admin.php");
  printContent($str);
  return;

  } else {

  $smsListId = LibEnv::getEnvHttpGET("smsListId");

  if ($smsList = $smsListUtils->selectById($smsListId)) {
    $name = $smsList->getName();
    }

  $panelUtils->setHeader($mlText[0], "$gSmsUrl/list/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('smsListId', $smsListId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
