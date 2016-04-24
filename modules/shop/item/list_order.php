<?PHP

require_once("website.php");

LibHtml::preventCaching();


$shopItemIds = LibEnv::getEnvHttpPOST("shopItemIds");

$listOrder = 1;
foreach ($shopItemIds as $shopItemId) {
  if ($shopItem = $shopItemUtils->selectById($shopItemId)) {
    $shopItem->setListOrder($listOrder);
    $shopItemUtils->update($shopItem);
    $listOrder++;
  }
}

?>
