<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$chosen_category = LibEnv::getEnvHttpPOST("chosen_category");

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[10], 300, 200);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gClientUrl/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>"
. " <a href='$gClientUrl/preference.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[20]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[4]", "nb"), $panelUtils->addCell("$mlText[9]", "nb"), $panelUtils->addCell("$mlText[8]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$clients = $clientUtils->selectAll();

$panelUtils->openList();
foreach ($clients as $client) {
  $clientId = $client->getId();

  $name = $client->getName();
  $description = $client->getDescription();
  $image = $client->getImage();
  $url = $client->getUrl();

  $strImage = "<img src='" . $clientUtils->imageUrl . '/' . $image . "' border='0' href='' title='$image'>";

  $strCommand = "<a href='$gClientUrl/edit.php?clientId=$clientId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[7]'>", "$gClientUrl/image.php?clientId=$clientId", 600, 600)
    . " <a href='$gClientUrl/delete.php?clientId=$clientId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $strImage, $url, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("client_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
