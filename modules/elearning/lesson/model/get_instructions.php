<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningLessonModelId = LibEnv::getEnvHttpGET("elearningLessonModelId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

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
