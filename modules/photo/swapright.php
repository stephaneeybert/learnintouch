<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);

$photoId = LibEnv::getEnvHttpGET("photoId");

$photoUtils->swapWithNext($photoId);

$str = LibHtml::urlRedirect("$gPhotoUrl/admin.php");
printContent($str);
return;

?>
