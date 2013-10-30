<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);

$photoAlbumId = LibEnv::getEnvHttpGET("photoAlbumId");

$photoAlbumUtils->swapWithNext($photoAlbumId);

$str = LibHtml::urlRedirect("$gPhotoUrl/album/admin.php");
printContent($str);
return;

?>
