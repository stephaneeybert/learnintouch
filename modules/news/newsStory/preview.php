<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);


$newsStoryId = LibEnv::getEnvHttpGET("newsStoryId");

$strCss = $templateUtils->renderPreviewCssProperties();

$str = $newsStoryUtils->renderNewsStory($newsStoryId);

printContent($str, $strCss);

?>
