<?PHP

require_once("website.php");

LibHtml::preventCaching();

$text = LibEnv::getEnvHttpGET("text");
$fromLanguageCode = LibEnv::getEnvHttpGET("fromLanguageCode");
$toLanguageCode = LibEnv::getEnvHttpGET("toLanguageCode");
$inputFieldId = LibEnv::getEnvHttpGET("inputFieldId");

// An ajax request parameter value is UTF-8 encoded
$text = utf8_decode($text);
$fromLanguageCode = utf8_decode($fromLanguageCode);
$toLanguageCode = utf8_decode($toLanguageCode);

$translation = $commonUtils->getGoogleTextTranslation($text, $fromLanguageCode, $toLanguageCode);

$translation = LibString::jsonEscapeLinebreak($translation);

$responseText = <<<HEREDOC
{
"translation" : "$translation",
"inputFieldId" : "$inputFieldId"
}
HEREDOC;

print($responseText);

?>
