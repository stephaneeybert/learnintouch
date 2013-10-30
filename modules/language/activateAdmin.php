<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LANGUAGE);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $languageId = LibEnv::getEnvHttpPOST("languageId");
  $code = LibEnv::getEnvHttpPOST("code");
  $activate = LibEnv::getEnvHttpPOST("activate");

  $code = LibString::cleanString($code);
  $activate = LibString::cleanString($activate);

  if ($activate) {
    // Activate the language
    $languageUtils->activateAdminLanguage($code);
    } else {
    // Deactivate the language
    $languageUtils->deactivateAdminLanguage($code);
    }

  $str = LibHtml::urlRedirect("$gLanguageUrl/admin.php");
  printContent($str);
  return;

  } else {

  $languageId = LibEnv::getEnvHttpGET("languageId");

  if ($languageId) {
    if ($language = $languageUtils->selectById($languageId)) {
      $code = $language->getCode();
      $name = $language->getName();
      $strImage = $languageUtils->renderImage($languageId);
      }
    }

  $activate = LibEnv::getEnvHttpGET("activate");

  if ($activate == 1) {
    $mlTitle = $mlText[4];
    $mlTextUsage = $mlText[1];
    } else {
    $mlTitle = $mlText[0];
    $mlTextUsage = $mlText[2];
    }

  $panelUtils->setHeader($mlTitle, "$gLanguageUrl/admin.php");
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
  }

?>
