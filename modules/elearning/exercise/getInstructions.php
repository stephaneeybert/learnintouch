<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

// An ajax request parameter value is UTF-8 encoded
$elearningExerciseId = utf8_decode($elearningExerciseId);
$languageCode = utf8_decode($languageCode);

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
