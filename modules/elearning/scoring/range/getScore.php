<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningScoringRangeDB = new ElearningScoringRangeDB();

$elearningScoringRangeId = LibEnv::getEnvHttpGET("elearningScoringRangeId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

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
