<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningScoringRangeDB = new ElearningScoringRangeDB();

$elearningScoringRangeId = LibEnv::getEnvHttpGET("elearningScoringRangeId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

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
