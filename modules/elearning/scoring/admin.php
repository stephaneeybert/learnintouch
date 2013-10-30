<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$help = $popupUtils->getHelpPopup($mlText[7], 300, 300);
$panelUtils->setHelp($help);
$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");
$strCommand = "<a href='$gElearningUrl/scoring/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$elearningScorings = $elearningScoringUtils->selectAll();

$panelUtils->openList();
foreach ($elearningScorings as $scoring) {
  $elearningScoringId = $scoring->getId();
  $name = $scoring->getName();
  $description = $scoring->getDescription();

  $strCommand =
    " <a href='$gElearningUrl/scoring/edit.php?elearningScoringId=$elearningScoringId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gElearningUrl/scoring/range/admin.php?elearningScoringId=$elearningScoringId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageList' title='$mlText[4]'></a>"
    . " <a href='$gElearningUrl/scoring/delete.php?elearningScoringId=$elearningScoringId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($name, $description, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
