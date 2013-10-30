<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");
$locked = LibEnv::getEnvHttpGET("locked");

$adminLogin = $adminUtils->checkAdminLogin();
if ($adminUtils->isSuperAdmin($adminLogin)) {
  if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
    $elearningCourse->setLocked($locked);
    $elearningCourseUtils->update($elearningCourse);
  }
}

$str = LibHtml::urlRedirect("$gElearningUrl/course/admin.php");
printContent($str);
return;

?>
