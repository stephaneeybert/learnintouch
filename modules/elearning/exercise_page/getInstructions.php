<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

// An ajax request parameter value is UTF-8 encoded
$elearningExercisePageId = utf8_decode($elearningExercisePageId);
$languageCode = utf8_decode($languageCode);

if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
  $instructions = $languageUtils->getTextForLanguage($elearningExercisePage->getInstructions(), $languageCode);
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
