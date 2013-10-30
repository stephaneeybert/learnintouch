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
  $elearningLessons = $elearningLessonUtils->selectLikePattern($typedInString);
} else {
  $elearningLessons = array();
}

$responseText = '[';

foreach ($elearningLessons as $elearningLesson) {
  $elearningLessonId = $elearningLesson->getId();
  $name = $elearningLesson->getName();
  if ($elearningLessonParagraphs = $elearningLessonParagraphUtils->selectByLessonId($elearningLessonId)) {
    foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
      $elearningLessonParagraphId = $elearningLessonParagraph->getId();
      $headline = $elearningLessonParagraph->getHeadline();
      $name = LibString::escapeDoubleQuotes($name);
      $headline = LibString::escapeDoubleQuotes($headline);
      $responseText .= " {\"id\": \"$elearningLessonParagraphId\", \"label\": \"$name - $headline\", \"value\": \"$name - $headline\"},";
    }
  }
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
