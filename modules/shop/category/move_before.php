<?PHP

require_once("website.php");

LibHtml::preventCaching();


$shopCategoryId = LibEnv::getEnvHttpGET("shopCategoryId");
$targetId = LibEnv::getEnvHttpGET("targetId");

$moved = false;

if ($shopCategory = $shopCategoryUtils->selectById($shopCategoryId)) {
  if ($targetId != $shopCategory->getParentCategoryId() && !$shopCategoryUtils->isGrandChildOf($targetId, $shopCategoryId)) {
    $moved = $shopCategoryUtils->placeBefore($shopCategoryId, $targetId);
  }
}

$responseText = <<<HEREDOC
{
"moved" : "$moved"
}
HEREDOC;

print($responseText);

?>
