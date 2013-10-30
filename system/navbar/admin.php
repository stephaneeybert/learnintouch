<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$closePopupWindow = LibEnv::getEnvHttpPOST("closePopupWindow");
if ($closePopupWindow) {
  $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$mlText = $languageUtils->getMlText(__FILE__);

$imageUrl = $navbarItemUtils->imageUrl;
$imagePath = $navbarItemUtils->imagePath;

$navbarId = LibEnv::getEnvHttpGET("navbarId");

if (!$navbarId) {
  $navbarId = LibSession::getSessionValue(NAVBAR_SESSION_CURRENT);
} else {
  LibSession::putSessionValue(NAVBAR_SESSION_CURRENT, $navbarId);
}

if (!$navbarId) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$panelUtils->setHeader($mlText[0]);
$help = $popupUtils->getHelpPopup($mlText[7], 300, 400);
$panelUtils->setHelp($help);

$strCommand = '';
if ($navbarUtils->countAvailableLanguages($navbarId) > 0) {
  $strCommand = "<a href='$gNavbarUrl/navbarLanguage/edit.php?navbarId=$navbarId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
}
$strCommand .= " <a href='$gNavbarUrl/edit.php?navbarId=$navbarId' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>";
if ($navbarUtils->countAvailableLanguages($navbarId) > 0) {
}

$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[10]", "nb"), '', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

$panelUtils->openList();
if ($navbarLanguages = $navbarLanguageUtils->selectByNavbarId($navbarId)) {
  foreach ($navbarLanguages as $navbarLanguage) {
    $navbarLanguageId = $navbarLanguage->getId();
    $language = $navbarLanguage->getLanguage();

    $languageName = $languageUtils->getLanguageName($language);

    if ($languageName) {
      $strLanguageAdd = $mlText[15] . " " . $languageName;
    } else {
      $strLanguageAdd = '';
    }

    if ($languageName) {
      $strLanguage = $mlText[17] . " " . $languageName;
    } else {
      $strLanguage = $mlText[16];
    }

    $strCommand = "<a href='$gNavbarUrl/navbarItem/edit.php?navbarLanguageId=$navbarLanguageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[12] $strLanguageAdd'></a>";

    if ($navbarUtils->countAvailableLanguages($navbarId) > 0) {
      $strCommand .= " <a href='$gNavbarUrl/navbarLanguage/edit.php?navbarLanguageId=$navbarLanguageId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[9]'></a>";
    }
    $strCommand .= " <a href='$gNavbarUrl/navbarLanguage/delete.php?navbarLanguageId=$navbarLanguageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

    $panelUtils->addLine($panelUtils->addCell($strLanguage, "l"), $panelUtils->addCell($strCommand, "nr"));

    // Display the items
    if ($navbarItems = $navbarItemUtils->selectByNavbarLanguageId($navbarLanguageId)) {
      foreach ($navbarItems as $navbarItem) {
        $navbarItemId = $navbarItem->getId();
        $navbarItemName = $navbarItem->getName();
        $navbarItemDescription = $navbarItem->getDescription();
        $navbarItemImage = $navbarItem->getImage();
        $navbarItemImageOver = $navbarItem->getImageOver();
        $navbarItemHide = $navbarItem->getHide();

        if ($navbarItemDescription) {
          $title = $navbarItemDescription;
        } else {
          $title = $navbarItemName;
        }

        if ($navbarItemImage && is_file($imagePath . $navbarItemImage)) {
          if ($navbarItemImageOver && is_file($imagePath . $navbarItemImageOver)) {
            $strOnMouseOver = "onMouseOver=\"src='$imageUrl/$navbarItemImageOver'\" onMouseOut=\"src='$imageUrl/$navbarItemImage'\"";
          } else {
            $strOnMouseOver = '';
          }

          $anchor = "<img src='$imageUrl/$navbarItemImage' $strOnMouseOver border='0' title='$title'> $navbarItemName";
        } else {
          $anchor = $navbarItemName;
        }

        if ($navbarItemHide) {
          $strHide = "<img border='0' src='$gCommonImagesUrl/$gImageFalse' title=''>";
        } else {
          $strHide = '';
        }

        $strSwap = "<a href='$gNavbarUrl/navbarItem/swapup.php?navbarItemId=$navbarItemId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[31]'></a>"
          . " <a href='$gNavbarUrl/navbarItem/swapdown.php?navbarItemId=$navbarItemId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[30]'></a>";

        $strCommand = "<a href='$gNavbarUrl/navbarItem/edit.php?navbarItemId=$navbarItemId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[22]'></a>"
          . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[8]'>", "$gNavbarUrl/navbarItem/image.php?navbarItemId=$navbarItemId", 600, 600)
          . " "
          . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[13]'>", "$gNavbarUrl/navbarItem/imageOver.php?navbarItemId=$navbarItemId", 600, 600)
          . " <a href='$gNavbarUrl/navbarItem/delete.php?navbarItemId=$navbarItemId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[23]'></a>";

        $panelUtils->addLine('', $panelUtils->addCell("$strSwap $anchor", "n"), "$strHide", $panelUtils->addCell($strCommand, "nr"));
      }
    }
  }
}
$panelUtils->closeList();

$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine('', '', "<input type='image' border='0' src='$gCommonImagesUrl/$gImageOk' title='$mlText[14]'>", '');
$panelUtils->addHiddenField('closePopupWindow', 1);
$panelUtils->closeForm();

$strRememberScroll = LibJavaScript::rememberScroll("navbar_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
