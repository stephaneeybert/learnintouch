<?PHP

$mlText = $languageUtils->getMlText(__FILE__);

// Check that the administrator has access to the preferences
$adminLogin = $adminUtils->checkAdminLogin();
if (!$adminUtils->isPreferenceAdmin($adminLogin)) {
  $str = $mlText[6];
  printMessage($str);
  return;
}

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $preferences = $preferenceUtils->selectPreferences();
  foreach ($preferences as $preference) {
    $preferenceId = $preference->getId();
    $name = $preference->getName();

    $value = LibEnv::getEnvHttpPOST("value_$preferenceId");

    if ($preferenceUtils->isUrl($name)) {
      $webpageId = LibEnv::getEnvHttpPOST("webpageId");
      $webpageName = LibEnv::getEnvHttpPOST("webpageName");
      $webpageId = LibString::cleanString($webpageId);
      $webpageName = LibString::cleanString($webpageName);

      // Clear the page if necessary
      if (!$webpageName) {
        $webpageId = '';
      }

      // If a web page or a system page has been selected then use it
      if ($webpageId) {
        $value = $webpageId;
      } else {
        $value = '';
      }
    }

    if (!$preferenceUtils->isMlText($name)) {
      $preference->setValue($value);
      $preferenceUtils->update($preference);
    }
  }

  if ($preferenceUtils->parentMenuUrl) {
    $str = LibHtml::urlRedirect($preferenceUtils->parentMenuUrl);
  } else {
    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
  }

  printContent($str);
  return;
}

$panelUtils->setHeader($mlText[0], $preferenceUtils->parentMenuUrl);
$help = $popupUtils->getHelpPopup($mlText[1], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF, "edit");

$preferences = $preferenceUtils->selectPreferences();

foreach ($preferences as $preference) {
  $preferenceId = $preference->getId();
  $name = $preference->getName();
  $value = $preference->getValue();
  $value = LibString::cleanString($value);

  $description = $preferenceUtils->getDescription($name);
  $help = $preferenceUtils->getHelp($name);
  $type = $preferenceUtils->getType($name);

  if ($help) {
    $label = $popupUtils->getTipPopup($description, $help, 300, 300);
  } else {
    $label = $description;
  }

  $strResetDefault = "<a href='#' onClick=\"document.forms['edit'].elements['value_$preferenceId'].value = document.forms['edit'].elements['hidden_value_$preferenceId'].value; \"><img border='0' src='$gCommonImagesUrl/$gImageReset' title='$mlText[4]'></a>";

  if ($preferenceUtils->isBoolean($name)) {
    if ($value) {
      $checked = "CHECKED";
    } else {
      $checked = '';
    }

    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='value_$preferenceId' $checked value='1'>");
  } else if ($preferenceUtils->isMlText($name)) {
    $resetUrl = $preferenceUtils->getResetUrl($PHP_SELF) . "?preferenceId=$preferenceId";
    $resetUrl = urlencode($resetUrl);
    $strContent = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageCompose' title='$mlText[7]'>", "$gPreferenceUrl/editMlText.php?preferenceId=$preferenceId&resetUrl=$resetUrl", 600, 600);
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strContent);
  } else if ($preferenceUtils->isText($name)) {
    $defaultValue = $preferenceUtils->getDefaultValue($name);
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='value_$preferenceId' value='$value' size='20' maxlength='255'> $strResetDefault");
    $panelUtils->addHiddenField("hidden_value_$preferenceId", $defaultValue);
  } else if ($preferenceUtils->isTextarea($name) || $preferenceUtils->isRawContent($name)) {
    $defaultValue = $preferenceUtils->getDefaultValue($name);
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<textarea name='value_$preferenceId' cols='30' rows='3'>$value</textarea> $strResetDefault");
    $panelUtils->addHiddenField("hidden_value_$preferenceId", $defaultValue);
  } else if ($preferenceUtils->isSelect($name)) {
    $selectOptions = $preferenceUtils->getSelectOptions($name);
    $strSelect = LibHtml::getSelectList("value_$preferenceId", $selectOptions, $value);
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelect);
  } else if ($preferenceUtils->isRange($name)) {
    $selectRange = $preferenceUtils->getRange($name);
    if (!$value) {
      $value = $preferenceUtils->getRangeDefault($name);
    }
    $strSelect = LibHtml::getSelectList("value_$preferenceId", $selectRange, $value);
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelect);
  } else if ($preferenceUtils->isColor($name)) {
    $strColorPicker = "<a href=\"javascript:TCP.popup(document.forms['edit'].elements['value_$preferenceId']);\"><img border='0' src='$gCommonImagesUrl/$gImageColorPicker' title='$mlText[3]'></a>";

    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' name='value_$preferenceId' value='$value' size='7' maxlength='7'> $strColorPicker", "n"));
  } else if ($preferenceUtils->isUrl($name)) {
    $strLinkPopup = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageSelect' title='$mlText[5]'>", "$gTemplateUrl/select.php", 600, 600);

    $webpageName = $templateUtils->getPageName($value);
    if ($webpageName) {
      $webpageId = $value;
    } else {
      $webpageId = '';
    }

    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' id='webpageName' name='webpageName' value='$webpageName' size='30' maxlength='255'> $strLinkPopup", "n"));
    $panelUtils->addHiddenField('webpageId', $webpageId);
  }

}

$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

$strJsColorPickerHead = "<script type='text/javascript' src='$gJsUrl/colorPicker/picker.js'></script>";

$strJsColorPicker = <<<HEREDOC
<script type='text/javascript'>
var gJsColorPickerUrl = '$gJsUrl/colorPicker/';
</script>
HEREDOC;

// Add the color picker script JUST AFTER the closing form tag
$panelUtils->addContent($strJsColorPicker);

$strRememberScroll = LibJavaScript::rememberScroll("preference_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str, $strJsColorPickerHead);

?>
