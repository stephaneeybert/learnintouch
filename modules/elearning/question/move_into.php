<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");
$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");

$moved = false;

if ($elearningQuestion = $elearningQuestionUtils->selectById($elearningQuestionId)) {
  if ($elearningExercisePageId != $elearningQuestion->getElearningExercisePage()) {
    $elearningQuestion->setElearningExercisePage($elearningExercisePageId);
    $elearningQuestion->setListOrder($elearningQuestionUtils->getNextListOrder($elearningExercisePageId));
    $elearningQuestionUtils->update($elearningQuestion);
    $moved = true;
  }
}

$responseText = <<<HEREDOC
{
"moved" : "$moved"
}
HEREDOC;

print($responseText);

?>
