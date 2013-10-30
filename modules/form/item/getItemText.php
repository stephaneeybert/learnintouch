<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formItemId = LibEnv::getEnvHttpGET("formItemId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

// An ajax request parameter value is UTF-8 encoded
$formItemId = utf8_decode($formItemId);
$languageCode = utf8_decode($languageCode);

if ($formItem = $formItemUtils->selectById($formItemId)) {
  $text = $languageUtils->getTextForLanguage($formItem->getText(), $languageCode);
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
