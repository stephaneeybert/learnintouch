<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");
$targetId = LibEnv::getEnvHttpGET("targetId");

// An ajax request parameter value is UTF-8 encoded
$elearningQuestionId = utf8_decode($elearningQuestionId);
$targetId = utf8_decode($targetId);

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
