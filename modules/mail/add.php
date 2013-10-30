<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $subject = LibEnv::getEnvHttpPOST("subject");
  $body = LibEnv::getEnvHttpPOST("body");

  $adminId = $adminUtils->getLoggedAdminId();

  $subject = LibString::cleanString($subject);

  // The subject is required
  if (!$subject) {
    array_push($warnings, $mlText[8]);
  }

  $systemDateTime = $clockUtils->getSystemDateTime();

  if (count($warnings) == 0) {

    $mail = new Mail();
    $mail->setSubject($subject);
    $mail->setBody($body);
    $mail->setAdminId($adminId);
    $mail->setCreationDate($systemDateTime);
    $mailUtils->insert($mail);
    $mailId = $mailUtils->getLastInsertId();

    $str = LibHtml::urlRedirect("$gMailUrl/edit_content.php?mailId=$mailId");
    printContent($str);
    return;

  }

} else {

  $subject = '';
  $body = '';

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gMailUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='subject' value='$subject' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
