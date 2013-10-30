<?PHP

require_once("website.php");

LibHtml::preventCaching();

$typedInString = LibEnv::getEnvHttpGET("term");

if (!$typedInString) {
  return;
}

// Ajax treats its data as UTF-8
$typedInString = utf8_decode($typedInString);

// The name is stored in the database in a html encoded format
$typedInString = LibString::cleanString($typedInString);

if ($typedInString) {
  $elearningLessons = $elearningLessonUtils->selectLikePatternInLessonAndCourse($typedInString);
} else {
  $elearningLessons = array();
}

$responseText = '[';

foreach ($elearningLessons as $elearningLesson) {
  $elearningLessonId = $elearningLesson->getId();
  $name = $elearningLesson->getName();
  $name = LibString::decodeHtmlspecialchars($name);
  $name = LibString::escapeDoubleQuotes($name);
  $responseText .= " {\"id\": \"$elearningLessonId\", \"label\": \"$name\", \"value\": \"$name\"},";
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
