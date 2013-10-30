<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $navmenuId = LibEnv::getEnvHttpPOST("navmenuId");
  $navmenuLanguageId = LibEnv::getEnvHttpPOST("navmenuLanguageId");
  $language = LibEnv::getEnvHttpPOST("language");

  $language = LibString::cleanString($language);

  // Check that there is not already a language for the navigation menu
  if ($navmenuLanguage = $navmenuLanguageUtils->selectByNavmenuIdAndLanguage($navmenuId, $language)) {
    $wNavmenuLanguageId = $navmenuLanguage->getId();
    if ($wNavmenuLanguageId != $navmenuLanguageId) {
      array_push($warnings, $mlText[1]);
    }
  }

  if (count($warnings) == 0) {

    if ($navmenuLanguage = $navmenuLanguageUtils->selectById($navmenuLanguageId)) {
      $navmenuLanguage->setLanguage($language);
      $navmenuLanguageUtils->update($navmenuLanguage);
    } else {
      $navmenuItem = new NavmenuItem();
      $listOrder = $navmenuItemUtils->getNextListOrder('');
      $navmenuItem->setListOrder($listOrder);
      $navmenuItemUtils->insert($navmenuItem);
      $navmenuItemId = $navmenuItemUtils->getLastInsertId();

      $navmenuLanguage = new NavmenuLanguage();
      $navmenuLanguage->setNavmenuId($navmenuId);
      $navmenuLanguage->setLanguage($language);
      $navmenuLanguage->setNavmenuItemId($navmenuItemId);
      $navmenuLanguageUtils->insert($navmenuLanguage);
    }

    $str = LibHtml::urlRedirect("$gNavmenuUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $navmenuId = LibEnv::getEnvHttpGET("navmenuId");
  $navmenuLanguageId = LibEnv::getEnvHttpGET("navmenuLanguageId");

}

$language = '';
if ($navmenuLanguageId) {
  if ($navmenuLanguage = $navmenuLanguageUtils->selectById($navmenuLanguageId)) {
    $language = $navmenuLanguage->getLanguage();
    $navmenuId = $navmenuLanguage->getNavmenuId();
  }
}

$languageNames = $navmenuUtils->getAvailableLanguages($navmenuId);
$strSelectLanguage = LibHtml::getSelectList("language", $languageNames, $language);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}


$panelUtils->setHeader($mlText[0], "$gNavmenuUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$label = $popupUtils->getTipPopup($mlText[7], $mlText[12], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "br"), $strSelectLanguage);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('navmenuLanguageId', $navmenuLanguageId);
$panelUtils->addHiddenField('navmenuId', $navmenuId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
