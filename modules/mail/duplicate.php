<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $mailId = LibEnv::getEnvHttpPOST("mailId");
  $subject = LibEnv::getEnvHttpPOST("subject");

  $systemDateTime = $clockUtils->getSystemDateTime();

  // Duplicate the mail
  if ($mail = $mailUtils->selectById($mailId)) {
    $mail->setSubject($subject);
    $mail->setBody($mail->getBody());
    $mail->setDescription($mail->getDescription());
    $mail->setTextFormat($mail->getTextFormat());
    $mail->setCreationDate($systemDateTime);
    $mail->setAdminId($mail->getAdminId());
    $mailUtils->insert($mail);
    }

  $str = LibHtml::urlRedirect("$gMailUrl/admin.php");
  printContent($str);
  return;

  } else {

  $mailId = LibEnv::getEnvHttpGET("mailId");

  $subject = '';
  $description = '';
  if ($mailId) {
    if ($mail = $mailUtils->selectById($mailId)) {
      $subject = $mail->getSubject();
      // The subject string must be cleaned up
      $randomNumber = LibUtils::generateUniqueId();
      $subject = LibString::cleanString($subject) . MAIL_DUPLICATA . '_' . $randomNumber;
      $description = $mail->getDescription();
      }
    }

  $panelUtils->setHeader($mlText[0], "$gMailUrl/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='subject' value='$subject' size='30' maxlength='255'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('mailId', $mailId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
