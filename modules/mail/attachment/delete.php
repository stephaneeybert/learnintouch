<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $mailId = LibEnv::getEnvHttpPOST("mailId");
  $filename = LibEnv::getEnvHttpPOST("filename");

  // Delete
  $mailUtils->removeAttachment($mailId, $filename);
  $mailUtils->deleteAttachedFile($filename);

  $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
  printContent($str);
  return;

  } else {

  $mailId = LibEnv::getEnvHttpGET("mailId");
  $filename = LibEnv::getEnvHttpGET("filename");

  $panelUtils->setHeader($mlText[0]);
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $filename);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('filename', $filename);
  $panelUtils->addHiddenField('mailId', $mailId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
