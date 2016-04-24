<?PHP

require_once("website.php");

LibHtml::preventCaching();

$typedInString = LibEnv::getEnvHttpGET("term");

if (!$typedInString) {
  return;
}

// The name is stored in the database in a html encoded format
$typedInString = LibString::cleanString($typedInString);

if ($typedInString) {
  $elearningExercises = $elearningExerciseUtils->selectLikePatternInExerciseAndCourse($typedInString);
} else {
  $elearningExercises = array();
}

$responseText = '[';

foreach ($elearningExercises as $elearningExercise) {
  $elearningExerciseId = $elearningExercise->getId();
  $name = $elearningExercise->getName();
  $name = LibString::decodeHtmlspecialchars($name);
  $name = LibString::escapeDoubleQuotes($name);
  $responseText .= " {\"id\": \"$elearningExerciseId\", \"label\": \"$name\", \"value\": \"$name\"},";
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
