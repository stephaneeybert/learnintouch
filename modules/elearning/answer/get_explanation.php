<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningAnswerId = LibEnv::getEnvHttpGET("elearningAnswerId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

// An ajax request parameter value is UTF-8 encoded
$elearningAnswerId = utf8_decode($elearningAnswerId);
$languageCode = utf8_decode($languageCode);

if ($elearningAnswer = $elearningAnswerUtils->selectById($elearningAnswerId)) {
  $explanation = $languageUtils->getTextForLanguage($elearningAnswer->getExplanation(), $languageCode);
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
