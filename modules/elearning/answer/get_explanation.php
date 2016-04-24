<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningAnswerId = LibEnv::getEnvHttpGET("elearningAnswerId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

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
