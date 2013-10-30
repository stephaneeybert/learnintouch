<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$mailId = LibEnv::getEnvHttpGET("mailId");

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if (!$mailId) {
  $mailId = LibSession::getSessionValue(MAIL_SESSION_MAIL);
} else {
  LibSession::putSessionValue(MAIL_SESSION_MAIL, $mailId);
}

if ($formSubmitted) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$subject = '';
$description = '';
if ($mail = $mailUtils->selectById($mailId)) {
  $subject = $mailUtils->renderSubject($mail);
  $description = $mail->getDescription();
}

$strAttachment = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[3]'> $mlText[3]", "$gMailUrl/attachment/file.php?mailId=$mailId", 600, 600);
$attachedFiles = $mailUtils->getExistingAttachedFiles($mailId);
$strAttachedFiles = '';
if (is_array($attachedFiles)) {
  foreach ($attachedFiles as $attachedFile) {
    $strAttachedFiles .= "$attachedFile " . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageFalse' title='$mlText[4]'><br>", "$gMailUrl/attachment/delete.php?mailId=$mailId&filename=$attachedFile", 600, 600);
  }
}

$panelUtils->setHeader($mlText[0]);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $subject);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $description);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($strAttachment, "nr"), $strAttachedFiles);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('mailId', $mailId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
