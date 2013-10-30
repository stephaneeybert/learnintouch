<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
$locked = LibEnv::getEnvHttpGET("locked");

$adminLogin = $adminUtils->checkAdminLogin();
if ($adminUtils->isSuperAdmin($adminLogin)) {
  if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
    $elearningExercise->setLocked($locked);
    $elearningExerciseUtils->update($elearningExercise);
  }
}

$str = LibHtml::urlRedirect("$gElearningUrl/exercise/admin.php");
printContent($str);
return;

?>
