<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");

$elearningLessonUtils->restoreFromGarbage($elearningLessonId);

$str = LibHtml::urlRedirect("$gElearningUrl/lesson/garbage.php");
printMessage($str);
return;

?>
