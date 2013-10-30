<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formItemValueId = LibEnv::getEnvHttpGET("formItemValueId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

// An ajax request parameter value is UTF-8 encoded
$formItemValueId = utf8_decode($formItemValueId);
$languageCode = utf8_decode($languageCode);

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
