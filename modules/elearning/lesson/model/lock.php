<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$elearningLessonModelId = LibEnv::getEnvHttpGET("elearningLessonModelId");
$locked = LibEnv::getEnvHttpGET("locked");

$adminLogin = $adminUtils->checkAdminLogin();
if ($adminUtils->isSuperAdmin($adminLogin)) {
  if ($elearningLessonModel = $elearningLessonModelUtils->selectById($elearningLessonModelId)) {
    $elearningLessonModel->setLocked($locked);
    $elearningLessonModelUtils->update($elearningLessonModel);
  }
}

$str = LibHtml::urlRedirect("$gElearningUrl/lesson/model/admin.php");
printContent($str);
return;

?>
