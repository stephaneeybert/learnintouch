<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$newsStoryId = LibEnv::getEnvHttpGET("newsStoryId");

$newsStoryUtils->swapWithPrevious($newsStoryId);

$str = LibHtml::urlRedirect("$gNewsUrl/newsStory/admin.php");
printContent($str);
return;

?>
