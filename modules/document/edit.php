<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DOCUMENT);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $documentId = LibEnv::getEnvHttpPOST("documentId");
  $reference = LibEnv::getEnvHttpPOST("reference");
  $description = LibEnv::getEnvHttpPOST("description");
  $hide = LibEnv::getEnvHttpPOST("hide");
  $secured = LibEnv::getEnvHttpPOST("secured");
  $categoryId = LibEnv::getEnvHttpPOST("categoryId");
  $filename = LibEnv::getEnvHttpPOST("filename");

  $reference = LibString::cleanString($reference);
  $description = LibString::cleanString($description);
  $hide = LibString::cleanString($hide);
  $secured = LibString::cleanString($secured);

  // If the document is assigned to another category then the document list order must be set according to the category number of documents, otherwise the document list order is not changed
  $listOrder = '';
  if ($document = $documentUtils->selectById($documentId)) {
    $listOrder = $document->getListOrder();
    $currentCategoryId = $document->getCategoryId();
  }

  // It must be a zero and not an empty value otherwise the list order will be reassigned every time
  if (!$currentCategoryId) {
    $currentCategoryId = '0';
  }

  // Check if the category is changed
  if ($currentCategoryId != $categoryId) {
    // Get the next list order
    $listOrder = $documentUtils->getNextListOrder($categoryId);
  }

  if ($document = $documentUtils->selectById($documentId)) {
    $document->setReference($reference);
    $document->setDescription($description);
    $document->setHide($hide);
    $document->setSecured($secured);
    $document->setCategoryId($categoryId);
    $document->setListOrder($listOrder);
    $documentUtils->update($document);
  } else {
    $document = new Document();
    $document->setReference($reference);
    $document->setDescription($description);
    $document->setHide($hide);
    $document->setSecured($secured);
    $document->setCategoryId($categoryId);
    $document->setListOrder($listOrder);
    $documentUtils->insert($document);
  }

  $str = LibHtml::urlRedirect("$gDocumentUrl/admin.php");
  printContent($str);
  return;

} else {

  $documentId = LibEnv::getEnvHttpGET("documentId");

  $filename = '';
  if ($documentId) {
    if ($document = $documentUtils->selectById($documentId)) {
      $filename = $document->getFile();
      $reference = $document->getReference();
      $description = $document->getDescription();
      $hide = $document->getHide();
      $secured = $document->getSecured();
      $categoryId = $document->getCategoryId();
    }
  }

  $categorys = $documentCategoryUtils->selectAll();
  $categoryList = Array('' => '');
  foreach ($categorys as $category) {
    $wDocumentCategoryId = $category->getId();
    $wName = $category->getName();
    $categoryList[$wDocumentCategoryId] = $wName;
  }
  $strSelect = LibHtml::getSelectList("categoryId", $categoryList, $categoryId);

  if ($hide == '1') {
    $checkedHide = "CHECKED";
  } else {
    $checkedHide = '';
  }

  if ($secured == '1') {
    $checkedSecured = "CHECKED";
  } else {
    $checkedSecured = '';
  }

  $panelUtils->setHeader($mlText[0], "$gDocumentUrl/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $label = $popupUtils->getTipPopup($mlText[9], $mlText[10], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $filename);
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[5], $mlText[2], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelect);
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[7], $mlText[3], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='description'  value='$description' size='30' maxlength='255'>");
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[6], $mlText[8], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='reference' value='$reference' size='30' maxlength='50'>");
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[1], $mlText[4], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='hide' $checkedHide value='1'>");
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[11], $mlText[12], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='secured' $checkedSecured value='1'>");
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('documentId', $documentId);
  $panelUtils->addHiddenField('filename', $filename);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
