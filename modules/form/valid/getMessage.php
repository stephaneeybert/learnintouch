<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formValidId = LibEnv::getEnvHttpGET("formValidId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

if ($formValid = $formValidUtils->selectById($formValidId)) {
  $message = $languageUtils->getTextForLanguage($formValid->getMessage(), $languageCode);
  $message = LibString::jsonEscapeLinebreak($message);
  $message = LibString::escapeDoubleQuotes($message);
} else {
  $message = '';
}

$responseText = <<<HEREDOC
{
"message" : "$message"
}
HEREDOC;

print($responseText);

?>
