<?PHP

require_once("website.php");

LibHtml::preventCaching();


$dynpageId = LibEnv::getEnvHttpGET("dynpageId");
$parentId = LibEnv::getEnvHttpGET("parentId");

// An ajax request parameter value is UTF-8 encoded
$dynpageId = utf8_decode($dynpageId);
$parentId = utf8_decode($parentId);

$moved = false;

if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
  if ($parentId != $dynpage->getParentId() && !$dynpageUtils->isGrandChildOf($parentId, $dynpageId)) {
    $dynpage->setParentId($parentId);
    $dynpage->setListOrder($dynpageUtils->getNextListOrder($parentId));
    $dynpageUtils->update($dynpage);
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
