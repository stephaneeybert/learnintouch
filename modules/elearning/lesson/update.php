<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningLessonId = LibEnv::getEnvHttpPOST("elearningLessonId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$instructions = LibEnv::getEnvHttpPOST("instructions");

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
