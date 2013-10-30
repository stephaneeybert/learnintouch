<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $mailListId = LibEnv::getEnvHttpPOST("mailListId");

  // Delete
  $mailListUtils->deleteMailList($mailListId);

  $str = LibHtml::urlRedirect("$gMailUrl/list/admin.php");
  printContent($str);
  return;

  } else {

  $mailListId = LibEnv::getEnvHttpGET("mailListId");

  if ($mailList = $mailListUtils->selectById($mailListId)) {
    $name = $mailList->getName();
    }

  $panelUtils->setHeader($mlText[0], "$gMailUrl/list/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('mailListId', $mailListId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
