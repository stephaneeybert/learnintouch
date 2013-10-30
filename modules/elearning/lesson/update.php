<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$instructions = LibEnv::getEnvHttpPOST("instructions");

// An ajax request parameter value is UTF-8 encoded
$elearningLessonId = utf8_decode($elearningLessonId);
$instructions = utf8_decode($instructions);

if ($elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
  $elearningLesson->setInstructions($languageUtils->setTextForLanguage($elearningLesson->getInstructions(), $languageCode, $instructions));
  $elearningLessonUtils->update($elearningLesson);
}

$notused = '';

$responseText = <<<HEREDOC
{
"notused" : "$notused"
}
HEREDOC;

print($responseText);

?>
