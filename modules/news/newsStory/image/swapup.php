<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$newsStoryImageId = LibEnv::getEnvHttpGET("newsStoryImageId");

$newsStoryImageUtils->swapWithPrevious($newsStoryImageId);

$str = LibHtml::urlRedirect("$gNewsUrl/newsStory/image/admin.php");
printContent($str);
return;

?>
