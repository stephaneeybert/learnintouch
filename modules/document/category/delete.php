<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DOCUMENT);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $categoryId = LibEnv::getEnvHttpPOST("categoryId");

  // Delete the document category only if it does not contain any document
  if ($documents = $documentUtils->selectByCategoryId($categoryId)) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    $documentCategoryUtils->delete($categoryId);

    $str = LibHtml::urlRedirect("$gDocumentUrl/category/admin.php");
    printContent($str);
    return;

  }

} else {

  $categoryId = LibEnv::getEnvHttpGET("categoryId");

}

if ($category = $documentCategoryUtils->selectById($categoryId)) {
  $name = $category->getName();
  $description = $category->getDescription();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gDocumentUrl/category/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('categoryId', $categoryId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
