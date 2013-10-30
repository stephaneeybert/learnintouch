<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");
$locked = LibEnv::getEnvHttpGET("locked");

$adminLogin = $adminUtils->checkAdminLogin();
if ($adminUtils->isSuperAdmin($adminLogin)) {
  if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
    $elearningLesson->setLocked($locked);
    $elearningLessonUtils->update($elearningLesson);
  }
}

$str = LibHtml::urlRedirect("$gElearningUrl/lesson/admin.php");
printContent($str);
return;

?>
