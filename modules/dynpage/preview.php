<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);


$dynpageId = LibEnv::getEnvHttpGET("dynpageId");

$strCss = $templateUtils->renderPreviewCssProperties();

$str = $templateUtils->renderCommonJavascripts();

if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
  $str .= $dynpageUtils->render($dynpage);
}

printContent($str, $strCss);

?>
