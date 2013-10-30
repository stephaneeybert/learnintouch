<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FORM);


$formId = LibEnv::getEnvHttpGET("formId");

$strCss = $templateUtils->renderPreviewCssProperties();

$str = $formUtils->render($formId);

printContent($str, $strCss);

?>
