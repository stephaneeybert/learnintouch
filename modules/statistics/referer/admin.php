<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_STATISTICS);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gStatisticsUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[10], 300, 400);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gStatisticsUrl/referer/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[4]", "nb"), $panelUtils->addCell("$mlText[8]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$statisticsReferers = $statisticsRefererUtils->selectAll();

$panelUtils->openList();
foreach ($statisticsReferers as $statisticsReferer) {
  $statisticsRefererId = $statisticsReferer->getId();
  $name = $statisticsReferer->getName();
  $description = $statisticsReferer->getDescription();
  $url = $statisticsReferer->getUrl();

  $strCommand = "<a href='$gStatisticsUrl/referer/edit.php?statisticsRefererId=$statisticsRefererId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gStatisticsUrl/referer/delete.php?statisticsRefererId=$statisticsRefererId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $url, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
