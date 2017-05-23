<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FORM);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $formValidId = LibEnv::getEnvHttpPOST("formValidId");
  $formItemId = LibEnv::getEnvHttpPOST("formItemId");
  $type = LibEnv::getEnvHttpPOST("type");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $message = LibEnv::getEnvHttpPOST("message");
  $boundary = LibEnv::getEnvHttpPOST("boundary");

  $type = LibString::cleanString($type);
  $boundary = LibString::cleanString($boundary);
  $currentLanguageCode = LibString::cleanString($currentLanguageCode);

  // The type is required
  if (!$type) {
    array_push($warnings, $mlText[6]);
  }

  $message = LibString::cleanHtmlString($message);

  if (count($warnings) == 0) {

    if ($formValid = $formValidUtils->selectById($formValidId)) {
      $formValid->setType($type);
      $formValid->setMessage($languageUtils->setTextForLanguage($formValid->getMessage(), $currentLanguageCode, $message));
      $formValid->setBoundary($boundary);
      $formValid->setFormItemId($formItemId);
      $formValidUtils->update($formValid);
    } else {
      $formValid = new FormValid();
      $formValid->setType($type);
      $formValid->setMessage($languageUtils->setTextForLanguage('', $currentLanguageCode, $message));
      $formValid->setBoundary($boundary);
      $formValid->setFormItemId($formItemId);
      $formValidUtils->insert($formValid);
    }

    $str = LibHtml::urlRedirect("$gFormUrl/valid/admin.php");
    printContent($str);
    exit;

  }

} else {

  $formValidId = LibEnv::getEnvHttpGET("formValidId");
  $formItemId = LibEnv::getEnvHttpGET("formItemId");

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  $type = '';
  $message = '';
  $boundary = '';
  if ($formValidId) {
    if ($formValid = $formValidUtils->selectById($formValidId)) {
      $type = $formValid->getType();
      $message = $languageUtils->getTextForLanguage($formValid->getMessage(), $currentLanguageCode);
      $boundary = $formValid->getBoundary();
      $formItemId = $formValid->getFormItemId();
    }
  }

}

$formValidTypeList = Array();
foreach ($gFormValidTypes as $formValidType => $formValidTypeName) {
  $formValidTypeList[$formValidType] = $formValidTypeName;
}
$strSelectFormValidType = LibHtml::getSelectList("type", $formValidTypeList, $type);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gFormUrl/valid/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $strSelectFormValidType);
$panelUtils->addLine();
include($gHtmlEditorPath . "CKEditorUtils.php");
$editorName = "message";
$contentEditor = new CKEditorUtils();
$contentEditor->languageUtils = $languageUtils;
$contentEditor->commonUtils = $commonUtils;
$contentEditor->load();
$contentEditor->withReducedToolbar();
$contentEditor->withAjaxSave();
$strEditor = $contentEditor->render();
$strEditor .= $contentEditor->renderInstance($editorName, $message);
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
$panelUtils->addHiddenField('currentLanguageCode', $currentLanguageCode);
$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $strEditor . ' ' . $strLanguageFlag);
$strJsEditor .= <<<HEREDOC
<script type='text/javascript'>
function changeWebsiteLanguage(languageCode) {
  var url = '$gFormUrl/valid/getMessage.php?formValidId=$formValidId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateMessage);
}
function updateMessage(responseText) {
  var response = eval('(' + responseText + ')');
  var message = response.message;
  setContent(message);
}
function saveEditorContent(editorName, content) {
  content = encodeURIComponent(content);
  var languageCode = document.getElementById('currentLanguageCode').value;
  var params = []; params["formValidId"] = "$formValidId"; params["languageCode"] = languageCode; params[editorName] = content;
  ajaxAsynchronousPOSTRequest("$gFormUrl/valid/update.php", params);
}
</script>
HEREDOC;
$panelUtils->addContent($strJsEditor);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[3], $mlText[4], 300, 400);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='message' name='boundary' value='$boundary' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('formValidId', $formValidId);
$panelUtils->addHiddenField('formItemId', $formItemId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
