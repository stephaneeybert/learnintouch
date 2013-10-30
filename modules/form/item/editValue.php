<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FORM);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $formItemValueId = LibEnv::getEnvHttpPOST("formItemValueId");
  $value = LibEnv::getEnvHttpPOST("value");
  $text = LibEnv::getEnvHttpPOST("text");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");

  $value = LibString::cleanString($value);

  $text = LibString::cleanHtmlString($text);

  if (count($warnings) == 0) {

    if ($formItemValue = $formItemValueUtils->selectById($formItemValueId)) {
      $formItemValue->setValue($value);
      $formItemValue->setText($languageUtils->setTextForLanguage($formItemValue->getText(), $currentLanguageCode, $text));
      $formItemValueUtils->update($formItemValue);
    }

    $str = LibJavascript::autoCloseWindow();
    printContent($str);
    return;

  }

} else {

  $formItemValueId = LibEnv::getEnvHttpGET("formItemValueId");

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  $value = '';
  $text = '';
  if ($formItemValue = $formItemValueUtils->selectById($formItemValueId)) {
    $value = $formItemValue->getValue();
    $text = $languageUtils->getTextForLanguage($formItemValue->getText(), $currentLanguageCode);
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0]);
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);

$label = $popupUtils->getTipPopup($mlText[3], $mlText[14], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='value' value='$value' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[19], $mlText[20], 300, 300);
if ($dynpageUtils->useHtmlEditorInnova()) {
  $oInnovaContentName = "text";
  include($gInnovaHtmlEditorPath . "setupForm.php");
  $panelUtils->addContent($gInnovaHead);
  $strEditor = "<textarea id='$oInnovaContentName' name='$oInnovaContentName' cols='30' rows='5'>\n$text\n</textarea> $gInnovaBodyOpen $gInnovaBodyClose";
  $strJsEditor = <<<HEREDOC
<script type='text/javascript'>
function getContent() {
  var content = $oInnovaName.getHTMLBody();
  return(content);
}
function setContent(content) {
  $oInnovaName.putHTML(content);
}
$oInnovaName.onSave=new Function("saveInnovaEditorContent()");
function saveInnovaEditorContent() {
  var body = getContent();
  saveEditorContent("$oInnovaContentName", body);
}
</script>
HEREDOC;
} else {
  include($gHtmlEditorPath . "CKEditorUtils.php");
  $editorName = "text";
  $contentEditor = new CKEditorUtils();
  $contentEditor->languageUtils = $languageUtils;
  $contentEditor->commonUtils = $commonUtils;
  $contentEditor->load();
  $contentEditor->withReducedToolbar();
  $contentEditor->withAjaxSave();
  $contentEditor->setHeight(300);
  $strEditor = $contentEditor->render();
  $strEditor .= $contentEditor->renderInstance($editorName, $text);
  $strJsEditor = <<<HEREDOC
<script type='text/javascript'>
function getContent() {
  var editor = CKEDITOR.instances.$editorName;
  var content = editor.getData();
  return(content);
}
function setContent(content) {
  var editor = CKEDITOR.instances.$editorName;
  editor.setData(content);
}
</script>
HEREDOC;
}
$panelUtils->addHiddenField('currentLanguageCode', $currentLanguageCode);
$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strEditor . ' ' . $strLanguageFlag);
$strJsEditor .= <<<HEREDOC
<script type='text/javascript'>
function changeWebsiteLanguage(languageCode) {
  var url = '$gFormUrl/item/getItemValueText.php?formItemValueId=$formItemValueId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateItemValueText);
}
function updateItemValueText(responseText) {
  var response = eval('(' + responseText + ')');
  var text = response.text;
  setContent(text);
}
function saveEditorContent(editorName, content) {
  content = encodeURIComponent(content);
  var languageCode = document.getElementById('currentLanguageCode').value;
  var params = []; params["formItemValueId"] = "$formItemValueId"; params["languageCode"] = languageCode; params[editorName] = content;
  ajaxAsynchronousPOSTRequest("$gFormUrl/item/updateItemValue.php", params);
}
</script>
HEREDOC;
$panelUtils->addContent($strJsEditor);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('formItemValueId', $formItemValueId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
