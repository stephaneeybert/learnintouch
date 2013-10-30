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

$rssFeedId = LibEnv::getEnvHttpGET("rssFeedId");

if (!$rssFeedId) {
  $rssFeedId = LibSession::getSessionValue(RSS_FEED_SESSION_CURRENT);
} else {
  LibSession::putSessionValue(RSS_FEED_SESSION_CURRENT, $rssFeedId);
}

if (!$rssFeedId) {
  $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$panelUtils->setHeader($mlText[0]);
$help = $popupUtils->getHelpPopup($mlText[7], 300, 400);
$panelUtils->setHelp($help);

if ($languageUtils->countActiveLanguages() > 1) {
  $strCommand = "<a href='$gRssUrl/edit.php?rssFeedId=$rssFeedId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
} else {
  $strCommand = '';
}

$panelUtils->addLine($panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($mlText[2], "nb"), $panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

if ($rssFeedLanguages = $rssFeedLanguageUtils->selectByRssFeedId($rssFeedId)) {
  foreach ($rssFeedLanguages as $rssFeedLanguage) {
    $rssFeedLanguageId = $rssFeedLanguage->getId();
    $url = $rssFeedLanguage->getUrl();
    $title = $rssFeedLanguage->getTitle();
    $language = $rssFeedLanguage->getLanguage();

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

    $strCommand = "<a href='$gRssUrl/edit.php?rssFeedLanguageId=$rssFeedLanguageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[22] $strLanguageAdd'></a>"
      . " <a href='$gRssUrl/delete.php?rssFeedLanguageId=$rssFeedLanguageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3] $strLanguageAdd'></a>";

    $panelUtils->addLine($url, $title, $strLanguage, $panelUtils->addCell($strCommand, "nbr"));
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
