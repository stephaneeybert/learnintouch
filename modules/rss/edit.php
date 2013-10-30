<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $rssFeedId = LibEnv::getEnvHttpPOST("rssFeedId");
  $language = LibEnv::getEnvHttpPOST("language");
  $url = LibEnv::getEnvHttpPOST("url");
  $title = LibEnv::getEnvHttpPOST("title");

  $language = LibString::cleanString($language);
  $url = LibString::cleanString($url);
  $title = LibString::cleanString($title);

  // Format the url
  // The url can be a web address or an email address
  if ($url && !LibEmail::validate($url)) {
    $url = LibUtils::formatUrl($url);
  }

  if ($rssFeedLanguage = $rssFeedLanguageUtils->selectByLanguageAndRssFeedId($language, $rssFeedId)) {
    $rssFeedLanguage->setUrl($url);
    $rssFeedLanguage->setTitle($title);
    $rssFeedLanguageUtils->update($rssFeedLanguage);
  } else {
    $rssFeedLanguage = new RssFeedLanguage();
    $rssFeedLanguage->setLanguage($language);
    $rssFeedLanguage->setUrl($url);
    $rssFeedLanguage->setTitle($title);
    $rssFeedLanguage->setRssFeedId($rssFeedId);
    $rssFeedLanguageUtils->insert($rssFeedLanguage);
  }

  $str = LibHtml::urlRedirect("$gRssUrl/admin.php");
  printContent($str);
  return;

}

$rssFeedId = LibEnv::getEnvHttpGET("rssFeedId");
if (!$rssFeedId) {
  $rssFeedId = LibEnv::getEnvHttpPOST("rssFeedId");
}

$rssFeedLanguageId = LibEnv::getEnvHttpGET("rssFeedLanguageId");
if (!$rssFeedLanguageId) {
  $rssFeedLanguageId = LibEnv::getEnvHttpPOST("rssFeedLanguageId");
}

// Init the unset variables
if (!$formSubmitted) {
  $url = '';
  $title = '';
  if ($rssFeedLanguage = $rssFeedLanguageUtils->selectById($rssFeedLanguageId)) {
    $language = $rssFeedLanguage->getLanguage();
    $url = $rssFeedLanguage->getUrl();
    $title = $rssFeedLanguage->getTitle();
    $rssFeedId = $rssFeedLanguage->getRssFeedId();
  }
}

$panelUtils->setHeader($mlText[0], "$gRssUrl/admin.php");
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[19], $mlText[21], 300, 300);
if ($rssFeedLanguageId) {
  $panelUtils->addHiddenField('language', $language);
} else {
  $languageNames = $rssFeedUtils->getAvailableLanguages($rssFeedId, true);
  $strSelectLanguage = LibHtml::getSelectList("language", $languageNames);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectLanguage);
}

$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[2], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' name='url' value='$url' size='30' maxlength='255'>", "n"));
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[3], $mlText[4], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' name='title' value='$title' size='30' maxlength='50'>", "n"));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('rssFeedId', $rssFeedId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
