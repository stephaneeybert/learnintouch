<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LANGUAGE);

$mlText = $languageUtils->getMlText(__FILE__);

if ($adminUtils->isStaff()) {
  $strCreateCommand = "<a href='$gLanguageUrl/edit.php' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
} else {
  $strCreateCommand = '';
}

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[6], 300, 300);
$panelUtils->setHelp($help);
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nb"), $panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($mlText[9], "nb"), '');
$panelUtils->addLine();

$languages = $languageUtils->selectAll();

$panelUtils->openList();
foreach ($languages as $language) {
  $languageId = $language->getId();

  $code = $language->getCode();
  $name = $language->getName();
  $strImage = $languageUtils->renderImage($languageId);

  if ($code == $languageUtils->getDefaultLanguageCode()) {
    $strDefault = "<img src='$gCommonImagesUrl/$gImageCurrent' border='0' title=''>";
  } else {
    $strDefault = '';
  }

  if ($languageUtils->isActiveLanguage($code)) {
    $mlTextActive = $mlText[11];
    $imageActive = $gImageDeactivate;
    $activate = 0;
  } else {
    $mlTextActive = $mlText[10];
    $imageActive = $gImageActivate;
    $activate = 1;
  }

  if ($languageUtils->isActiveAdminLanguage($code)) {
    $mlTextActiveAdmin = $mlText[15];
    $imageActiveAdmin = $gImageDeactivate;
    $activateAdmin = 0;
  } else {
    $mlTextActiveAdmin = $mlText[14];
    $imageActiveAdmin = $gImageActivate;
    $activateAdmin = 1;
  }


  $adminLanguageCodes = array('en', 'fr');

  $strCommand = "<a href='$gLanguageUrl/activate.php?languageId=$languageId&activate=$activate' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$imageActive' title='$mlTextActive'></a>"
    . " <a href='$gLanguageUrl/default.php?languageId=$languageId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDefault' title='$mlText[7]'></a>";
  if (in_array($code, $adminLanguageCodes)) {
    $strCommand .= " <a href='$gLanguageUrl/activateAdmin.php?languageId=$languageId&activate=$activateAdmin' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$imageActiveAdmin' title='$mlTextActiveAdmin'></a>";
  } else {
    $strCommand .= " <img border='0' src='$gCommonImagesUrl/$gImageTransparent' title=''>";
  }
  if ($adminUtils->isStaff()) {
    $strCommand .= "<a href='$gLanguageUrl/edit.php?languageId=$languageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
      . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[12]'>", "$gLanguageUrl/image.php?languageId=$languageId", 600, 600)
      . " <a href='$gLanguageUrl/delete.php?languageId=$languageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";
  }

  $panelUtils->addLine($panelUtils->addCell($name, "n"), $panelUtils->addCell($code, "n"), $panelUtils->addCell($strImage, "nm"), $strDefault, $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
