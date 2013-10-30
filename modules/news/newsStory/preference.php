<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$newsStoryUtils->loadPreferences();

$preferenceUtils->init($newsStoryUtils->preferences, "$gNewsUrl/newsStory/admin.php");

require_once($gPreferencePath . "admin.php");

?>
