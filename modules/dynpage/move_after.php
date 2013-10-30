<?PHP

require_once("website.php");

LibHtml::preventCaching();


$dynpageId = LibEnv::getEnvHttpGET("dynpageId");
$targetId = LibEnv::getEnvHttpGET("targetId");

// An ajax request parameter value is UTF-8 encoded
$dynpageId = utf8_decode($dynpageId);
$targetId = utf8_decode($targetId);

$moved = false;

if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
  if ($targetId != $dynpage->getParentId() && !$dynpageUtils->isGrandChildOf($targetId, $dynpageId)) {
    $moved = $dynpageUtils->placeAfter($dynpageId, $targetId);
  }
}

$responseText = <<<HEREDOC
{
"moved" : "$moved"
}
HEREDOC;

print($responseText);

?>
