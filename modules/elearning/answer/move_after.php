<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningAnswerId = LibEnv::getEnvHttpGET("elearningAnswerId");
$targetId = LibEnv::getEnvHttpGET("targetId");

// An ajax request parameter value is UTF-8 encoded
$elearningAnswerId = utf8_decode($elearningAnswerId);
$targetId = utf8_decode($targetId);

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
