<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");

$elearningExercisePageUtils->swapWithNext($elearningExercisePageId);

$str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
printContent($str);
return;

?>
