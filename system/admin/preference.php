<?PHP

require_once("website.php");

$adminUtils->checkSuperAdminLogin();

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$adminUtils->loadPreferences();

$preferenceUtils->init($adminUtils->preferences, "$gAdminUrl/list.php");

require_once($gPreferencePath . "admin.php");

?>
