<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

$elearningExerciseUtils->restoreFromGarbage($elearningExerciseId);

$str = LibHtml::urlRedirect("$gElearningUrl/exercise/garbage.php");
printMessage($str);
return;

?>
