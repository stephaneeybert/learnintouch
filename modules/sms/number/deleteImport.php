<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $smsNumberUtils->deleteImported();

  $str = LibHtml::urlRedirect("$gSmsUrl/number/admin.php");
  printContent($str);
  return;

} else {

  $nbImported = $smsNumberUtils->countImported();

  if ($nbImported == 0) {
    array_push($warnings, $mlText[4]);
  }

  $strWarning = '';
  if (count($warnings) > 0) {
    foreach ($warnings as $warning) {
      $strWarning .= "<br>$warning";
    }
  }

  $panelUtils->setHeader($mlText[0], "$gSmsUrl/number/admin.php");
  $panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
  if ($nbImported > 0) {
    $panelUtils->openForm($PHP_SELF);
    $panelUtils->addLine($panelUtils->addCell("$mlText[2]", "br"), $nbImported);
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell("$mlText[1]", "br"), $panelUtils->getOk());
    $panelUtils->addHiddenField('formSubmitted', 1);
    $panelUtils->closeForm();
  }

  // Display the mobile phone numbers of the last import
  if ($smsNumbers = $smsNumberUtils->selectImported()) {
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell("$mlText[3]", "nb"));
    $panelUtils->addLine();
    foreach($smsNumbers as $smsNumber) {
      $smsNumberId = $smsNumber->getId();
      $mobilePhone = $smsNumber->getMobilePhone();
      $panelUtils->addLine($panelUtils->addCell($mobilePhone, "l"));
    }
  }

  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
