<?PHP

require_once("website.php");

LibHtml::preventCaching();


$elearningQuestionId = LibEnv::getEnvHttpPOST("elearningQuestionId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$explanation = LibEnv::getEnvHttpPOST("explanation");

// An ajax request parameter value is UTF-8 encoded
$elearningQuestionId = utf8_decode($elearningQuestionId);
$explanation = utf8_decode($explanation);

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
