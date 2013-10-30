<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$instructions = LibEnv::getEnvHttpPOST("instructions");

// An ajax request parameter value is UTF-8 encoded
$elearningExercisePageId = utf8_decode($elearningExercisePageId);
$instructions = utf8_decode($instructions);

if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
  $elearningExercisePage->setInstructions($languageUtils->setTextForLanguage($elearningExercisePage->getInstructions(), $languageCode, $instructions));
  $elearningExercisePageUtils->update($elearningExercisePage);
}

$notused = '';

$responseText = <<<HEREDOC
{
"notused" : "$notused"
}
HEREDOC;

print($responseText);

?>
