<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$closePopupWindow = LibEnv::getEnvHttpPOST("closePopupWindow");
if ($closePopupWindow) {
  $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$mlText = $languageUtils->getMlText(__FILE__);

$templateElementId = LibEnv::getEnvHttpGET("templateElementId");

if (!$templateElementId) {
  $templateElementId = LibSession::getSessionValue(TEMPLATE_SESSION_ELEMENT);
} else {
  LibSession::putSessionValue(TEMPLATE_SESSION_ELEMENT, $templateElementId);
}

if ($templateElement = $templateElementUtils->selectById($templateElementId)) {
  $elementType = $templateElement->getElementType();
} else {
  $elementType = '';
}

$panelUtils->setHeader($mlText[0]);

if ($languageUtils->countActiveLanguages() > 1) {
  $strCommand = "<a href='$gTemplateDesignUrl/element/add_language.php?templateElementId=$templateElementId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
} else {
  $strCommand = '';
}


$panelUtils->addLine($panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

if ($templateElementLanguages = $templateElementLanguageUtils->selectByTemplateElementId($templateElementId)) {
  foreach ($templateElementLanguages as $templateElementLanguage) {
    $language = $templateElementLanguage->getLanguage();
    $objectId = $templateElementLanguage->getObjectId();
    $templateElementLanguageId = $templateElementLanguage->getId();

    $languageFlag = $languageUtils->renderLanguageFlag($language);
    $languageName = $languageUtils->getLanguageName($language);

    if (!$languageName) {
      $languageName = $mlText[13];
    }

    if ($languageName) {
      $strLanguageEdit = $mlText[15] . " " . $languageName;
    } else {
      $strLanguageEdit = '';
    }

    $elementDescription = $templateElementUtils->getDescription($elementType);

    $editUrl = $templateElementLanguageUtils->getEditContentUrl($elementType, $templateElementLanguageId, $objectId, $language);
    $strCommand = "<a onclick=\"window.open(this.href, '_blank'); return(false);\" href='$editUrl' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[22] $strLanguageEdit'></a>"
      . " <a href='$gTemplateDesignUrl/element/delete_language.php?templateElementLanguageId=$templateElementLanguageId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3] $strLanguageEdit'></a>";

    $panelUtils->addLine($elementDescription . ' ( ' . $languageName . ' ' . $languageFlag . ' )', $panelUtils->addCell($strCommand, "nbr"));
  }
}

$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell("<input type='image' border='0' src='$gCommonImagesUrl/$gImageOk' title='$mlText[14]'>", 'c'));
$panelUtils->addHiddenField('closePopupWindow', 1);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
