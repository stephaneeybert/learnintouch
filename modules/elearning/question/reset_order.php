<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
$chronological = LibEnv::getEnvHttpGET("chronological");

$elearningQuestionUtils->resetListOrder($elearningExercisePageId, true, $chronological);

$str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
printMessage($str);
return;

?>
