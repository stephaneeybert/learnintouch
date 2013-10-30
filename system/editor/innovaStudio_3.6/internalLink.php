<?php

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $webpageId = LibEnv::getEnvHttpPOST("webpageId");
  $newWindow = LibEnv::getEnvHttpPOST("newWindow");

  $internalUrl = $templateUtils->renderPageUrl($webpageId);

  $str = <<<HEREDOC
<script type='text/javascript'>

function getEditor() {
  if (navigator.appName.indexOf('Microsoft') != -1) {
    var oName = window.opener.oUtil.oName;
    oEditor = eval("window.opener." + oName);
    } else {
    var oName = window.opener.oUtil.oName;
    oEditor = eval("window.opener." + oName);
    }

  return(oEditor);
  }

function insertLink(url, target) {
  oEditor = getEditor();
  oEditor.insertLink(url, '', target);
  }

function insertInternalLink() {
  var internalUrl = '$internalUrl';

  if ("$newWindow" == "1") {
    target = '_blank';
    } else {
    target = '_self';
    }

  insertLink(internalUrl, target);
  }

insertInternalLink();

</script>
HEREDOC;

  printMessage($str);

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;

  } else {

  $panelUtils->setHeader($mlText[3]);
  $panelUtils->openForm($PHP_SELF, "edit");
  $strLinkPopup = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageSelect' title='$mlText[7]'>", "$gTemplateUrl/select.php", 600, 600);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' id='webpageName' name='webpageName' size='30' maxlength='50'> $strLinkPopup");
  $panelUtils->addHiddenField('webpageId', '');
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='checkbox' name='newWindow' value='1'>");
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->closeForm();

  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
