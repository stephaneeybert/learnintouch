<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

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
