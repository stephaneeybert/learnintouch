<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningAnswerId = LibEnv::getEnvHttpGET("elearningAnswerId");
$elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");

// An ajax request parameter value is UTF-8 encoded
$elearningAnswerId = utf8_decode($elearningAnswerId);
$elearningQuestionId = utf8_decode($elearningQuestionId);

$moved = false;

if ($elearningAnswer = $elearningAnswerUtils->selectById($elearningAnswerId)) {
  if ($elearningQuestionId != $elearningAnswer->getElearningQuestion()) {
    $elearningAnswerUtils->specifyAsNotSolution($elearningAnswerId);
    $elearningAnswer->setElearningQuestion($elearningQuestionId);
    $elearningAnswer->setListOrder($elearningAnswerUtils->getNextListOrder($elearningQuestionId));
    $elearningAnswerUtils->update($elearningAnswer);
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
