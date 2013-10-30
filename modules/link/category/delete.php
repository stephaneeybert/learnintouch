<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LINK);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $linkCategoryId = LibEnv::getEnvHttpPOST("linkCategoryId");

  // Delete the link category only if it is not used
  if ($links = $linkUtils->selectByCategoryId($linkCategoryId)) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {

    $linkCategoryUtils->delete($linkCategoryId);

    $str = LibHtml::urlRedirect("$gLinkUrl/category/admin.php");
    printContent($str);
    return;

  }

} else {

  $linkCategoryId = LibEnv::getEnvHttpGET("linkCategoryId");

}

if ($linkCategory = $linkCategoryUtils->selectById($linkCategoryId)) {
  $name = $linkCategory->getName();
  $description = $linkCategory->getDescription();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gLinkUrl/category/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $description);
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('linkCategoryId', $linkCategoryId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
