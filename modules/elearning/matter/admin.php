<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gElearningUrl/course/admin.php");
$help = $popupUtils->getHelpPopup($mlText[7], 300, 200);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gElearningUrl/matter/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$elearningMatters = $elearningMatterUtils->selectAll();

$panelUtils->openList();
foreach ($elearningMatters as $elearningMatter) {
  $elearningMatterId = $elearningMatter->getId();
  $name = $elearningMatter->getName();
  $description = $elearningMatter->getDescription();

  $strCommand = "<a href='$gElearningUrl/matter/edit.php?elearningMatterId=$elearningMatterId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gElearningUrl/matter/delete.php?elearningMatterId=$elearningMatterId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $description, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
