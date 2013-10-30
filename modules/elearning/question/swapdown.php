<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");

$elearningQuestionUtils->swapWithNext($elearningQuestionId);

$str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
printMessage($str);
return;

?>
