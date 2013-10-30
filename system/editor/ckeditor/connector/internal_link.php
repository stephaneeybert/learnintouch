<?php

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $webpageId = LibEnv::getEnvHttpPOST("webpageId");
  $newWindow = LibEnv::getEnvHttpPOST("newWindow");
  $CKEditorFuncNum = LibEnv::getEnvHttpPOST("CKEditorFuncNum");
  $templateModelId = LibEnv::getEnvHttpPOST("templateModelId");

  $internalUrl = $templateUtils->renderPageUrl($webpageId, $templateModelId);

  $str = <<<HEREDOC
<script type='text/javascript'>
  var internalUrl = '$internalUrl';
  window.opener.CKEDITOR.tools.callFunction('$CKEditorFuncNum', internalUrl, '$newWindow');
</script>
HEREDOC;
  printMessage($str);

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;

} else {

  $CKEditorFuncNum = LibEnv::getEnvHttpGET("CKEditorFuncNum");
  if (!$CKEditorFuncNum) {
    $CKEditorFuncNum = LibEnv::getEnvHttpPOST("CKEditorFuncNum");
  }

  $modelList = $templateModelUtils->getAllModels();
  $strSelectModel = LibHtml::getSelectList("templateModelId", $modelList);

  $panelUtils->setHeader($mlText[3]);
  $panelUtils->openForm($PHP_SELF, "edit");
  $strLinkPopup = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageSelect' title='$mlText[7]'>", "$gTemplateUrl/select.php", 600, 600);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' id='webpageName' name='webpageName' size='30' maxlength='50'> $strLinkPopup");
  $panelUtils->addHiddenField('webpageId', '');
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[8], $mlText[9], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectModel);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='checkbox' name='newWindow' value='1'>");
  $panelUtils->addHiddenField('CKEditorFuncNum', $CKEditorFuncNum);
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->closeForm();

  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
