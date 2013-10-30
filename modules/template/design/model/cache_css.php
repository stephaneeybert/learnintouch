<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

LibHtml::preventCaching();

$templateModelId = LibEnv::getEnvHttpGET("templateModelId");

if ($templateModel = $templateModelUtils->selectById($templateModelId)) {
  $templateUtils->cacheModelFile($templateModelId);

  $templateModelUtils->cacheCssFile($templateModelId);

  print($templateModelId);
}

?>
