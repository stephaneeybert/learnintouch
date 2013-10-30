<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningLessonModelId = LibEnv::getEnvHttpGET("elearningLessonModelId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

// An ajax request parameter value is UTF-8 encoded
$elearningLessonModelId = utf8_decode($elearningLessonModelId);
$languageCode = utf8_decode($languageCode);

if ($elearningLessonModel = $elearningLessonModelUtils->selectById($elearningLessonModelId)) {
  $instructions = $languageUtils->getTextForLanguage($elearningLessonModel->getInstructions(), $languageCode);
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
