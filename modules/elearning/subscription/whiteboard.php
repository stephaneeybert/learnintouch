<?PHP

require_once("website.php");

$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");
$elearningClassId = LibEnv::getEnvHttpGET("elearningClassId");

$str = $elearningExerciseUtils->renderWhiteboard($elearningSubscriptionId, $elearningClassId);

printAdminPage($str);

?>
