<?PHP

require_once("website.php");

$elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");

$str = $elearningLessonUtils->printLesson($elearningLessonId);

print($templateUtils->renderPopup($str));

?>
