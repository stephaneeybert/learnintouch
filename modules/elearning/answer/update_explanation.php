<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningAnswerId = LibEnv::getEnvHttpPOST("elearningAnswerId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$explanation = LibEnv::getEnvHttpPOST("explanation");

// An ajax request parameter value is UTF-8 encoded
$elearningAnswerId = utf8_decode($elearningAnswerId);
$explanation = utf8_decode($explanation);

if ($elearningAnswer = $elearningAnswerUtils->selectById($elearningAnswerId)) {
  $elearningAnswer->setExplanation($languageUtils->setTextForLanguage($elearningAnswer->getExplanation(), $languageCode, $explanation));
  $elearningAnswerUtils->update($elearningAnswer);
}

$notused = '';

$responseText = <<<HEREDOC
{
"notused" : "$notused"
}
HEREDOC;

print($responseText);

?>
