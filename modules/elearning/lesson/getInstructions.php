<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

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
