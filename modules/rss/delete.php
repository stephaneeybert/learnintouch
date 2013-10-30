<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $rssFeedLanguageId = LibEnv::getEnvHttpPOST("rssFeedLanguageId");

  $rssFeedLanguageUtils->delete($rssFeedLanguageId);

  $str = LibHtml::urlRedirect("$gRssUrl/admin.php");
  printContent($str);
  return;

} else {

  $rssFeedLanguageId = LibEnv::getEnvHttpGET("rssFeedLanguageId");

  if ($rssFeedLanguage = $rssFeedLanguageUtils->selectById($rssFeedLanguageId)) {
    $language = $rssFeedLanguage->getLanguage();
    $url = $rssFeedLanguage->getUrl();
  }

  $languageName = $languageUtils->getLanguageName($language);

  $panelUtils->setHeader($mlText[0], "$gRssUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $languageName);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "br"), $url);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('rssFeedLanguageId', $rssFeedLanguageId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
