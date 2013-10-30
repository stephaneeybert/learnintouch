<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningScoringRangeDB = new ElearningScoringRangeDB();

$elearningScoringRangeId = LibEnv::getEnvHttpGET("elearningScoringRangeId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

// An ajax request parameter value is UTF-8 encoded
$elearningScoringRangeId = utf8_decode($elearningScoringRangeId);
$languageCode = utf8_decode($languageCode);

if ($elearningScoringRange = $elearningScoringRangeDB->selectById($elearningScoringRangeId)) {
  $score = $languageUtils->getTextForLanguage($elearningScoringRange->getScore(), $languageCode);
  $score = LibString::jsonEscapeLinebreak($score);
  $score = LibString::escapeDoubleQuotes($score);
} else {
  $score = '';
}

$responseText = <<<HEREDOC
{
"score" : "$score"
}
HEREDOC;

print($responseText);

?>
