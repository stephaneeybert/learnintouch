<?PHP

require_once("website.php");

LibHtml::preventCaching();

$albumIds = LibEnv::getEnvHttpPOST("albumIds");

$listOrder = 1;
foreach ($albumIds as $photoAlbumId) {
  if ($photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
    $photoAlbum->setListOrder($listOrder);
    $photoAlbumUtils->update($photoAlbum);
    $listOrder++;
  }
}

?>
