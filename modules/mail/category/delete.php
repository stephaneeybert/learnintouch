<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $categoryId = LibEnv::getEnvHttpPOST("categoryId");

  // Delete the mail category only if it is not used
  if ($mails = $mailUtils->selectByCategoryId($categoryId)) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {
    $mailCategoryUtils->delete($categoryId);

    $str = LibHtml::urlRedirect("$gMailUrl/category/admin.php");
    printContent($str);
    return;
  }

} else {

  $categoryId = LibEnv::getEnvHttpGET("categoryId");

}

if ($mailCategory = $mailCategoryUtils->selectById($categoryId)) {
  $name = $mailCategory->getName();
  $description = $mailCategory->getDescription();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gMailUrl/category/admin.php");
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
