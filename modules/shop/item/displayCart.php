<?PHP

require_once("website.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormat.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatDao.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatDB.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatUtils.php");

require_once($gShopPath . "item/updateCart.php");

$checkout = LibEnv::getEnvHttpPOST("checkout");

if ($checkout) {
  $str = LibHtml::urlRedirect("$gShopUrl/order/checkout.php");
  printContent($str);
  exit;
}

$str = $shopItemUtils->renderCart();

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
