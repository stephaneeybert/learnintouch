<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningScoringRangeDB = new ElearningScoringRangeDB();

$elearningScoringRangeId = LibEnv::getEnvHttpPOST("elearningScoringRangeId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$score = LibEnv::getEnvHttpPOST("score");

// An ajax request parameter value is UTF-8 encoded
$elearningScoringRangeId = utf8_decode($elearningScoringRangeId);
$score = utf8_decode($score);

if ($elearningScoringRange = $elearningScoringRangeDB->selectById($elearningScoringRangeId)) {
  $elearningScoringRange->setScore($languageUtils->setTextForLanguage($elearningScoringRange->getScore(), $languageCode, $score));
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
