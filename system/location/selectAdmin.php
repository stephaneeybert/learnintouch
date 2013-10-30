<?PHP

require_once("website.php");

require_once($gLocationPath . "selectController.php");

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0]);
$help = $popupUtils->getHelpPopup($mlText[1], 300, 100);
$panelUtils->setHelp($help);

require_once($gLocationPath . "selectList.php");

$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell($strSelectLocationCountry, "nb"));
$panelUtils->addHiddenField('region', $region);
$panelUtils->addHiddenField('state', $state);
$panelUtils->addHiddenField('zipCode', $zipCode);
$panelUtils->closeForm();

$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell("$mlText[3]", "nb"), $panelUtils->addCell($strSelectLocationRegion, "nb"));
$panelUtils->addHiddenField('country', $country);
$panelUtils->addHiddenField('state', $state);
$panelUtils->addHiddenField('zipCode', $zipCode);
$panelUtils->closeForm();

$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell("$mlText[4]", "nb"), $panelUtils->addCell($strSelectLocationState, "nb"));
$panelUtils->addHiddenField('country', $country);
$panelUtils->addHiddenField('region', $region);
$panelUtils->addHiddenField('zipCode', $zipCode);
$panelUtils->closeForm();

$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell($strSelectLocationZipCode, "nb"));
$panelUtils->addHiddenField('country', $country);
$panelUtils->addHiddenField('region', $region);
$panelUtils->addHiddenField('state', $state);
$panelUtils->closeForm();

$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('country', $country);
$panelUtils->addHiddenField('region', $region);
$panelUtils->addHiddenField('state', $state);
$panelUtils->addHiddenField('zipCode', $zipCode);
$panelUtils->closeForm();
$panelUtils->addLine();

$str = $panelUtils->render();

printAdminPage($str);

?>
