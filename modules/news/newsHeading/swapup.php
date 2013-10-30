<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$newsHeadingId = LibEnv::getEnvHttpGET("newsHeadingId");

// Swap the element with the previous one
$newsHeadingUtils->swapWithPrevious($newsHeadingId);

$str = LibHtml::urlRedirect("$gNewsUrl/newsHeading/admin.php");
printContent($str);
return;

?>
