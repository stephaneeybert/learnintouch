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
  $modelType = LibEnv::getEnvHttpPOST("modelType");
  $parentId = LibEnv::getEnvHttpPOST("parentId");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $modelType = LibString::cleanString($modelType);
  $parentId = LibString::cleanString($parentId);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[4]);
  }

  // If no parent, then the model type is required
  if (!$parentId && !$modelType) {
    array_push($warnings, $mlText[1]);
  }

  // The name must not already exist
  if ($templateModel = $templateModelUtils->selectByName($name)) {
    array_push($warnings, $mlText[9]);
  }

  if (count($warnings) == 0) {

    if (!$templateModel = $templateModelUtils->selectById($templateModelId)) {
      // Create the property set
      $templatePropertySetId = $templatePropertySetUtils->createPropertySet();
      $innerTemplatePropertySetId = $templatePropertySetUtils->createPropertySet();

      $templateModel = new TemplateModel();
      $templateModel->setName($name);
      $templateModel->setDescription($description);
      $templateModel->setModelType($modelType);
      $templateModel->setParentId($parentId);
      $templateModel->setTemplatePropertySetId($templatePropertySetId);
      $templateModel->setInnerTemplatePropertySetId($innerTemplatePropertySetId);
      $templateModelUtils->insert($templateModel);
      $templateModelId = $templateModelUtils->getLastInsertId();

      // Create the model containers
      $templateModelUtils->createContainers($templateModelId, $parentId);

      // If the model is the only one then set it as the default one
      // and as the entry one
      if ($templateModelUtils->countAll() == 1) {
        $templateUtils->setComputerDefault($templateModelId);
        $templateUtils->setComputerEntry($templateModelId);
      }
    }

    $str = LibHtml::urlRedirect("$gTemplateUrl/design/model/compose.php?templateModelId=$templateModelId");
    printContent($str);
    return;

  }

} else {

  $templateModelId = LibEnv::getEnvHttpGET("templateModelId");

  $name = '';
  $description = '';
  $modelType = '';
  if ($templateModelId) {
    if ($templateModel = $templateModelUtils->selectById($templateModelId)) {
      $name = $templateModel->getName();
      $description = $templateModel->getDescription();
      $modelType = $templateModel->getModelType();
    }
  }

}

$modelList = array();
$i = 0;
$modelTypes = $templateModelUtils->getModelTypeNames();
foreach($modelTypes as $wModelType => $wModelName) {
  if ($modelType == $wModelType) {
    $checkedModel = 'checked';
  }  else {
    $checkedModel = '';
  }

  $imageName = strtolower($wModelType);
  $imageUrl = "$gTemplateDesignUrl/data/type/$imageName.png";

  $modelList[$i] = "<img border='0' src='$imageUrl' title='$wModelName'>"
    . "<br><br>$wModelName"
    . "<br><input type='radio' name='modelType' value='$wModelType' $checkedModel><br><br>";
  $i++;
}

$strSelectModelType = "<table border='0' cellspacing='10' cellpadding='0' cellspacing='0'>";
for ($i = 0; $i < count($modelList); $i = $i + 3) {
  $cell1 = LibUtils::getArrayValue($i, $modelList);
  $cell2 = LibUtils::getArrayValue($i+1, $modelList);
  $cell3 = LibUtils::getArrayValue($i+2, $modelList);

  $strSelectModelType .= "<tr>"
    . "<td align='center'>" . $cell1 . "</td>"
    . "<td align='center'>" . $cell2 . "</td>"
    . "<td align='center'>" . $cell3 . "</td>"
    . "</tr>";
}
$strSelectModelType .= "</table>";

$parentList = Array('' => '');
$templateModels = $templateModelUtils->selectWithNoParent();
foreach ($templateModels as $templateModel) {
  $wTemplateModelId = $templateModel->getId();
  $wName = $templateModel->getName();
  $parentList[$wTemplateModelId] = $wName;
}
$strSelectParent = LibHtml::getSelectList("parentId", $parentList);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/design/model/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$help = $popupUtils->getHelpPopup($mlText[10], 300, 300);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='text' name='description'  value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[2], $mlText[12], 300, 400);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectModelType);
$label = $popupUtils->getTipPopup($mlText[3], $mlText[5], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectParent);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('templateModelId', $templateModelId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
