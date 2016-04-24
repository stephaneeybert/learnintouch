<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formItemValueId = LibEnv::getEnvHttpGET("formItemValueId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

if ($formItemValue = $formItemValueUtils->selectById($formItemValueId)) {
  $text = $languageUtils->getTextForLanguage($formItemValue->getText(), $languageCode);
  $text = LibString::jsonEscapeLinebreak($text);
  $text = LibString::escapeDoubleQuotes($text);
} else {
  $text = '';
}

$responseText = <<<HEREDOC
{
"text" : "$text"
}
HEREDOC;

print($responseText);

?>
