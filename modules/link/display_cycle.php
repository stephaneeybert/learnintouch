<?PHP

require_once("website.php");

$linkCategoryId = LibEnv::getEnvHttpGET("linkCategoryId");

// Prevent sql injection attacks as the id is always numeric
$linkCategoryId = (int) $linkCategoryId;


$gTemplate->setPageContent($linkCategoryUtils->renderImageCycleInPage($linkCategoryId));

require_once($gTemplatePath . "render.php");

?>
