<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningScoringRangeDB = new ElearningScoringRangeDB();

$elearningScoringRangeId = LibEnv::getEnvHttpPOST("elearningScoringRangeId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$proposal = LibEnv::getEnvHttpPOST("proposal");

// An ajax request parameter value is UTF-8 encoded
$elearningScoringRangeId = utf8_decode($elearningScoringRangeId);
$proposal = utf8_decode($proposal);

if ($elearningScoringRange = $elearningScoringRangeDB->selectById($elearningScoringRangeId)) {
  $elearningScoringRange->setProposal($languageUtils->setTextForLanguage($elearningScoringRange->getProposal(), $languageCode, $proposal));
  $elearningScoringRangeDB->update($elearningScoringRange);
}

$notused = '';

$responseText = <<<HEREDOC
{
"notused" : "$notused"
}
HEREDOC;

print($responseText);

?>
