<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_GUESTBOOK);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$guestbookUtils->loadPreferences();

$preferenceUtils->init($guestbookUtils->preferences, "$gGuestbookUrl/admin.php");

require_once($gPreferencePath . "admin.php");

?>
