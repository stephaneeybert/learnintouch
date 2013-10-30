<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$elearningAnswerId = LibEnv::getEnvHttpGET("elearningAnswerId");

$elearningAnswerUtils->swapWithNext($elearningAnswerId);

$str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
printContent($str);
return;

?>
