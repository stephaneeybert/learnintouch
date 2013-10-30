<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$elearningAnswerId = LibEnv::getEnvHttpGET("elearningAnswerId");
$notSolution = LibEnv::getEnvHttpGET("notSolution");

if ($notSolution) {
  $elearningAnswerUtils->specifyAsNotSolution($elearningAnswerId);
} else {
  $elearningAnswerUtils->specifyAsSolution($elearningAnswerId);
}

$str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
printMessage($str);
return;

?>
