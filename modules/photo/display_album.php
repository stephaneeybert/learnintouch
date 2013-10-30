<?PHP

require_once("website.php");

// A photo album id can be passed in a url
$photoAlbumId = LibEnv::getEnvHttpGET("photoAlbumId");
if (!$photoAlbumId) {
  $photoAlbumId = LibEnv::getEnvHttpPOST("photoAlbumId");
}

// Prevent sql injection attacks as the id is always numeric
$photoAlbumId = (int) $photoAlbumId;

$preferenceUtils->init($photoUtils->preferences);
$displayAll = $preferenceUtils->getValue("PHOTO_DISPLAY_ALL");

if (!$displayAll) {
  if (!$photoAlbumId) {
    $photoAlbumId = LibSession::getSessionValue(PHOTO_SESSION_ALBUM);
  } else {
    LibSession::putSessionValue(PHOTO_SESSION_ALBUM, $photoAlbumId);
  }
}

$str = $photoAlbumUtils->render($photoAlbumId);

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
