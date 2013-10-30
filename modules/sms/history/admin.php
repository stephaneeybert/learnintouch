<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gSmsUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[4], 300, 400);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gSmsUrl/history/deleteAll.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nb"), $panelUtils->addCell($mlText[9], "nb"), $panelUtils->addCell($mlText[3], "nb"), $panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($mlText[6], "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();


if ($smsHistories = $smsHistoryUtils->selectAll()) {
  $panelUtils->openList();
  foreach ($smsHistories as $smsHistory) {
    $smsHistoryId = $smsHistory->getId();
    $smsId = $smsHistory->getSmsId();
    $smsListId = $smsHistory->getSmsListId();
    $mobilePhone = $smsHistory->getMobilePhone();
    $adminId = $smsHistory->getAdminId();
    $sendDate = $smsHistory->getSendDate();
    $nbRecipients = $smsHistory->getNbRecipients();

    $strBody = '';
    if ($sms = $smsUtils->selectById($smsId)) {
      $body = $sms->getBody();
      $strBody = $popupUtils->getDialogPopup($body, "$gSmsUrl/preview.php?smsId=$smsId", 600, 600);
    }

    $recipient = '';
    if ($smsList = $smsListUtils->selectById($smsListId)) {
      $recipient = $smsList->getName();
    } else {
      $recipient = $mobilePhone;
    }

    $administrator = '';
    if ($admin = $adminUtils->selectById($adminId)) {
      $firstname = $admin->getFirstname();
      $lastname = $admin->getLastname();
      $administrator = "$firstname $lastname";
    }

    $panelUtils->addLine($strBody, $panelUtils->addCell($sendDate, "n"), $recipient, $administrator, $nbRecipients, '');
  }
  $panelUtils->closeList();
}

$strRememberScroll = LibJavaScript::rememberScroll("sms_history_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
