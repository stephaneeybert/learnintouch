<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningLessonModelId = LibEnv::getEnvHttpGET("elearningLessonModelId");

$elearningLessonHeadingId = $elearningLessonHeadingUtils->add($elearningLessonModelId);

$notused = '';

$responseText = <<<HEREDOC
{
"elearningLessonHeadingId" : "$elearningLessonHeadingId"
}
HEREDOC;

print($responseText)

?>
