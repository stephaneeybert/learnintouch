<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");
$targetId = LibEnv::getEnvHttpGET("targetId");

$moved = false;

if ($elearningQuestion = $elearningQuestionUtils->selectById($elearningQuestionId)) {
  $moved = $elearningQuestionUtils->placeBefore($elearningQuestionId, $targetId);
}

$responseText = <<<HEREDOC
{
"moved" : "$moved"
}
HEREDOC;

print($responseText);

?>
