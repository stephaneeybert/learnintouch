<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningScoringRangeDB = new ElearningScoringRangeDB();

$elearningScoringRangeId = LibEnv::getEnvHttpPOST("elearningScoringRangeId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$advice = LibEnv::getEnvHttpPOST("advice");

if ($elearningScoringRange = $elearningScoringRangeDB->selectById($elearningScoringRangeId)) {
  $elearningScoringRange->setAdvice($languageUtils->setTextForLanguage($elearningScoringRange->getAdvice(), $languageCode, $advice));
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
