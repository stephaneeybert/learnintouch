<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

// An ajax request parameter value is UTF-8 encoded
$elearningQuestionId = utf8_decode($elearningQuestionId);
$languageCode = utf8_decode($languageCode);

if ($elearningQuestion = $elearningQuestionUtils->selectById($elearningQuestionId)) {
  $explanation = $languageUtils->getTextForLanguage($elearningQuestion->getExplanation(), $languageCode);
  $explanation = LibString::jsonEscapeLinebreak($explanation);
  $explanation = LibString::escapeDoubleQuotes($explanation);
} else {
  $explanation = '';
}

$responseText = <<<HEREDOC
{
"explanation" : "$explanation"
}
HEREDOC;

print($responseText);

?>
