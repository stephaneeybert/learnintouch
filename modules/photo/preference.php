<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$photoUtils->loadPreferences();

$preferenceUtils->init($photoUtils->preferences, "$gPhotoUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
