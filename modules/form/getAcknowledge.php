<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formId = LibEnv::getEnvHttpGET("formId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

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
