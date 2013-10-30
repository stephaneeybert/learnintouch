<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);


$panelUtils->setHeader($mlText[0], "$gNewsUrl/newsPaper/admin.php");
$help = $popupUtils->getHelpPopup($mlText[9], 300, 500);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gNewsUrl/newsPublication/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nb"), $panelUtils->addCell($mlText[6], "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$newsPublications = $newsPublicationUtils->selectAll();

$panelUtils->openList();
foreach ($newsPublications as $newsPublication) {
  $newsPublicationId = $newsPublication->getId();
  $name = $newsPublication->getName();
  $description = $newsPublication->getDescription();

  $strCommand = ''
    . "\n<a href='$gNewsUrl/newsPublication/edit.php?newsPublicationId=$newsPublicationId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . "\n <a href='$gNewsUrl/newsPublication/delete.php?newsPublicationId=$newsPublicationId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $description, $panelUtils->addCell("$strCommand", "r"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("news_newspublication_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
