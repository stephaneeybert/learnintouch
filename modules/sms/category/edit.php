<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

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

    if ($smsCategory = $smsCategoryUtils->selectById($categoryId)) {
      $smsCategory->setName($name);
      $smsCategory->setDescription($description);
      $smsCategoryUtils->update($smsCategory);
    } else {
      $smsCategory = new SmsCategory();
      $smsCategory->setName($name);
      $smsCategory->setDescription($description);
      $smsCategoryUtils->insert($smsCategory);
    }

    $str = LibHtml::urlRedirect("$gSmsUrl/category/admin.php");
    printContent($str);
    return;

  }

} else {

  $categoryId = LibEnv::getEnvHttpGET("categoryId");

  $name = '';
  $description = '';
  if ($categoryId) {
    if ($smsCategory = $smsCategoryUtils->selectById($categoryId)) {
      $name = $smsCategory->getName();
      $description = $smsCategory->getDescription();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gSmsUrl/category/admin.php");
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
