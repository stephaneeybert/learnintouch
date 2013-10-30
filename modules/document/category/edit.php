<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DOCUMENT);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $categoryId = LibEnv::getEnvHttpPOST("categoryId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  if (count($warnings) == 0) {

    if ($category = $documentCategoryUtils->selectById($categoryId)) {
      $category->setName($name);
      $category->setDescription($description);
      $documentCategoryUtils->update($category);
    } else {
      $category = new DocumentCategory();
      $category->setName($name);
      $category->setDescription($description);

      // Get the next list order if a document category is specified
      $nextListOrder = $documentCategoryUtils->getNextListOrder();
      $category->setListOrder($nextListOrder);

      $documentCategoryUtils->insert($category);
    }

    $str = LibHtml::urlRedirect("$gDocumentUrl/category/admin.php");
    printContent($str);
    return;

  }

} else {

  $categoryId = LibEnv::getEnvHttpGET("categoryId");

  $name = '';
  $description = '';
  if ($categoryId) {
    if ($category = $documentCategoryUtils->selectById($categoryId)) {
      $name = $category->getName();
      $description = $category->getDescription();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gDocumentUrl/category/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('categoryId', $categoryId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
