<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formId = LibEnv::getEnvHttpGET("formId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

// An ajax request parameter value is UTF-8 encoded
$formId = utf8_decode($formId);
$languageCode = utf8_decode($languageCode);

if ($form = $formUtils->selectById($formId)) {
  $instructions = $languageUtils->getTextForLanguage($form->getInstructions(), $languageCode);
  $instructions = LibString::jsonEscapeLinebreak($instructions);
  $instructions = LibString::escapeDoubleQuotes($instructions);
} else {
  $instructions = '';
}

$responseText = <<<HEREDOC
{
"instructions" : "$instructions"
}
HEREDOC;

print($responseText);

?>
