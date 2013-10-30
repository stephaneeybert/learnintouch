<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");

$strHead = $templateUtils->renderPreviewCssProperties();

$str = $templateUtils->renderCommonJavascripts();

$str .= $newsPaperUtils->render($newsPaperId);

$strCss = $templateUtils->renderPreviewCssProperties();

printContent($str, $strCss);

?>
