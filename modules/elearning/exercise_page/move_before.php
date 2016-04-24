<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
$targetId = LibEnv::getEnvHttpGET("targetId");

$moved = false;

if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
  $moved = $elearningExercisePageUtils->placeBefore($elearningExercisePageId, $targetId);
}

$responseText = <<<HEREDOC
{
"moved" : "$moved"
}
HEREDOC;

print($responseText);

?>
