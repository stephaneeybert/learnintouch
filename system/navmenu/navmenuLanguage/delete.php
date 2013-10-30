<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $navmenuLanguageId = LibEnv::getEnvHttpPOST("navmenuLanguageId");

  $navmenuLanguageUtils->deleteLanguage($navmenuLanguageId);

  $str = LibHtml::urlRedirect("$gNavmenuUrl/admin.php");
  printMessage($str);
  return;

} else {

  $navmenuLanguageId = LibEnv::getEnvHttpGET("navmenuLanguageId");

  if ($navmenuLanguage = $navmenuLanguageUtils->selectById($navmenuLanguageId)) {
    $language = $navmenuLanguage->getLanguage();
  }

  $languageName = $languageUtils->getLanguageName($language);

  $panelUtils->setHeader($mlText[0], "$gNavmenuUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $languageName);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('navmenuLanguageId', $navmenuLanguageId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
