<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$listDocumentCategories = $documentCategoryUtils->getAll();
array_unshift($listDocumentCategories, '');
$strSelectDocumentCategory = LibHtml::getSelectList("documentCategoryId", $listDocumentCategories);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $documentId = LibEnv::getEnvHttpPOST("documentId");
  $documentCategoryId = LibEnv::getEnvHttpPOST("documentCategoryId");

  if ($documentId) {
    $documentId = 'SYSTEM_PAGE_DOCUMENT' . $documentId;

    $str = $templateUtils->renderJsUpdate($documentId);
    printMessage($str);
  } else if ($documentCategoryId) {
    $str = $templateUtils->renderJsUpdate($documentCategoryId);
    printMessage($str);
  }

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;

} else {

  $documentId = '';

}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/select.php");
$panelUtils->openForm($PHP_SELF);
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gDocumentUrl/suggest_documents.php", "documentName", "documentId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('documentId', $documentId);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nc"));
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[2], $mlText[3], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='documentName' value='' size='40' />");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[3], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectDocumentCategory);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
