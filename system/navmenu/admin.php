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

$navmenuId = LibEnv::getEnvHttpGET("navmenuId");

if (!$navmenuId) {
  $navmenuId = LibSession::getSessionValue(NAVMENU_SESSION_CURRENT);
} else {
  LibSession::putSessionValue(NAVMENU_SESSION_CURRENT, $navmenuId);
}

if (!$navmenuId) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$panelUtils->setHeader($mlText[0]);
$help = $popupUtils->getHelpPopup($mlText[7], 300, 500);
$panelUtils->setHelp($help);

$strCommand = '';
if ($navmenuUtils->countAvailableLanguages($navmenuId) > 0) {
  $strCommand .= " <a href='$gNavmenuUrl/navmenuLanguage/edit.php?navmenuId=$navmenuId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
}
$strCommand .= " <a href='$gNavmenuUrl/edit.php?navmenuId=$navmenuId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>";

$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[10]", "nb"), '', $panelUtils->addCell($strCommand, "nr"));


$panelUtils->openList();
if ($navmenuLanguages = $navmenuLanguageUtils->selectByNavmenuId($navmenuId)) {
  foreach ($navmenuLanguages as $navmenuLanguage) {
    $navmenuLanguageId = $navmenuLanguage->getId();
    $language = $navmenuLanguage->getLanguage();
    $rootNavmenuItemId = $navmenuLanguage->getNavmenuItemId();

    $languageName = $languageUtils->getLanguageName($language);

    if ($languageName) {
      $strLanguageAdd = $mlText[15] . " " . $languageName;
    } else {
      $strLanguageAdd = '';
    }

    if ($languageName) {
      $strLanguage = $mlText[17] . " " . $languageName;
    } else {
      $strLanguage = $mlText[13];
    }

    $strCommand = "<a href='$gNavmenuUrl/navmenuItem/edit.php?parentNavmenuItemId=$rootNavmenuItemId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[12] $strLanguageAdd'></a>";

    if ($navmenuUtils->countAvailableLanguages($navmenuId) > 0) {
      $strCommand .= " <a href='$gNavmenuUrl/navmenuLanguage/edit.php?navmenuLanguageId=$navmenuLanguageId' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[9]'></a>";
    }

    $strCommand .= " <a href='$gNavmenuUrl/navmenuLanguage/delete.php?navmenuLanguageId=$navmenuLanguageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

    $panelUtils->addLine($panelUtils->addCell($strLanguage, "l"), $panelUtils->addCell($strCommand, "nr"));

    if ($listItems = $navmenuItemUtils->listItems($rootNavmenuItemId)) {
      foreach ($listItems as $listItem) {
        $navmenuItemId = $listItem[0];
        $level = $listItem[1];

        if (!$navmenuItem = $navmenuItemUtils->selectById($navmenuItemId)) {
          continue;
        }

        $navmenuItemName = $navmenuItem->getName();
        $navmenuItemHide = $navmenuItem->getHide();

        if ($navmenuItemName == 'NAVMENU_SEPARATOR') {
          $navmenuItemName = NAVMENU_SEPARATOR;
        }

        $imageUrl = $navmenuItemUtils->imageUrl;
        $imagePath = $navmenuItemUtils->imagePath;
        $image = $navmenuItem->getImage();
        $imageOver = $navmenuItem->getImageOver();
        if ($image && is_file($imagePath . $image)) {
          if ($imageOver && is_file($imagePath . $imageOver)) {
            $strOnMouseOver = "onMouseOver=\"src='$imageUrl/$imageOver'\" onMouseOut=\"src='$imageUrl/$image'\"";
          } else {
            $strOnMouseOver = '';
          }

          $strImage = "<img border='0' src='$imageUrl/$image' $strOnMouseOver title=''>";
        } else {
          $strImage = '';
        }

        $strName = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $level)
          . " " . $strImage
          . " " . $navmenuItemName;

        if ($navmenuItemHide) {
          $strHide = "<img border='0' src='$gCommonImagesUrl/$gImageHidden' title=''>";
        } else {
          $strHide = '';
        }

        $strSwap = "<a href='$gNavmenuUrl/navmenuItem/swapup.php?navmenuItemId=$navmenuItemId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[31]'></a>"
          . " <a href='$gNavmenuUrl/navmenuItem/swapdown.php?navmenuItemId=$navmenuItemId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[30]'></a>";

        $strCommand = "<a href='$gNavmenuUrl/navmenuItem/edit.php?parentNavmenuItemId=$navmenuItemId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[12]'></a>"
          . " <a href='$gNavmenuUrl/navmenuItem/addSeparator.php?parentNavmenuItemId=$navmenuItemId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[16]'></a>";
        $strCommand .= " <a href='$gNavmenuUrl/navmenuItem/edit.php?navmenuItemId=$navmenuItemId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[22]'></a>"
          . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[8]'>", "$gNavmenuUrl/navmenuItem/image.php?navmenuItemId=$navmenuItemId", 600, 600)
          . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[19]'>", "$gNavmenuUrl/navmenuItem/imageOver.php?navmenuItemId=$navmenuItemId", 600, 600)
          . " <a href='$gNavmenuUrl/navmenuItem/delete.php?navmenuItemId=$navmenuItemId' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[23]'></a>";

        $panelUtils->addLine('', $panelUtils->addCell("$strSwap $strName", "n"), '', $panelUtils->addCell($strCommand, "nr"));
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

$strRememberScroll = LibJavaScript::rememberScroll("navmenu_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
