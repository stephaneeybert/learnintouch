<?PHP

require_once("website.php");

LibHtml::preventCaching();


$shopCategoryId = LibEnv::getEnvHttpGET("shopCategoryId");
$targetId = LibEnv::getEnvHttpGET("targetId");

// An ajax request parameter value is UTF-8 encoded
$shopCategoryId = utf8_decode($shopCategoryId);
$targetId = utf8_decode($targetId);

$moved = false;

if ($shopCategory = $shopCategoryUtils->selectById($shopCategoryId)) {
  if ($targetId != $shopCategory->getParentCategoryId() && !$shopCategoryUtils->isGrandChildOf($targetId, $shopCategoryId)) {
    $moved = $shopCategoryUtils->placeAfter($shopCategoryId, $targetId);
  }
}

$responseText = <<<HEREDOC
{
"moved" : "$moved"
}
HEREDOC;

print($responseText);

?>
