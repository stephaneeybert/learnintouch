<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SHOP);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gShopUrl/payment/banks.php");
$help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
$panelUtils->setHelp($help);
$panelUtils->addLine($panelUtils->addCell("$mlText[3]", "nb"), $panelUtils->addCell("$mlText[4]", "nb"), '');
$panelUtils->addLine();

$languages = $languageUtils->getActiveLanguages();
foreach ($languages as $language) {
  $languageCode = $language->getCode();
  $languageId = $language->getId();
  $strImage = $languageUtils->renderImage($languageId);

  $pageName = '';
  if ($url = $shopOrderUtils->getComputerBankDetailsPage($languageCode)) {
    if ($url) {
      $pageName = $templateUtils->getPageName($url);
    }
  }

  $strCommand = ''
    . " <a href='$gShopUrl/payment/transfert/bank_edit.php?languageCode=$languageCode' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>";

  $strComputerImage = "<img border='0' src='$gCommonImagesUrl/$gImageComputer' title='$mlText[5]'>";

  $panelUtils->addLine($strImage, "$strComputerImage $pageName", $panelUtils->addCell($strCommand, "nr"));

  if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_PHONE_MODEL)) {
    $pageName = '';
    if ($url = $shopOrderUtils->getPhoneBankDetailsPage($languageCode)) {
      if ($url) {
        $pageName = $templateUtils->getPageName($url);
      }
    }

    $strCommand = ''
      . " <a href='$gShopUrl/payment/transfert/bank_edit.php?languageCode=$languageCode&amp;isPhone=1' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>";

    $strPhoneImage = "<img border='0' src='$gCommonImagesUrl/$gImagePda' title='$mlText[6]'>";

    $panelUtils->addLine($strImage, "$strPhoneImage $pageName", $panelUtils->addCell($strCommand, "nr"));
  }
}

$str = $panelUtils->render();

printAdminPage($str);

?>
