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
  $advice = $languageUtils->getTextForLanguage($elearningScoringRange->getAdvice(), $languageCode);
  $advice = LibString::jsonEscapeLinebreak($advice);
  $advice = LibString::escapeDoubleQuotes($advice);
} else {
  $advice = '';
}

$responseText = <<<HEREDOC
{
"advice" : "$advice"
}
HEREDOC;

print($responseText);

?>
