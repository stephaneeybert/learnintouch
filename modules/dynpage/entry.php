<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$panelUtils->setHeader($mlText[0], "$gDynpageUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
$panelUtils->setHelp($help);
$panelUtils->addLine($panelUtils->addCell("$mlText[3]", "nb"), $panelUtils->addCell("$mlText[4]", "nb"), '');
$panelUtils->addLine();

$templateUtils->deleteEntryPages();

$languages = $languageUtils->getActiveLanguages();

$panelUtils->openList();
foreach ($languages as $language) {
  $languageCode = $language->getCode();
  $languageId = $language->getId();
  $strImage = $languageUtils->renderImage($languageId);

  // The computer model entry page
  $pageName = '';
  if ($webpageId = $templateUtils->getComputerEntryPage($languageCode)) {
    if ($webpageId) {
      $pageName = $templateUtils->getPageName($webpageId);
    }
  }

  $strCommand = ''
    . " <a href='$gDynpageUrl/entryEdit.php?languageCode=$languageCode' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>";

  $strComputerImage = "<img border='0' src='$gCommonImagesUrl/$gImageComputer' title='$mlText[5]'>";

  $panelUtils->addLine($strImage, "$strComputerImage $pageName", $panelUtils->addCell($strCommand, "nr"));

  // The phone model entry page
  if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_PHONE_MODEL)) {
    $pageName = '';
    if ($webpageId = $templateUtils->getPhoneEntryPage($languageCode)) {
      if ($webpageId) {
        $pageName = $templateUtils->getPageName($webpageId);
      }
    }

    $strCommand = ''
      . " <a href='$gDynpageUrl/entryEdit.php?languageCode=$languageCode&amp;isPhone=1' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>";

    $strPhoneImage = "<img border='0' src='$gCommonImagesUrl/$gImagePda' title='$mlText[6]'>";

    $panelUtils->addLine($strImage, "$strPhoneImage $pageName", $panelUtils->addCell($strCommand, "nr"));
  }
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
