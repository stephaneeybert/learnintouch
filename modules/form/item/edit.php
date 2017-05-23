<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FORM);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $formItemId = LibEnv::getEnvHttpPOST("formItemId");
  $formId = LibEnv::getEnvHttpPOST("formId");
  $type = LibEnv::getEnvHttpPOST("type");
  $name = LibEnv::getEnvHttpPOST("name");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $text = LibEnv::getEnvHttpPOST("text");
  $help = LibEnv::getEnvHttpPOST("help");
  $defaultValue = LibEnv::getEnvHttpPOST("defaultValue");
  $size = LibEnv::getEnvHttpPOST("size");
  $maxlength = LibEnv::getEnvHttpPOST("maxlength");
  $inMailAddress = LibEnv::getEnvHttpPOST("inMailAddress");
  $mailListId = LibEnv::getEnvHttpPOST("mailListId");
  $reloadPage = LibEnv::getEnvHttpPOST("reloadPage");

  $type = LibString::cleanString($type);
  $name = LibString::cleanString($name);
  $currentLanguageCode = LibString::cleanString($currentLanguageCode);
  $help = LibString::cleanString($help);
  $defaultValue = LibString::cleanString($defaultValue);
  $size = LibString::cleanString($size);
  $maxlength = LibString::cleanString($maxlength);
  $inMailAddress = LibString::cleanString($inMailAddress);
  $reloadPage = LibString::cleanString($reloadPage);

  // The name is most often required
  if ($type != 'FORM_ITEM_SUBMIT' && $type != 'FORM_ITEM_SECURE_CODE' && !$name && $type != 'FORM_ITEM_COMMENT') {
    array_push($warnings, $mlText[6]);
  }

  $name = LibString::stringToAlphanum($name);

  $text = LibString::cleanHtmlString($text);

  if (count($warnings) == 0) {

    if ($formItem = $formItemUtils->selectById($formItemId)) {
      $formItem->setType($type);
      $formItem->setName($name);
      $formItem->setText($languageUtils->setTextForLanguage($formItem->getText(), $currentLanguageCode, $text));
      $formItem->setHelp($help);
      $formItem->setDefaultValue($defaultValue);
      $formItem->setSize($size);
      $formItem->setMaxlength($maxlength);
      $formItem->setInMailAddress($inMailAddress);
      $formItem->setMailListId($mailListId);
      $formItemUtils->update($formItem);

      if ($reloadPage) {
        $str = LibHtml::urlRedirect("$gFormUrl/item/edit.php?formItemId=$formItemId");
        printContent($str);
        exit;
      }

    } else {
      $formItem = new FormItem();
      $formItem->setType($type);
      $formItem->setName($name);
      $formItem->setText($languageUtils->setTextForLanguage('', $currentLanguageCode, $text));
      $formItem->setHelp($help);
      $formItem->setDefaultValue($defaultValue);
      $formItem->setSize($size);
      $formItem->setMaxlength($maxlength);
      // Get the next list order
      $listOrder = $formItemUtils->getNextListOrder($formId);
      $formItem->setListOrder($listOrder);
      $formItem->setInMailAddress($inMailAddress);
      $formItem->setMailListId($mailListId);
      $formItem->setFormId($formId);
      $formItemUtils->insert($formItem);
      $formItemId = $formItemUtils->getLastInsertId();
    }

    $str = LibHtml::urlRedirect("$gFormUrl/item/admin.php");
    printContent($str);
    exit;

  }

} else {

  $formItemId = LibEnv::getEnvHttpGET("formItemId");
  $formId = LibEnv::getEnvHttpGET("formId");

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  // Add or delete a value
  $formItemValueId = LibEnv::getEnvHttpGET("formItemValueId");
  $addValue = LibEnv::getEnvHttpGET("addValue");
  $deleteValue = LibEnv::getEnvHttpGET("deleteValue");

  if ($addValue && $formItemId) {
    $formItemValueUtils->add($formItemId);

    $str = LibHtml::urlRedirect("$PHP_SELF?formItemId=$formItemId");
    printContent($str);
    exit;
  } else if ($deleteValue && $formItemValueId) {
    $formItemValueUtils->deleteFormItemValue($formItemValueId);
  }

  $type = '';
  $name = '';
  $text = '';
  $help = '';
  $defaultValue = '';
  $size = '';
  $maxlength = '';
  $inMailAddress = '';
  $mailListId = '';
  $mailListName = '';
  if ($formItem = $formItemUtils->selectById($formItemId)) {
    $type = $formItem->getType();
    $name = $formItem->getName();
    $text = $languageUtils->getTextForLanguage($formItem->getText(), $currentLanguageCode);
    $help = $formItem->getHelp();
    $defaultValue = $formItem->getDefaultValue();
    $size = $formItem->getSize();
    $maxlength = $formItem->getMaxlength();
    $inMailAddress = $formItem->getInMailAddress();
    $mailListId = $formItem->getMailListId();
  }

}

$formItemTypeList = Array();
foreach ($gFormItemTypes as $formItemType => $formItemTypeName) {
  $formItemTypeList[$formItemType] = $formItemTypeName;
}
$strSelectFormItemType = LibHtml::getSelectList("type", $formItemTypeList, $type, false, 1, "submitAndReload();");
$strJsSubmitAndReload = <<<HEREDOC
<script type='text/javascript'>
function submitAndReload() {
  document.getElementById('reloadPage').value = '1';
  document.forms['edit'].submit();
}
</script>
HEREDOC;

if ($inMailAddress == '1') {
  $checkedInMailAddress = "CHECKED";
} else {
  $checkedInMailAddress = '';
}

$mailListName = '';
if ($mailListId) {
  if ($mailList = $mailListUtils->selectById($mailListId)) {
    $mailListName = $mailList->getName();
  }
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gFormUrl/item/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, 'edit');
$label = $popupUtils->getTipPopup($mlText[5], $mlText[9], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectFormItemType);
$panelUtils->addContent($strJsSubmitAndReload);
$panelUtils->addHiddenField('reloadPage', '');
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[19], $mlText[20], 300, 200);
include($gHtmlEditorPath . "CKEditorUtils.php");
$editorName = "text";
$contentEditor = new CKEditorUtils();
$contentEditor->languageUtils = $languageUtils;
$contentEditor->commonUtils = $commonUtils;
$contentEditor->load();
$contentEditor->withReducedToolbar();
$contentEditor->withAjaxSave();
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
$panelUtils->addHiddenField('currentLanguageCode', $currentLanguageCode);
$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strEditor . ' ' . $strLanguageFlag);
$strJsEditor .= <<<HEREDOC
<script type='text/javascript'>
function changeWebsiteLanguage(languageCode) {
  var url = '$gFormUrl/item/getItemText.php?formItemId=$formItemId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateItemText);
}
function updateItemText(responseText) {
  var response = eval('(' + responseText + ')');
  var text = response.text;
  setContent(text);
}
function saveEditorContent(editorName, content) {
  content = encodeURIComponent(content);
  var languageCode = document.getElementById('currentLanguageCode').value;
  var params = []; params["formItemId"] = "$formItemId"; params["languageCode"] = languageCode; params[editorName] = content;
  ajaxAsynchronousPOSTRequest("$gFormUrl/item/updateItem.php", params);
}
</script>
HEREDOC;
$panelUtils->addContent($strJsEditor);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[4], $mlText[10], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[11], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='help' value='$help' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[2], $mlText[12], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='defaultValue' value='$defaultValue' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[15], $mlText[16], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='size' value='$size' size='4' maxlength='3'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[17], $mlText[18], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='maxlength' value='$maxlength' size='5' maxlength='4'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[24], $mlText[25], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='inMailAddress' $checkedInMailAddress value='1'>");
$panelUtils->addLine();
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gMailUrl/list/suggestLists.php", "mailListName", "mailListId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('mailListId', $mailListId);
$label = $popupUtils->getTipPopup($mlText[21], $mlText[22], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='mailListName' value='$mailListName' size='30' />");
$panelUtils->addLine();

$formItemValues = $formItemValueUtils->selectByFormItemId($formItemId);

$strAddValue = "<a href='$gFormUrl/item/edit.php?formItemId=$formItemId&addValue=1' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[28]'></a>";

if ($formItemUtils->isListType($type)) {
  $label = $popupUtils->getTipPopup($mlText[13], $mlText[8], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strAddValue);
  $panelUtils->addLine();

  $strConfirmDelete = <<<HEREDOC
<script type='text/javascript'>
function confirmDelete() {
  confirmation = confirm('$mlText[30]');
  if (confirmation) {
    return(true);
  }

  return(false);
}
</script>
HEREDOC;
  $panelUtils->addContent($strConfirmDelete);

  for ($i = 0; $i < count($formItemValues); $i++) {
    $formItemValue = $formItemValues[$i];
    $formItemValueId = $formItemValue->getId();
    $value = $formItemValue->getValue();

    $strDeleteValue = "<a href='$gFormUrl/item/edit.php?formItemId=$formItemId&deleteValue=1&formItemValueId=$formItemValueId' onclick='javascript:return(confirmDelete(this))' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[29]' /></a>";

    $strLine = "<input type='text' name='value$formItemValueId' value='$value' size='30' maxlength='50'>";

    // The one and only value cannot be deleted
    if (count($formItemValues) > 1) {
      $strLine .= ' ' . $strDeleteValue;
    }

    $label = $popupUtils->getTipPopup($mlText[3], $mlText[14], 300, 300);
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strLine);

    $strEdit = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[26]'>", "$gFormUrl/item/editValue.php?formItemValueId=$formItemValueId", 600, 600);

    $label = $popupUtils->getTipPopup($mlText[7], $mlText[14], 300, 300);
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strEdit);
    $panelUtils->addLine();
  }
} else {
  // Prevent the erasing of the values when changing the type to a non list one
  for ($i = 0; $i < count($formItemValues); $i++) {
    $formItemValue = $formItemValues[$i];
    $formItemValueId = $formItemValue->getId();
    $value = $formItemValue->getValue();
    $text = $languageUtils->getTextForLanguage($formItemValue->getText(), $currentLanguageCode);
    $panelUtils->addHiddenField('value$formItemValueId', $value);
    $panelUtils->addHiddenField('text$formItemValueId', $text);
  }
}

$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('formItemId', $formItemId);
$panelUtils->addHiddenField('formId', $formId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
