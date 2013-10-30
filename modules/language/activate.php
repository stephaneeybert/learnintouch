<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LANGUAGE);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $languageId = LibEnv::getEnvHttpPOST("languageId");
  $code = LibEnv::getEnvHttpPOST("code");
  $activate = LibEnv::getEnvHttpPOST("activate");

  $code = LibString::cleanString($code);
  $activate = LibString::cleanString($activate);

  $defaultLanguageCode = $propertyUtils->retrieve($languageUtils->propertyDefault);

  // Check that the default language is not being deactivated
  if ($code == $defaultLanguageCode && $activate == false) {
    array_push($warnings, $mlText[15]);
  }

  if (count($warnings) == 0) {

    if ($activate) {
      // Activate the language
      $languageUtils->activateLanguage($code);
    } else {
      // Deactivate the language
      $languageUtils->deactivateLanguage($code);
    }

    $str = LibHtml::urlRedirect("$gLanguageUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $languageId = LibEnv::getEnvHttpGET("languageId");
  $activate = LibEnv::getEnvHttpGET("activate");

}

if ($languageId) {
  if ($language = $languageUtils->selectById($languageId)) {
    $code = $language->getCode();
    $name = $language->getName();
    $strImage = $languageUtils->renderImage($languageId);
  }
}

if ($activate == 1) {
  $mlTitle = $mlText[4];
  $mlTextUsage = $mlText[1];
} else {
  $mlTitle = $mlText[0];
  $mlTextUsage = $mlText[2];
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlTitle, "$gLanguageUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $code);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $strImage);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlTextUsage, "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('languageId', $languageId);
$panelUtils->addHiddenField('activate', $activate);
$panelUtils->addHiddenField('code', $code);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
