<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningScoringRangeDB = new ElearningScoringRangeDB();

$elearningScoringRangeId = LibEnv::getEnvHttpGET("elearningScoringRangeId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

if ($elearningScoringRange = $elearningScoringRangeDB->selectById($elearningScoringRangeId)) {
  $proposal = $languageUtils->getTextForLanguage($elearningScoringRange->getProposal(), $languageCode);
  $proposal = LibString::jsonEscapeLinebreak($proposal);
  $proposal = LibString::escapeDoubleQuotes($proposal);
} else {
  $proposal = '';
}

$responseText = <<<HEREDOC
{
"proposal" : "$proposal"
}
HEREDOC;

print($responseText);

?>
