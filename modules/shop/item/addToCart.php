<?PHP

require_once("website.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormat.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatDao.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatDB.php");
require_once($gPhotoPath . "album/format/PhotoAlbumFormatUtils.php");

require_once($gShopPath . "item/updateCart.php");

$str = LibHtml::urlRedirect("$gShopUrl/item/displayCart.php");
printContent($str);
exit;

?>
