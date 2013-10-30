<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
$targetId = LibEnv::getEnvHttpGET("targetId");

// An ajax request parameter value is UTF-8 encoded
$elearningExercisePageId = utf8_decode($elearningExercisePageId);
$targetId = utf8_decode($targetId);

$moved = false;

if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
  $moved = $elearningExercisePageUtils->placeAfter($elearningExercisePageId, $targetId);
}

$responseText = <<<HEREDOC
{
"moved" : "$moved"
}
HEREDOC;

print($responseText);

?>
