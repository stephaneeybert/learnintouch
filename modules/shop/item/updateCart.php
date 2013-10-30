<?PHP

require_once("website.php");

$updateCart = LibEnv::getEnvHttpPOST("updateCart");

if ($updateCart) {

  $items = $shopItemUtils->getCart();

  if (count($items) > 0) {
    for ($i = 0; $i < count($items); $i++) {
      $wItemType = $items[$i][0];
      $wItemId = $items[$i][1];
      $quantity = LibEnv::getEnvHttpPOST("quantity_$wItemType$wItemId");
      $isGift = LibEnv::getEnvHttpPOST("isGift_$wItemType$wItemId");
      if ($quantity > 0) {
        $items[$i][2] = $quantity;
      } else {
        unset($items[$i]);
      }
      $items[$i][3] = $isGift;
      if ($wItemType == SHOP_CART_ITEM) {
        $items[$i][4] = '';
      } else if ($wItemType == SHOP_CART_PHOTO) {
        $photoFormatId = LibEnv::getEnvHttpPOST("photoFormatId_$wItemType$wItemId");
        $frame = LibEnv::getEnvHttpPOST("frame_$wItemType$wItemId");
        $aspect = LibEnv::getEnvHttpPOST("aspect_$wItemType$wItemId");
        $options = $photoFormatId . SHOP_CART_OPTION_SEPARATOR . $frame . SHOP_CART_OPTION_SEPARATOR . $aspect;
        $items[$i][4] = $options;
      }
    }

    $shopItemUtils->setCart($items);
  }

  $checkout = LibEnv::getEnvHttpPOST("checkout");

  if ($checkout) {
    $str = LibHtml::urlRedirect("$gShopUrl/order/checkout.php");
    printContent($str);
    exit;
  }

} else {

  // Add or delete an item from the cart
  $itemType = LibEnv::getEnvHttpGET("itemType");
  $itemId = LibEnv::getEnvHttpGET("itemId");
  $quantity = LibEnv::getEnvHttpGET("quantity");
  if ($quantity == 1) {
    $shopItemUtils->addToCart($itemType, $itemId);
  } else if ($quantity < 1) {
    $shopItemUtils->deleteFromCart($itemType, $itemId);
  }

}

?>
