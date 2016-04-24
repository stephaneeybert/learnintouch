<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningQuestionId = LibEnv::getEnvHttpPOST("elearningQuestionId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$explanation = LibEnv::getEnvHttpPOST("explanation");

if ($elearningQuestion = $elearningQuestionUtils->selectById($elearningQuestionId)) {
  $elearningQuestion->setExplanation($languageUtils->setTextForLanguage($elearningQuestion->getExplanation(), $languageCode, $explanation));
  $elearningQuestionUtils->update($elearningQuestion);
}

$notused = '';

$responseText = <<<HEREDOC
{
"notused" : "$notused"
}
HEREDOC;

print($responseText);

?>
