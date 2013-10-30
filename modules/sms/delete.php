<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $smsId = LibEnv::getEnvHttpPOST("smsId");

  $smsUtils->deleteSms($smsId);

  $str = LibHtml::urlRedirect("$gSmsUrl/admin.php");
  printContent($str);
  return;

} else {

  $smsId = LibEnv::getEnvHttpGET("smsId");

  if ($sms = $smsUtils->selectById($smsId)) {
    $body = $smsUtils->renderBody($sms);
  }

  $panelUtils->setHeader($mlText[0], "$gSmsUrl/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $body);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('smsId', $smsId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
