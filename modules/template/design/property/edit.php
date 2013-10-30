<?PHP

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($strTitle);
$help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
$panelUtils->setHelp($help);
$panelUtils->addContent("<form action='$gTemplateUrl/design/property/editController.php' method='post' name='edit' id='edit'>");

$templatePropertySetUtils->loadPropertyTypes();
foreach ($templatePropertySetUtils->currentPropertyTypes as $name) {
  if ($templateProperty = $templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetUtils->currentTemplatePropertySetId, $name)) {
    $value = $templateProperty->getValue();
  } else {
    $value = '';
  }

  // Prevent the setting of the width or height on images
  // as this should be done in the preferences so as to have images resized on the server side
  if (strstr($tagId, 'image_file') && ($name == 'WIDTH' || $name == 'HEIGHT')) {
    continue;
  }

  $description = $templatePropertySetUtils->getDescription($name);
  $help = $templatePropertySetUtils->getHelp($name);
  $type = $templatePropertySetUtils->getType($name);

  if ($help) {
    $label = $popupUtils->getTipPopup($description, $help, 300, 300);
  } else {
    $label = $description;
  }

  if (!$name) {
    $panelUtils->addLine();
  } else if ($templatePropertySetUtils->isBoolean($name)) {
    if ($value) {
      $checked = "CHECKED";
    } else {
      $checked = '';
    }

    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='value_$name' $checked value='1'>");
  } else if ($templatePropertySetUtils->isText($name)) {
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='value_$name' value='$value' size='20' maxlength='255'>");
  } else if ($templatePropertySetUtils->isTextarea($name)) {
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<textarea name='value_$name' cols='20' rows='3'>$value</textarea>");
  } else if ($templatePropertySetUtils->isSelect($name)) {
    $selectOptions = $templatePropertySetUtils->getSelectOptions($name);
    $strSelect = LibHtml::getSelectList("value_$name", $selectOptions, $value);
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelect);
  } else if ($templatePropertySetUtils->isImage($name)) {
    $url = "$gTemplateUrl/design/property/image.php"
      . "?templatePropertySetId="
      . $templatePropertySetUtils->currentTemplatePropertySetId
      . "&name="
      . $name;
    $strPopup = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[4]'>", $url, 600, 600);
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' name='value_$name' value='$value' size='20' maxlength='255'> $strPopup", "n"));
  } else if ($templatePropertySetUtils->isRange($name)) {
    $selectRange = $templatePropertySetUtils->getRange($name);
    if (strlen($value) == 0) {
      $value = $templatePropertySetUtils->getRangeDefault($name);
    }
    $strSelect = LibHtml::getSelectList("value_$name", $selectRange, $value);
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelect);
  } else if ($templatePropertySetUtils->isColor($name)) {
    $strColorPicker = "<a href=\"javascript:TCP.popup(document.forms['edit'].elements['value_$name']);\"><img border='0' src='$gCommonImagesUrl/$gImageColorPicker' title='$mlText[3]'></a>";

    $colorHelp = $popupUtils->getTipPopup('', $mlText[5], 300, 400);
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' name='value_$name' value='$value' size='7' maxlength='7'> $strColorPicker $colorHelp", "n"));
  }

}

$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('templatePropertySetId', $templatePropertySetId);
$panelUtils->addHiddenField('templateModelId', $templatePropertySetUtils->currentTemplateModelId);
$panelUtils->addHiddenField('tagID', $templatePropertySetUtils->currentTagID);
$panelUtils->closeForm();

$strJsColorPickerHead = "<script type='text/javascript' src='$gJsUrl/colorPicker/picker.js'></script>";

$strJsColorPicker = <<<HEREDOC
<script type='text/javascript'>
var gJsColorPickerUrl = '$gJsUrl/colorPicker/';
</script>
HEREDOC;

// Add the color picker script JUST AFTER the closing form tag
$panelUtils->addContent($strJsColorPicker);

$str = $panelUtils->render();

printAdminPage($str, $strJsColorPickerHead);

?>
