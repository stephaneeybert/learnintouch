<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$instructions = LibEnv::getEnvHttpPOST("instructions");

// An ajax request parameter value is UTF-8 encoded
$elearningExerciseId = utf8_decode($elearningExerciseId);
$instructions = utf8_decode($instructions);

if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $elearningExercise->setInstructions($languageUtils->setTextForLanguage($elearningExercise->getInstructions(), $languageCode, $instructions));
  $elearningExerciseUtils->update($elearningExercise);
}

$notused = '';

$responseText = <<<HEREDOC
{
"notused" : "$notused"
}
HEREDOC;

print($responseText);

?>
