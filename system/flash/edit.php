<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FLASH);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $flashId = LibEnv::getEnvHttpPOST("flashId");
  $width = LibEnv::getEnvHttpPOST("width");
  $height = LibEnv::getEnvHttpPOST("height");
  $bgcolor = LibEnv::getEnvHttpPOST("bgcolor");

  $width = LibString::cleanString($width);
  $height = LibString::cleanString($height);
  $bgcolor = LibString::cleanString($bgcolor);

  if ($flash = $flashUtils->selectById($flashId)) {
    $flash->setWidth($width);
    $flash->setHeight($height);
    $flash->setBgcolor($bgcolor);
    $flashUtils->update($flash);
    }

  $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
  printContent($str);
  return;

  } else {

  $flashId = LibEnv::getEnvHttpGET("flashId");

  $width = '';
  $height = '';
  $bgcolor = '';
  $file = '';
  if ($flash = $flashUtils->selectById($flashId)) {
    $width = $flash->getWidth();
    $height = $flash->getHeight();
    $bgcolor = $flash->getBgcolor();
    $file = $flash->getFile();
    }

  $panelUtils->setHeader($mlText[0]);
  $panelUtils->openForm($PHP_SELF, "edit");
  $label = $popupUtils->getTipPopup($mlText[1], $mlText[11], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "br"), "<input type='text' name='width' value='$width' size='12' maxlength='10'>");
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[2], $mlText[11], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "br"), "<input type='text' name='height' value='$height' size='12' maxlength='10'>");
  $panelUtils->addLine();
  $strColorPicker = "<a href=\"javascript:TCP.popup(document.forms['edit'].elements['bgcolor']);\"><img border='0' src='$gCommonImagesUrl/$gImageColorPicker' title='$mlText[7]'></a>";
  $label = $popupUtils->getTipPopup($mlText[3], $mlText[13], 300, 400);
  $panelUtils->addLine($panelUtils->addCell($label, "br"), "<input type='text' name='bgcolor' value='$bgcolor' size='12' maxlength='10'> $strColorPicker");
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[6], $mlText[16], 300, 200);
  $strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageFlash' title='$mlText[12]'>", "$gFlashUrl/file.php?flashId=$flashId", 600, 600);
  $panelUtils->addLine($panelUtils->addCell($label, "br"), "$strCommand $file");
  $panelUtils->addLine();
  $actionscript = $gFlashPath . "actionscript.zip";
  $url = "$gUtilsUrl/download.php?filename=$actionscript";
  $label = $popupUtils->getTipPopup($mlText[4], $mlText[5], 300, 300);
  $strCommand = " <a href='$url' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageDownload' title='$mlText[8]'></a>";
  $panelUtils->addLine($panelUtils->addCell($label, "br"), $strCommand);
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('flashId', $flashId);
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
  }

?>
