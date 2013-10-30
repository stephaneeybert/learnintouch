<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningLessonModelId = LibEnv::getEnvHttpGET("elearningLessonModelId");

// An ajax request parameter value is UTF-8 encoded
$elearningLessonModelId = utf8_decode($elearningLessonModelId);

$elearningLessonHeadingId = $elearningLessonHeadingUtils->add($elearningLessonModelId);

$notused = '';

$responseText = <<<HEREDOC
{
"elearningLessonHeadingId" : "$elearningLessonHeadingId"
}
HEREDOC;

print($responseText)

?>
