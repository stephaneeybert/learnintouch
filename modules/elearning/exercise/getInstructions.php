<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $instructions = $languageUtils->getTextForLanguage($elearningExercise->getInstructions(), $languageCode);
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
