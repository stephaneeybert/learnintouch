<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $smsNumberId = LibEnv::getEnvHttpPOST("smsNumberId");

  $smsNumberUtils->deleteSmsNumber($smsNumberId);

  $str = LibHtml::urlRedirect("$gSmsUrl/number/admin.php");
  printContent($str);
  return;

  } else {

  $smsNumberId = LibEnv::getEnvHttpGET("smsNumberId");

  $mobilePhone = '';
  if ($smsNumber = $smsNumberUtils->selectById($smsNumberId)) {
    $mobilePhone = $smsNumber->getMobilePhone();
    }

  $panelUtils->setHeader($mlText[0], "$gSmsUrl/number/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $mobilePhone);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('smsNumberId', $smsNumberId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
