<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $templateElementLanguageId = LibEnv::getEnvHttpPOST("templateElementLanguageId");

  $templateElementLanguageUtils->deleteLanguage($templateElementLanguageId);

  $str = LibHtml::urlRedirect("$gTemplateDesignUrl/element/admin.php");
  printContent($str);
  return;

} else {

  $templateElementLanguageId = LibEnv::getEnvHttpGET("templateElementLanguageId");

  $elementDescription = '';
  if ($templateElementLanguage = $templateElementLanguageUtils->selectById($templateElementLanguageId)) {
    $language = $templateElementLanguage->getLanguage();
    $templateElementId = $templateElementLanguage->getTemplateElementId();
    if ($templateElement = $templateElementUtils->selectById($templateElementId)) {
      $elementType = $templateElement->getElementType();
      $elementDescription = $templateElementUtils->getDescription($elementType);

      $languageFlag = $languageUtils->renderLanguageFlag($language);
      $languageName = $languageUtils->getLanguageName($language);

      if (!$languageName) {
        $languageName = $mlText[3];
      }

      $elementDescription .= ' ( ' . $languageName . ' ' . $languageFlag . ' )';
    }
  }

  $panelUtils->setHeader($mlText[0], "$gTemplateDesignUrl/element/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "br"), $elementDescription);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('templateElementLanguageId', $templateElementLanguageId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
