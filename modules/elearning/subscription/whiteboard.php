<?PHP

require_once("website.php");

$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");

$str = $elearningExerciseUtils->renderWhiteboard($elearningSubscriptionId);

printContent($str);

?>
