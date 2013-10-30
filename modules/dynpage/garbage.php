<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$mlText = $languageUtils->getMlText(__FILE__);


$panelUtils->setHeader($mlText[0], "$gDynpageUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[9], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->addLine();

$strCommand = " <a href='$gDynpageUrl/emptyGarbage.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[1]'></a>";

$panelUtils->addLine($panelUtils->addCell("$mlText[7]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

$dynpages = $dynpageUtils->selectGarbage();
foreach ($dynpages as $dynpage) {
  $dynpageId = $dynpage->getId();

  $name = $dynpage->getName();
  $description = $dynpage->getDescription();

  $strCommand = "<a href='$gDynpageUrl/restore.php?dynpageId=$dynpageId' $gJSNoStatus>"
               . "<img border='0' src='$gCommonImagesUrl/$gImageRestore' title='$mlText[11]'></a>";

  $panelUtils->addLine($panelUtils->addCell($name, "n"), $description, $panelUtils->addCell("$strCommand", "nr"));
  }

$str = $panelUtils->render();

printAdminPage($str);

?>
