<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $navbarLanguageId = LibEnv::getEnvHttpPOST("navbarLanguageId");

  $navbarLanguageUtils->deleteLanguage($navbarLanguageId);

  $str = LibHtml::urlRedirect("$gNavbarUrl/admin.php");
  printContent($str);
  return;

} else {

  $navbarLanguageId = LibEnv::getEnvHttpGET("navbarLanguageId");

  if ($navbarLanguage = $navbarLanguageUtils->selectById($navbarLanguageId)) {
    $language = $navbarLanguage->getLanguage();
  }

  $languageName = $languageUtils->getLanguageName($language);

  $panelUtils->setHeader($mlText[0], "$gNavbarUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $languageName);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('navbarLanguageId', $navbarLanguageId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
