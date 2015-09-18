<?PHP

require_once("website.php");

LibHtml::preventCaching();

$albumIds = LibEnv::getEnvHttpPOST("albumIds");

$listOrder = 1;
foreach ($albumIds as $photoAlbumId) {
  // An ajax request parameter value is UTF-8 encoded
  $photoAlbumId = utf8_decode($photoAlbumId);

  if ($photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
    $photoAlbum->setListOrder($listOrder);
    $photoAlbumUtils->update($photoAlbum);
    $listOrder++;
  }
}

?>
