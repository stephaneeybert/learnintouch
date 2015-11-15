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
  $parentId = LibEnv::getEnvHttpPOST("parentId");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $parentId = LibString::cleanString($parentId);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[4]);
  }

  // The name must not already exist
  if ($templateModel = $templateModelUtils->selectByName($name)) {
    $wTemplateModelId = $templateModel->getId();
    // Except for the current one...
    if ($wTemplateModelId != $templateModelId) {
      array_push($warnings, $mlText[9]);
    }
  }

  if (count($warnings) == 0) {

    if ($templateModel = $templateModelUtils->selectById($templateModelId)) {
      $templateModel->setName($name);
      $templateModel->setDescription($description);
      $templateModel->setParentId($parentId);
      $templateModelUtils->update($templateModel);
    }

    // Set a flag to request the update of the cache file
    $templateUtils->setRefreshCache();

    $str = LibHtml::urlRedirect("$gTemplateUrl/design/model/admin.php");
    printContent($str);
    return;

  }

} else {

  $templateModelId = LibEnv::getEnvHttpGET("templateModelId");

  $name = '';
  $description = '';
  $parentId = '';
  if ($templateModel = $templateModelUtils->selectById($templateModelId)) {
    $name = $templateModel->getName();
    $description = $templateModel->getDescription();
    $parentId = $templateModel->getParentId();
  }

}

$parentList = Array('' => '');
$templateModels = $templateModelUtils->selectWithNoParentAndNotItself($templateModelId);
foreach ($templateModels as $templateModel) {
  $wTemplateModelId = $templateModel->getId();
  $wName = $templateModel->getName();
  $parentList[$wTemplateModelId] = $wName;
}
$strSelectParent = LibHtml::getSelectList("parentId", $parentList, $parentId);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/design/model/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$label = $popupUtils->getTipPopup($mlText[6], $mlText[3], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='text' name='description'  value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[8], $mlText[10], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectParent);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('templateModelId', $templateModelId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
