<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CLOCK);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $timeDifference = LibEnv::getEnvHttpPOST("timeDifference");

  $timeDifference = LibString::cleanString($timeDifference);

  $clockUtils->setTimeDifference($timeDifference);
}

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
$panelUtils->setHelp($help);
$strCommand = " <a href='$gClockUrl/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[6]'></a>";
$panelUtils->addLine('', $panelUtils->addCell($strCommand, "nr"));
$systemTime = $clockUtils->getSystemTime();
$timeDifferenceIndex = $clockUtils->getTimeDifferenceIndex();
$localTime = $clockUtils->getLocalTime();
$localDate = $clockUtils->renderDate();

$strSelectDifference = LibHtml::getSelectList("timeDifference", $clockUtils->timeDifferences, $timeDifferenceIndex, true);

$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $systemTime);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "br"), $strSelectDifference);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[4], "br"), $localTime);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "br"), $localDate);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
