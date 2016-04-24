<?PHP

require_once("website.php");

LibHtml::preventCaching();


$shopCategoryId = LibEnv::getEnvHttpGET("shopCategoryId");
$parentId = LibEnv::getEnvHttpGET("parentId");

$moved = false;

if ($shopCategory = $shopCategoryUtils->selectById($shopCategoryId)) {
  if ($parentId != $shopCategory->getParentCategoryId() && !$shopCategoryUtils->isGrandChildOf($parentId, $shopCategoryId)) {
    $shopCategory->setParentCategoryId($parentId);
    $shopCategory->setListOrder($shopCategoryUtils->getNextListOrder($parentId));
    $shopCategoryUtils->update($shopCategory);
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
