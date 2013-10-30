<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LINK);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $linkCategoryId = LibEnv::getEnvHttpPOST("linkCategoryId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  if (count($warnings) == 0) {

    if ($linkCategory = $linkCategoryUtils->selectById($linkCategoryId)) {
      $linkCategory->setName($name);
      $linkCategory->setDescription($description);
      $linkCategoryUtils->update($linkCategory);
    } else {
      $linkCategory = new LinkCategory();
      $linkCategory->setName($name);
      $linkCategory->setDescription($description);
      $linkCategoryUtils->insert($linkCategory);
    }

    $str = LibHtml::urlRedirect("$gLinkUrl/category/admin.php");
    printContent($str);
    return;

  }

} else {

  $linkCategoryId = LibEnv::getEnvHttpGET("linkCategoryId");

  $name = '';
  $description = '';
  if ($linkCategoryId) {
    if ($linkCategory = $linkCategoryUtils->selectById($linkCategoryId)) {
      $name = $linkCategory->getName();
      $description = $linkCategory->getDescription();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gLinkUrl/category/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('linkCategoryId', $linkCategoryId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
