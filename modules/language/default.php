<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LANGUAGE);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  // Get the form data
  $languageId = LibEnv::getEnvHttpPOST("languageId");

  // Get the language code
  if ($language = $languageUtils->selectById($languageId)) {
    $code = $language->getCode();
    }

  // Set the language as default one
  $languageUtils->setDefaultLanguageCode($code);

  $str = LibHtml::urlRedirect("$gLanguageUrl/admin.php");
  printMessage($str);
  return;

  } else {

  $languageId = LibEnv::getEnvHttpGET("languageId");

  if ($language = $languageUtils->selectById($languageId)) {
    $code = $language->getCode();
    $name = $language->getName();
    $strImage = $languageUtils->renderImage($languageId);
    }

  $panelUtils->setHeader($mlText[0], "$gLanguageUrl/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $code);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $strImage);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('languageId', $languageId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
