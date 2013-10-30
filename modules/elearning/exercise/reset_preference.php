<?PHP

require_once("website.php");

require_once($gPreferencePath . "getPreferenceToReset.php");

$elearningExerciseUtils->loadPreferences();

$preferenceUtils->init($elearningExerciseUtils->preferences);

require_once($gPreferencePath . "reset_preference.php");

?>
