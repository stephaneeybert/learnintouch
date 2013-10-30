<?PHP

require_once("website.php");

LibHtml::preventCaching();


$shopItemIds = LibEnv::getEnvHttpPOST("shopItemIds");

$listOrder = 1;
foreach ($shopItemIds as $shopItemId) {
  // An ajax request parameter value is UTF-8 encoded
  $shopItemId = utf8_decode($shopItemId);

  if ($shopItem = $shopItemUtils->selectById($shopItemId)) {
    $shopItem->setListOrder($listOrder);
    $shopItemUtils->update($shopItem);
    $listOrder++;
  }
}

?>
