<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $navbarId = LibEnv::getEnvHttpPOST("navbarId");
  $navbarLanguageId = LibEnv::getEnvHttpPOST("navbarLanguageId");
  $language = LibEnv::getEnvHttpPOST("language");

  $language = LibString::cleanString($language);

  // Check that there is not already a language for the navigation bar
  if ($navbarLanguage = $navbarLanguageUtils->selectByNavbarIdAndLanguage($navbarId, $language)) {
    $wNavbarLanguageId = $navbarLanguage->getId();
    if ($wNavbarLanguageId != $navbarLanguageId) {
      array_push($warnings, $mlText[1]);
    }
  }

  if (count($warnings) == 0) {

    if ($navbarLanguage = $navbarLanguageUtils->selectById($navbarLanguageId)) {
      $navbarLanguage->setLanguage($language);
      $navbarLanguageUtils->update($navbarLanguage);
    } else {
      $navbarLanguage = new NavbarLanguage();
      $navbarLanguage->setNavbarId($navbarId);
      $navbarLanguage->setLanguage($language);
      $navbarLanguageUtils->insert($navbarLanguage);
    }

    $str = LibHtml::urlRedirect("$gNavbarUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $navbarId = LibEnv::getEnvHttpGET("navbarId");
  $navbarLanguageId = LibEnv::getEnvHttpGET("navbarLanguageId");

}

$language = '';
if ($navbarLanguageId) {
  if ($navbarLanguage = $navbarLanguageUtils->selectById($navbarLanguageId)) {
    $language = $navbarLanguage->getLanguage();
    $navbarId = $navbarLanguage->getNavbarId();
  }
}

$languageNames = $navbarUtils->getAvailableLanguages($navbarId);
$strSelectLanguage = LibHtml::getSelectList("language", $languageNames, $language);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gNavbarUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$label = $popupUtils->getTipPopup($mlText[7], $mlText[12], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "br"), $strSelectLanguage);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('navbarLanguageId', $navbarLanguageId);
$panelUtils->addHiddenField('navbarId', $navbarId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
