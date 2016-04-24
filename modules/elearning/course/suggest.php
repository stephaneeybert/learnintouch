<?PHP

require_once("website.php");

LibHtml::preventCaching();

$typedInString = LibEnv::getEnvHttpGET("term");
$elearningSessionId = LibEnv::getEnvHttpGET("elearningSessionId");

if (!$typedInString) {
  return;
}

// The name is stored in the database in a html encoded format
$typedInString = LibString::cleanString($typedInString);

$responseText = '[';

if ($elearningSessionId) {
  $elearningCourses = $elearningCourseUtils->selectLikePatternAndSessionId($typedInString, $elearningSessionId);
} else {
  $elearningCourses = $elearningCourseUtils->selectLikePattern($typedInString);
}
if ($elearningCourses) {
  foreach ($elearningCourses as $elearningCourse) {
    $elearningCourseId = $elearningCourse->getId();
    $name = $elearningCourse->getName();
    $name = LibString::decodeHtmlspecialchars($name);
    $name = LibString::escapeDoubleQuotes($name);
    $responseText .= " {\"id\": \"$elearningCourseId\", \"label\": \"$name\", \"value\": \"$name\"},";
    }
  }

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
