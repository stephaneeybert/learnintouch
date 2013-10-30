<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

// An ajax request parameter value is UTF-8 encoded
$elearningLessonId = utf8_decode($elearningLessonId);
$languageCode = utf8_decode($languageCode);

if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
  $instructions = $languageUtils->getTextForLanguage($elearningLesson->getInstructions(), $languageCode);
  $instructions = LibString::jsonEscapeLinebreak($instructions);
  $instructions = LibString::escapeDoubleQuotes($instructions);
} else {
  $instructions = '';
}

$responseText = <<<HEREDOC
{
"instructions" : "$instructions"
}
HEREDOC;

print($responseText);

?>
