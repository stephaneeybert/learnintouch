<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $navlinkId = LibEnv::getEnvHttpPOST("navlinkId");
  $language = LibEnv::getEnvHttpPOST("language");

  $language = LibString::cleanString($language);

  // Check that there is not already a language for the link
  if ($navlinkItem = $navlinkItemUtils->selectByLanguageAndNavlinkId($language, $navlinkId)) {
    array_push($warnings, $mlText[20]);
  }

  if (count($warnings) == 0) {

    $navlinkItem = new NavlinkItem();
    $navlinkItem->setNavlinkId($navlinkId);
    $navlinkItem->setLanguage($language);
    $navlinkItemUtils->insert($navlinkItem);

    $str = LibHtml::urlRedirect("$gNavlinkUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $navlinkId = LibEnv::getEnvHttpGET("navlinkId");

}

$languageNames = $navlinkUtils->getAvailableLanguages($navlinkId, true);
$strSelectLanguage = LibHtml::getSelectList("language", $languageNames);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gNavlinkUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[19], $mlText[21], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectLanguage);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('navlinkId', $navlinkId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
