<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$instructions = LibEnv::getEnvHttpPOST("instructions");

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
