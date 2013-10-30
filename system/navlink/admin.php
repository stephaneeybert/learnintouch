<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

// Close the window
$closePopupWindow = LibEnv::getEnvHttpPOST("closePopupWindow");
if ($closePopupWindow) {
  $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$mlText = $languageUtils->getMlText(__FILE__);

$imageUrl = $navlinkItemUtils->imageUrl;
$imagePath = $navlinkItemUtils->imagePath;

$navlinkId = LibEnv::getEnvHttpGET("navlinkId");

if (!$navlinkId) {
  $navlinkId = LibSession::getSessionValue(NAVLINK_SESSION_CURRENT);
} else {
  LibSession::putSessionValue(NAVLINK_SESSION_CURRENT, $navlinkId);
}

if (!$navlinkId) {
  $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$panelUtils->setHeader($mlText[0]);
$help = $popupUtils->getHelpPopup($mlText[7], 300, 400);
$panelUtils->setHelp($help);

if ($languageUtils->countActiveLanguages() > 1) {
  $strCommand = "<a href='$gNavlinkUrl/addLanguage.php?navlinkId=$navlinkId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
} else {
  $strCommand = '';
}

$panelUtils->addLine($panelUtils->addCell($mlText[10], "nb"), $panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

if ($navlinkItems = $navlinkItemUtils->selectByNavlinkId($navlinkId)) {
  foreach ($navlinkItems as $navlinkItem) {
    $navlinkItemId = $navlinkItem->getId();
    $text = $navlinkItem->getText();
    $language = $navlinkItem->getLanguage();
    $image = $navlinkItem->getImage();
    $imageOver = $navlinkItem->getImageOver();
    $description = $navlinkItem->getDescription();

    if ($image && is_file($imagePath . $image)) {
      if ($imageOver && is_file($imagePath . $imageOver)) {
        $strOnMouseOver = "onMouseOver=\"src='$imageUrl/$imageOver'\" onMouseOut=\"src='$imageUrl/$image'\"";
      } else {
        $strOnMouseOver = '';
      }

      $strImage = "<img src='$imageUrl/$image' $strOnMouseOver border='0' title='$description'>";
    } else {
      $strImage = '';
    }

    $languageName = $languageUtils->getLanguageName($language);

    if ($languageName) {
      $strLanguageAdd = $mlText[15] . " " . $languageName;
    } else {
      $strLanguageAdd = '';
    }

    if ($languageName) {
      $strLanguage = ucwords($languageName);
    } else {
      $strLanguage = $mlText[13];
    }

    $strCommand = "<a href='$gNavlinkUrl/editItem.php?navlinkItemId=$navlinkItemId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[22] $strLanguageAdd'></a>"
      . " <a href='$gNavlinkUrl/deleteItem.php?navlinkItemId=$navlinkItemId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3] $strLanguageAdd'></a>";

    $panelUtils->addLine("$strImage $text", $strLanguage, $panelUtils->addCell($strCommand, "nbr"));
  }
}

$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine('', '', "<input type='image' border='0' src='$gCommonImagesUrl/$gImageOk' title='$mlText[14]'>", '');
$panelUtils->addHiddenField('closePopupWindow', 1);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
