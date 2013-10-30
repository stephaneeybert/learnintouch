<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gContactUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[9], 300, 300);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gContactUrl/referer/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[4]", "nb"), $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

$languages = $languageUtils->getActiveLanguages();

$contactRefereres = $contactRefererUtils->selectAll();

$panelUtils->openList();
foreach ($contactRefereres as $contactReferer) {
  $contactRefererId = $contactReferer->getId();
  $descriptions = $contactReferer->getDescription();

  $strDescriptions = '';
  foreach ($languages as $language) {
    $languageCode = $language->getCode();
    $description = $languageUtils->getTextForLanguage($descriptions, $languageCode);
    $languageFlag = $languageUtils->renderLanguageFlag($languageCode);
    $strDescriptions .= $languageFlag . ' ' . $description;
    }

  $strSwap = "<a href='$gContactUrl/referer/swapup.php?contactRefererId=$contactRefererId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[11]'></a>"
    . " <a href='$gContactUrl/referer/swapdown.php?contactRefererId=$contactRefererId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[10]'></a>";

  $strCommand = "<a href='$gContactUrl/referer/edit.php?contactRefererId=$contactRefererId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gContactUrl/referer/delete.php?contactRefererId=$contactRefererId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($strSwap . ' ' . $strDescriptions, $panelUtils->addCell($strCommand, "nr"));
  }
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
