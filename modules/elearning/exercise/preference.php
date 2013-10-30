<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

require_once($gPreferencePath . "setCurrentAdminLanguage.php");

$elearningExerciseUtils->loadPreferences();

$preferenceUtils->init($elearningExerciseUtils->preferences, "$gElearningUrl/subscription/admin.php");

require_once($gPreferencePath . "admin.php");

?>
