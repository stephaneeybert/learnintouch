<?PHP

require_once("website.php");

LibHtml::preventCaching();

$text = LibEnv::getEnvHttpGET("text");
$fromLanguageCode = LibEnv::getEnvHttpGET("fromLanguageCode");
$toLanguageCode = LibEnv::getEnvHttpGET("toLanguageCode");
$inputFieldId = LibEnv::getEnvHttpGET("inputFieldId");

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
