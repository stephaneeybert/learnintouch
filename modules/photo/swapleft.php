<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);

$photoId = LibEnv::getEnvHttpGET("photoId");

$photoUtils->swapWithPrevious($photoId);

$str = LibHtml::urlRedirect("$gPhotoUrl/admin.php");
printContent($str);
return;

?>
