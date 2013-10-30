<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningLessonModelId = LibEnv::getEnvHttpPOST("elearningLessonModelId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$instructions = LibEnv::getEnvHttpPOST("instructions");

// An ajax request parameter value is UTF-8 encoded
$elearningLessonModelId = utf8_decode($elearningLessonModelId);
$instructions = utf8_decode($instructions);

if ($elearningLessonModel = $elearningLessonModelUtils->selectById($elearningLessonModelId)) {
  $elearningLessonModel->setInstructions($languageUtils->setTextForLanguage($elearningLessonModel->getInstructions(), $languageCode, $instructions));
  $elearningLessonModelUtils->update($elearningLessonModel);
}

$notused = '';

$responseText = <<<HEREDOC
{
"notused" : "$notused"
}
HEREDOC;

print($responseText);

?>
