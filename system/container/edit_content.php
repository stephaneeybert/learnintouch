<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

// Set a flag to request the update of the cache file
$templateUtils->setRefreshCache();

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $containerId = LibEnv::getEnvHttpPOST("containerId");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $content = LibEnv::getEnvHttpPOST("content");

  $currentLanguageCode = LibString::cleanString($currentLanguageCode);

  $content = LibString::cleanHtmlString($content);

  if ($container = $containerUtils->selectById($containerId)) {
    $mlText = $languageUtils->setTextForLanguage($container->getContent(), $currentLanguageCode, $content);
    $container->setContent($mlText);
    $containerUtils->update($container);
  }

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;

} else {

  $containerId = LibEnv::getEnvHttpGET("containerId");

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  $content = '';
  if ($container = $containerUtils->selectById($containerId)) {
    $content = $languageUtils->getTextForLanguage($container->getContent(), $currentLanguageCode);
  }

  $panelUtils->setHeader($mlText[0]);
  $panelUtils->openForm($PHP_SELF);
  if ($dynpageUtils->useHtmlEditorInnova()) {
    $oInnovaContentName = "content";
    include($gInnovaHtmlEditorPath . "setupContainer.php");
    $panelUtils->addContent($gInnovaHead);
    $strEditor = "<textarea id='$oInnovaContentName' name='$oInnovaContentName' cols='30' rows='5'>\n$content\n</textarea> $gInnovaBodyOpen $gInnovaBodyClose";
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
    $editorName = "content";
    $contentEditor = new CKEditorUtils();
    $contentEditor->languageUtils = $languageUtils;
    $contentEditor->commonUtils = $commonUtils;
    $contentEditor->load();
    $contentEditor->setImagePath($templateUtils->imagePath);
    $contentEditor->setImageUrl($templateUtils->imageUrl);
    $contentEditor->setImageBrowserUploadUrl($gSystemUrl . '/editor/ckeditor/connector/image_container.php');
    $contentEditor->withReducedToolbar();
    $contentEditor->withImageButton();
    $contentEditor->withAjaxSave();
    $contentEditor->setHeight(300);
    $strEditor = $contentEditor->render();
    $strEditor .= $contentEditor->renderInstance($editorName, $content);
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
  $panelUtils->addLine($panelUtils->addCell($strEditor . ' ' . $strLanguageFlag, 'c'));
  $strJsEditor .= <<<HEREDOC
<script type='text/javascript'>
function changeWebsiteLanguage(languageCode) {
  var url = '$gContainerUrl/getContent.php?containerId=$containerId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateContent);
}
function updateContent(responseText) {
  var response = eval('(' + responseText + ')');
  var content = response.content;
  setContent(content);
}
function saveEditorContent(editorName, content) {
  editorName = encodeURIComponent(editorName);
  content = encodeURIComponent(content);
  var languageCode = document.getElementById('currentLanguageCode').value;
  var params = []; params["containerId"] = "$containerId"; params["languageCode"] = languageCode; params[editorName] = content;
  ajaxAsynchronousPOSTRequest("$gContainerUrl/update.php", params);
}
</script>
HEREDOC;
  $panelUtils->addContent($strJsEditor);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($panelUtils->getOk(), 'c'));
  $panelUtils->addHiddenField('containerId', $containerId);
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();

  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
