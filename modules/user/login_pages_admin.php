<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_USER);

$mlText = $languageUtils->getMlText(__FILE__);


$templateUtils->deleteEntryPages();

$panelUtils->setHeader($mlText[0], "$gUserUrl/admin.php");
$label = $popupUtils->getTipPopup($mlText[10], $mlText[1], 300, 300);
$panelUtils->addLine($panelUtils->addCell("$mlText[3]", "nb"), $panelUtils->addCell($label, "nb"), '');
$panelUtils->addLine();

$languages = $languageUtils->getActiveLanguages();
foreach ($languages as $language) {
  $languageCode = $language->getCode();
  $languageId = $language->getId();
  $strImage = $languageUtils->renderImage($languageId);

  // The computer model post login page
  $pageName = '';
  if ($url = $userUtils->getComputerPostLoginPage($languageCode)) {
    if ($url) {
      $pageName = $templateUtils->getPageName($url);
      }
    }

  $strCommand = ''
    . " <a href='$gUserUrl/postLoginEdit.php?languageCode=$languageCode' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>";

  $strComputerImage = "<img border='0' src='$gCommonImagesUrl/$gImageComputer' title='$mlText[5]'>";

  $panelUtils->addLine($strImage, $panelUtils->addCell("$strComputerImage $pageName", 'n'), $panelUtils->addCell($strCommand, "nr"));

  // The phone model post login page
  if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_PHONE_MODEL)) {
    $pageName = '';
    if ($url = $userUtils->getPhonePostLoginPage($languageCode)) {
      if ($url) {
        $pageName = $templateUtils->getPageName($url);
        }
      }

    $strCommand = ''
      . " <a href='$gUserUrl/postLoginEdit.php?languageCode=$languageCode&amp;isPhone=1' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>";

    $strPhoneImage = "<img border='0' src='$gCommonImagesUrl/$gImagePda' title='$mlText[6]'>";

    $panelUtils->addLine($strImage, $panelUtils->addCell("$strPhoneImage $pageName", 'n'), $panelUtils->addCell($strCommand, "nr"));
    }
  }

$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[7], $mlText[8], 300, 300);
$panelUtils->addLine($panelUtils->addCell("$mlText[3]", "nb"), $panelUtils->addCell($label, "nb"), '');
$panelUtils->addLine();

foreach ($languages as $language) {
  $languageCode = $language->getCode();
  $languageId = $language->getId();
  $strImage = $languageUtils->renderImage($languageId);

  // The computer model expired login page
  $pageName = '';
  if ($url = $userUtils->getComputerExpiredLoginPage($languageCode)) {
    if ($url) {
      $pageName = $templateUtils->getPageName($url);
      }
    }

  $strCommand = ''
    . " <a href='$gUserUrl/expiredLoginEdit.php?languageCode=$languageCode' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[9]'></a>";

  $strComputerImage = "<img border='0' src='$gCommonImagesUrl/$gImageComputer' title='$mlText[11]'>";

  $panelUtils->addLine($strImage, $panelUtils->addCell("$strComputerImage $pageName", 'n'), $panelUtils->addCell($strCommand, "nr"));

  // The phone model expired login page
  if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_PHONE_MODEL)) {
    $pageName = '';
    if ($url = $userUtils->getPhoneExpiredLoginPage($languageCode)) {
      if ($url) {
        $pageName = $templateUtils->getPageName($url);
        }
      }

    $strCommand = ''
      . " <a href='$gUserUrl/expiredLoginEdit.php?languageCode=$languageCode&amp;isPhone=1' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[9]'></a>";

    $strPhoneImage = "<img border='0' src='$gCommonImagesUrl/$gImagePda' title='$mlText[12]'>";

    $panelUtils->addLine($strImage, $panelUtils->addCell("$strPhoneImage $pageName", 'n'), $panelUtils->addCell($strCommand, "nr"));
    }
  }

$str = $panelUtils->render();

printAdminPage($str);

?>
