<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$templatePageId = LibEnv::getEnvHttpPOST("templatePageId");

if ($templatePageId) {

  $templateModelId = LibEnv::getEnvHttpPOST("templateModelId");

  $templatePageUtils->deleteTags($templatePageId);

  $str = LibHtml::urlRedirect("$gTemplateUrl/design/page/admin.php?templateModelId=$templateModelId");
  printMessage($str);
  return;
  }

$templateModelId = LibEnv::getEnvHttpGET("templateModelId");
$systemPage = LibEnv::getEnvHttpGET("systemPage");

$templatePageId = '';
$description = '';
if ($templatePage = $templatePageUtils->selectByTemplateModelIdAndSystemPage($templateModelId, $systemPage)) {
  $templatePageId = $templatePage->getId();
  $systemPage = $templatePage->getSystemPage();

  $systemPages = $templatePageUtils->getPageList();
  $description = $systemPages[$systemPage];
  }

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/design/page/admin.php?templateModelId=$templateModelId");
$panelUtils->addLine();
if ($description) {
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "br"), $description);
} else {
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "wr"), '');
}
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine('', "<input type='image' border='0' src='$gCommonImagesUrl/$gImageOk' title='$mlText[2]'>");
$panelUtils->addHiddenField('templatePageId', $templatePageId);
$panelUtils->addHiddenField('templateModelId', $templateModelId);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
