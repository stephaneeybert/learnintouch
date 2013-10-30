<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[13], 300, 400);
$panelUtils->setHelp($help);

$strCommand = " <a href='$gTemplateUrl/design/model/add.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>"
. " <a href='$gTemplateUrl/design/model/entry.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageHome' title='$mlText[12]'></a>"
. " <a href='$gTemplateUrl/design/model/entryAdmin.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageHome' title='$mlText[7]'></a>"
. " <a href='$gImageSetUrl/admin.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageNavIcon' title='$mlText[21]'></a>"
. " <a href='$gTemplateUrl/design/model/preference.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[27]'></a>";

$panelUtils->addLine($panelUtils->addCell("$mlText[4]", "nb"), $panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[14]", "nb"), $panelUtils->addCell("$mlText[8]", "nb"), '', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

$templateModels = $templateModelUtils->selectAll();

$panelUtils->openList();
foreach ($templateModels as $templateModel) {
  $templateModelId = $templateModel->getId();
  $name = $templateModel->getName();
  $description = $templateModel->getDescription();

  $strDefault = '';
  if ($templateModelId == $templateUtils->getComputerDefault()) {
    $strDefault .= "<img src='$gCommonImagesUrl/$gImageComputer' border='0' title='$mlText[15]'>";
  }
  if ($templateModelId == $templateUtils->getPhoneDefault()) {
    $strDefault .= " <img src='$gCommonImagesUrl/$gImagePda' border='0' title='$mlText[16]'>";
  }

  $strEntry = '';
  if ($templateModelId == $templateUtils->getComputerEntry()) {
    $strEntry .= "<img src='$gCommonImagesUrl/$gImageComputer' border='0' title='$mlText[17]'>";
  }
  if ($templateModelId == $templateUtils->getPhoneEntry()) {
    $strEntry .= " <img src='$gCommonImagesUrl/$gImagePda' border='0' title='$mlText[18]'>";
  }

  $strCommand = ''
    . "<a href='$gTemplateUrl/design/model/edit.php?templateModelId=$templateModelId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gTemplateUrl/design/model/compose.php?templateModelId=$templateModelId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDesign' title='$mlText[6]'></a>"
    . " <a href='$gTemplateUrl/design/page/admin.php?templateModelId=$templateModelId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageProperty' title='$mlText[20]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[19]'>", "$gTemplateUrl/design/model/preview.php?templateModelId=$templateModelId", 800, 600)
    . " <a href='$gTemplateUrl/design/model/duplicate.php?templateModelId=$templateModelId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageCopy' title='$mlText[9]'></a>";

  $strCommand .= " <a href='$gTemplateUrl/design/model/delete.php?templateModelId=$templateModelId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($panelUtils->addCell($name, "n"), $panelUtils->addCell($description, "n"), $strEntry, $strDefault, '', $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
