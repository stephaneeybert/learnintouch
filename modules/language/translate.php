<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LANGUAGE);

$adminModuleUtils->checkAdminModule(MODULE_LANGUAGE_TRANSLATE);

$toLanguageCode = LibEnv::getEnvHttpGET("toLanguageCode");
$filePath = LibEnv::getEnvHttpGET("filePath");
if (!$toLanguageCode) {
  $toLanguageCode = LibEnv::getEnvHttpPOST("toLanguageCode");
  $filePath = LibEnv::getEnvHttpPOST("filePath");
}

$toLanguageCode = urldecode($toLanguageCode);
$filePath = urldecode($filePath);

$mlText = $languageUtils->getMlText(__FILE__);
if ($language = $languageUtils->selectByCode($toLanguageCode)) {
  $languageId = $language->getId();
  $strImage = $languageUtils->renderImage($languageId);

  $translatedFilePath = '';
  $filename = basename($filePath);
  $nameBits = explode(".", $filename);
  if (is_array($nameBits) && count($nameBits) > 0) {
    $englishFilePath = dirname($filePath) . '/.' . $nameBits[0] . '.' . 'en' . '.php';
    $translatedFilePath = dirname($filePath) . '/.' . $nameBits[0] . '.' . $toLanguageCode . '.php';
  }
}

if (!is_file($englishFilePath)) {
  reportError("The file $englishFilePath or the file $translatedFilePath could not be found for translation.");
}

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  // Get the text string keys from the english language file
  $textStrings = $languageUtils->getMlTextStrings($englishFilePath);

  $editedTextStrings = array();
  foreach ($textStrings as $key => $textString) {
    $editedTextStrings[$key] = LibEnv::getEnvHttpPOST("text_string_$key");
  }
  ksort($editedTextStrings);
  foreach ($editedTextStrings as $key => $editedTextString) {
    $editedTextStrings[$key] = LibString::stripBSlashes($editedTextStrings[$key]);
  }

  // Create the file content and write the file
  $languageUtils->setMlTextStrings($translatedFilePath, $editedTextStrings);

  $str = LibHtml::urlRedirect("$gLanguageUrl/translate_admin.php?toLanguageCode=$toLanguageCode");
  printContent($str);
  return;

} else {

  $englishStrings = $languageUtils->getMlTextStrings($englishFilePath);
  $textStrings = $languageUtils->getMlTextStrings($translatedFilePath);

  // Add the english version for the missing array values
  // This is needed if new array values are added after the language
  // file has already been translated
  foreach ($englishStrings as $key => $value) {
    if (!isset($textStrings[$key])) {
      $textStrings[$key] = $value;
    }
  }

  $strGoogleTranslate = <<<HEREDOC
<script type='text/javascript'>
function getGoogleTranslation(englishString, toLanguageCode, inputFieldId) {
  englishString = encodeURIComponent(englishString);
  toLanguageCode = encodeURIComponent(toLanguageCode);
  var url = '$gLanguageUrl/get_google_translation.php?text='+englishString+'&fromLanguageCode=en&toLanguageCode='+toLanguageCode+'&inputFieldId='+inputFieldId;
  ajaxAsynchronousRequest(url, displayTranslation);
}

function displayTranslation(responseText) {
  var response = eval('(' + responseText + ')');
  var translation = response.translation;
  var inputFieldId = response.inputFieldId;
  if (inputFieldId && translation) {
    document.getElementById(inputFieldId).value = translation;
  }
}
</script>
HEREDOC;

  $panelUtils->setHeader($mlText[0], "$gLanguageUrl/translate_admin.php?toLanguageCode=$toLanguageCode");
  $help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
  $panelUtils->setHelp($help);
  $panelUtils->addContent($strGoogleTranslate);
  $strCommand = "<a href='$gLanguageUrl/translate_admin.php?toLanguageCode=$toLanguageCode' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[4]'></a>";
  $panelUtils->addLine($panelUtils->addCell($strCommand, "br"));
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $strImage);
  $panelUtils->addLine();

  $panelUtils->openForm($PHP_SELF, 'edit');

  foreach ($textStrings as $key => $textString) {
    if (!array_key_exists($key, $englishStrings)) {
      reportError("The language resource key $key \n" . $textStrings[$key] . "\nfound in $filePath exists in $toLanguageCode but could not be found in english.");
      continue;
    }

    $englishString = $englishStrings[$key];

    // Protect the single quotes
    $textString = str_replace("'", "&#039;", $textString);

    // Input the text string
    if (strstr($englishString, "<br />") || strstr($englishString, "<br>")) {

      // Transform the '\n' strings into line breaks
      $textString = str_replace("<br>", "\n", $textString);
      $textString = str_replace("<br />", "\n", $textString);

      $strInputField = "<textarea rows=6 cols=60 id='text_string_$key' name='text_string_$key'>$textString</textarea>";
    } else {
      $strInputField = "<input type='text' id='text_string_$key' name='text_string_$key' value='$textString' size='62'>";
    }

    $encodedString = $englishString;

    $translate = "<a href='javascript:void(0);'><span class='translate' encodedString='$encodedString' toLanguageCode='$toLanguageCode' textKey='text_string_$key'><img border='0' src='$gCommonImagesUrl/$gImageReset' title='$mlText[3]'></span></a>";
    $translate = ''; // Google has shut the door

    $panelUtils->addLine($panelUtils->addCell($englishString, "r"), $panelUtils->addCell($strInputField . ' ' . $translate, ""));
    $panelUtils->addLine();
  }

$strJs = <<<HEREDOC
<script type="text/javascript">
$(document).ready(function() {
  $('.translate').click(function (event) {
    var textString = $(this).attr('encodedString');
    var toLanguageCode = $(this).attr('toLanguageCode');
    var textKey = $(this).attr('textKey');
    getGoogleTranslation(textString, toLanguageCode, textKey); 
  });
});
</script>
HEREDOC;
  $panelUtils->addContent($strJs);
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('toLanguageCode', $toLanguageCode);
  $panelUtils->addHiddenField('filePath', $filePath);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
