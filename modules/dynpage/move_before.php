<?PHP

require_once("website.php");

LibHtml::preventCaching();


$dynpageId = LibEnv::getEnvHttpGET("dynpageId");
$targetId = LibEnv::getEnvHttpGET("targetId");

$moved = false;

if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
  if ($targetId != $dynpage->getParentId() && !$dynpageUtils->isGrandChildOf($targetId, $dynpageId)) {
    $moved = $dynpageUtils->placeBefore($dynpageId, $targetId);
  }
}

$responseText = <<<HEREDOC
{
"moved" : "$moved"
}
HEREDOC;

print($responseText);

?>
