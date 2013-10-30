<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formId = LibEnv::getEnvHttpGET("formId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

// An ajax request parameter value is UTF-8 encoded
$formId = utf8_decode($formId);
$languageCode = utf8_decode($languageCode);

if ($form = $formUtils->selectById($formId)) {
  $acknowledge = $languageUtils->getTextForLanguage($form->getAcknowledge(), $languageCode);
  $acknowledge = LibString::jsonEscapeLinebreak($acknowledge);
  $acknowledge = LibString::escapeDoubleQuotes($acknowledge);
} else {
  $acknowledge = '';
}

$responseText = <<<HEREDOC
{
"acknowledge" : "$acknowledge"
}
HEREDOC;

print($responseText);

?>
