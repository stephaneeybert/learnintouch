<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gSmsUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[4], 300, 400);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gSmsUrl/list/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$smsLists = $smsListUtils->selectAll();

$panelUtils->openList();
foreach ($smsLists as $smsList) {
  $smsListId = $smsList->getId();
  $name = $smsList->getName();

  $strCommand = ''
    . " <a href='$gSmsUrl/send.php?smsListId=$smsListId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageSms' title='$mlText[6]'></a>"
    . " <a href='$gSmsUrl/list/edit.php?smsListId=$smsListId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gSmsUrl/list/compose.php?smsListId=$smsListId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDesign' title='$mlText[5]'></a>"
    . " <a href='$gSmsUrl/list/delete.php?smsListId=$smsListId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("sms_list_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
