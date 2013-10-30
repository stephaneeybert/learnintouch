<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $categoryId = LibEnv::getEnvHttpPOST("categoryId");

  // Delete the sms category only if it is not used
  if ($smss = $smsUtils->selectByCategoryId($categoryId)) {
    array_push($warnings, $mlText[3]);
  }

  if (count($warnings) == 0) {
    $smsCategoryUtils->delete($categoryId);

    $str = LibHtml::urlRedirect("$gSmsUrl/category/admin.php");
    printContent($str);
    return;
  }

} else {

  $categoryId = LibEnv::getEnvHttpGET("categoryId");

}

$name = '';
$description = '';

if ($smsCategory = $smsCategoryUtils->selectById($categoryId)) {
  $name = $smsCategory->getName();
  $description = $smsCategory->getDescription();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gSmsUrl/category/admin.php");
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
