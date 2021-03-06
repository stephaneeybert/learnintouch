<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningAnswerId = LibEnv::getEnvHttpPOST("elearningAnswerId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$explanation = LibEnv::getEnvHttpPOST("explanation");

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
