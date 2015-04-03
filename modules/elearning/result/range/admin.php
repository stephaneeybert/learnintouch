<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$help = $popupUtils->getHelpPopup($mlText[9], 300, 300);
$panelUtils->setHelp($help);
$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");

$strCommand = "<a href='$gElearningUrl/result/range/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$elearningResultRanges = $elearningResultRangeUtils->selectAll();

$panelUtils->openList();
foreach ($elearningResultRanges as $elearningResultRange) {
  $elearningResultRangeId = $elearningResultRange->getId();
  $upperRange = $elearningResultRange->getUpperRange();
  $grade = $elearningResultRange->getGrade();

  $strCommand = "<a href='$gElearningUrl/result/range/edit.php?elearningResultRangeId=$elearningResultRangeId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gElearningUrl/result/range/delete.php?elearningResultRangeId=$elearningResultRangeId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($upperRange, $grade, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
