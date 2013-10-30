<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $templateModelId = LibEnv::getEnvHttpPOST("templateModelId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[3]);
  }

  // The name must not be a numeric string
  if (is_numeric($name)) {
    array_push($warnings, $mlText[2]);
  }

  // Check that the name is not already used
  if ($templateModel = $templateModelUtils->selectByName($name)) {
    $wTemplateModelId = $templateModel->getId();
    if ($wTemplateModelId != $templateModelId) {
      array_push($warnings, $mlText[4]);
    }
  }

  if (count($warnings) == 0) {

    // Duplicate the model
    $lastInsertTemplateModelId = $templateModelUtils->duplicate($templateModelId, $name, $description);

    // Cache the css file for the model
    $templateModelUtils->cacheCssFile($lastInsertTemplateModelId);

    $str = LibHtml::urlRedirect("$gTemplateUrl/design/model/admin.php");
    printContent($str);
    return;

  }

} else {

  $templateModelId = LibEnv::getEnvHttpGET("templateModelId");

  $name = '';
  $description = '';
  if ($templateModelId) {
    if ($templateModel = $templateModelUtils->selectById($templateModelId)) {
      $randomNumber = LibUtils::generateUniqueId();
      $name = $templateModel->getName() . TEMPLATE_DUPLICATA . '_' . $randomNumber;
      $description = $templateModel->getDescription();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/design/model/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('templateModelId', $templateModelId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
