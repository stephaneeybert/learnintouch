<?PHP

// Reload the preferences in the given language
// so as to have the default values in that language

// Reset the given preference
$preferenceId = LibEnv::getEnvHttpGET("preferenceId");
$defaultValue = $preferenceUtils->reset($preferenceId);

$defaultValue = LibString::br2nl($defaultValue);
$defaultValue = LibString::decodeHtmlspecialchars($defaultValue);
$defaultValue = LibString::jsonEscapeLinebreak($defaultValue);
$defaultValue = LibString::escapeDoubleQuotes($defaultValue);

// Return the value of the reset preference
$responseText = <<<HEREDOC
{
"defaultValue" : "$defaultValue",
"languageCode" : "$languageCode"
}
HEREDOC;

print($responseText);

?>
