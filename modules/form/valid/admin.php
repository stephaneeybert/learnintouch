<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FORM);

$mlText = $languageUtils->getMlText(__FILE__);

$formItemId = LibEnv::getEnvHttpGET("formItemId");

if (!$formItemId) {
  $formItemId = LibSession::getSessionValue(FORM_SESSION_ITEM);
} else {
  LibSession::putSessionValue(FORM_SESSION_ITEM, $formItemId);
}

if ($formItem = $formItemUtils->selectById($formItemId)) {
  $itemName = $formItem->getName();
} else {
  $itemName = '';
}

$panelUtils->setHeader($mlText[0], "$gFormUrl/item/admin.php");
$help = $popupUtils->getHelpPopup($mlText[7], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), $itemName, '', '');
$panelUtils->addLine();
$strCommand = "<a href='$gFormUrl/valid/edit.php?formItemId=$formItemId' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[4]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$formValids = $formValidUtils->selectByFormItemId($formItemId);

$panelUtils->openList();
foreach ($formValids as $formValid) {
  $formValidId = $formValid->getId();
  $type = $formValid->getType();
  $languages = $languageUtils->getActiveLanguages();
  $message = '';
  foreach ($languages as $language) {
    $languageId = $language->getId();
    $languageCode = $language->getCode();
    $strImage = $languageUtils->renderImage($languageId);
    $message .= '<div>' . $strImage . ' : ' . $languageUtils->getTextForLanguage($formValid->getMessage(), $languageCode) . '</div>';
  }
  $boundary = $formValid->getBoundary();

  $typeName = $gFormValidTypes[$type];

  $strCommand = "<a href='$gFormUrl/valid/edit.php?formValidId=$formValidId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gFormUrl/valid/delete.php?formValidId=$formValidId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($typeName, $message, $boundary, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
