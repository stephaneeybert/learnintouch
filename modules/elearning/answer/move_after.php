<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningAnswerId = LibEnv::getEnvHttpGET("elearningAnswerId");
$targetId = LibEnv::getEnvHttpGET("targetId");

$moved = false;

if ($elearningAnswer = $elearningAnswerUtils->selectById($elearningAnswerId)) {
  $moved = $elearningAnswerUtils->placeAfter($elearningAnswerId, $targetId);
}

$responseText = <<<HEREDOC
{
"moved" : "$moved"
}
HEREDOC;

print($responseText);

?>
