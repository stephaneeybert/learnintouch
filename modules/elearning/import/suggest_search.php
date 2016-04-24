<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

LibHtml::preventCaching();

$contentImportId = LibEnv::getEnvHttpGET("contentImportId");
$searchPattern = LibEnv::getEnvHttpGET("term");

$searchPattern = LibString::cleanString($searchPattern);

if ($contentImportId) {
  $xmlResponseSearchedContent = $elearningImportUtils->exposeSearchedContentAsXML($contentImportId, $searchPattern, false);
  $courses = $elearningImportUtils->getCourseListREST($xmlResponseSearchedContent);
  $lessons = $elearningImportUtils->getLessonListREST($xmlResponseSearchedContent);
  $exercises = $elearningImportUtils->getExerciseListREST($xmlResponseSearchedContent);

  $responseText = '[';

  foreach ($courses as $course) {
    list($elearningCourseId, $name, $description) = $course;
    $name = LibString::decodeHtmlspecialchars($name);
    $description = LibString::escapeDoubleQuotes($description);
    $responseText .= " {\"id\" : \"\", \"label\" : \"$mlText[0] $name\", \"value\" : \"$name\"},";
  }

  foreach ($lessons as $lesson) {
    list($elearningLessonId, $name, $description) = $lesson;
    $name = LibString::decodeHtmlspecialchars($name);
    $description = LibString::escapeDoubleQuotes($description);
    $responseText .= " {\"id\" : \"\", \"label\" : \"$mlText[1] $name\", \"value\" : \"$name\"},";
  }

  foreach ($exercises as $exercise) {
    list($elearningExerciseId, $name, $description) = $exercise;
    $name = LibString::decodeHtmlspecialchars($name);
    $description = LibString::escapeDoubleQuotes($description);
    $responseText .= " {\"id\" : \"\", \"label\" : \"$mlText[2] $name\", \"value\" : \"$name\"},";
  }
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
