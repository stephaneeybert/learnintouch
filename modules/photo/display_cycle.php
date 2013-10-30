<?PHP

require_once("website.php");

$photoAlbumId = LibEnv::getEnvHttpGET("photoAlbumId");

// Prevent sql injection attacks as the id is always numeric
$photoAlbumId = (int) $photoAlbumId;

$gTemplate->setPageContent($photoUtils->renderImageCycleInPage($photoAlbumId));

require_once($gTemplatePath . "render.php");

?>
