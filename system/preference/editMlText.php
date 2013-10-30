<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $preferenceId = LibEnv::getEnvHttpPOST("preferenceId");

  $values = '';
  $languages = $languageUtils->getActiveLanguages();
  foreach ($languages as $language) {
    $languageCode = $language->getCode();
    $value = LibEnv::getEnvHttpPOST("value_$languageCode");
    $values = $languageUtils->setTextForLanguage($values, $languageCode, $value);
  }

  if ($preference = $preferenceUtils->selectById($preferenceId)) {
    $preference->setValue($values);
    $preferenceUtils->update($preference);
  }

  $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
  printContent($str);
  return;

} else {

  $preferenceId = LibEnv::getEnvHttpGET("preferenceId");
  $resetUrl = LibEnv::getEnvHttpGET("resetUrl");

  $resetUrl = urldecode($resetUrl);

  $values = '';
  $language = '';
  if ($preference = $preferenceUtils->selectById($preferenceId)) {
    $values = $preference->getValue();
  }

  $strJsReset = <<<HEREDOC
<script>
function confirmReset() {
  confirmation = confirm('$mlText[1]');
  if (confirmation) {
    return(true);
  }

  return(false);
}

function resetPreference(url) {
  if (confirmReset()) {
    ajaxAsynchronousRequest(url, refreshPreference);
  }
}

function refreshPreference(responseText) {
  var response = eval('(' + responseText + ')');
  var defaultValue = response.defaultValue;
  var preferenceDivId = 'value_' + response.languageCode;
  if (preferenceDivId) {
    document.getElementById(preferenceDivId).value = defaultValue;
  }
}
</script>
HEREDOC;

  $panelUtils->setHeader($mlText[0]);
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addContent($strJsReset);
  $i = 0;
  $languages = $languageUtils->getActiveLanguages();
  foreach ($languages as $language) {
    $languageCode = $language->getCode();
    $languageName = $language->getName();
    $value = $languageUtils->getTextForLanguage($values, $languageCode);
    $languageFlag = $languageUtils->renderLanguageFlag($languageCode);
    $strField = "<textarea name='value_$languageCode' id='value_$languageCode' cols='30' rows='5'>$value</textarea> $languageFlag";
    $strResetUrl = $resetUrl . "&languageCode=$languageCode";
    $strField .= ' ' . "<a href=\"javascript:resetPreference('$strResetUrl');\" $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageReset' title='$mlText[2] $languageName'></a>";
    if ($i == 0) {
      $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $strField);
      $i++;
    } else {
      $panelUtils->addLine('', $strField);
    }
  }

  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('preferenceId', $preferenceId);
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->closeForm();
  $panelUtils->addLine();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
