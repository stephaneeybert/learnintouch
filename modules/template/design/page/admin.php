<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$templateModelId = LibEnv::getEnvHttpGET("templateModelId");

if (!$templateModelId) {
  $templateModelId = $templateUtils->getCurrentModel();
} else {
  $templateUtils->setCurrentModel($templateModelId);
}

$systemPages = $templatePageUtils->getPageList();

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/design/model/compose.php?templateModelId=$templateModelId");
foreach ($systemPages as $systemPage => $description) {
  $str = "<a href='$gTemplateDesignUrl/page/deleteProperty.php?systemPage=$systemPage&templateModelId=$templateModelId' $gJSNoStatus title='$mlText[4]'>"
    . " <img border='0' src='$gCommonImagesUrl/$gImageDelete' title=''></a>"
    . " <a href='$gTemplateDesignUrl/page/editProperty.php?systemPage=$systemPage&templateModelId=$templateModelId' $gJSNoStatus title='$mlText[1]'>"
    . " <img border='0' src='$gCommonImagesUrl/$gImageProperty' title=''> $description</a>";
  $panelUtils->addLine($str);
}

$str = $panelUtils->render();

printAdminPage($str);

?>
