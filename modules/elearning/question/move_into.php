<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");
$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");

// An ajax request parameter value is UTF-8 encoded
$elearningQuestionId = utf8_decode($elearningQuestionId);
$elearningExercisePageId = utf8_decode($elearningExercisePageId);

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
