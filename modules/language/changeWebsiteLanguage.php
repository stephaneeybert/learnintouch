<?php

require_once("website.php");

LibHtml::preventCaching();

$languageCode = LibEnv::getEnvHttpGET("languageCode");
$previousLanguageCode = LibEnv::getEnvHttpGET("previousLanguageCode");

// An ajax request parameter value is UTF-8 encoded
$languageCode = utf8_decode($languageCode);
$previousLanguageCode = utf8_decode($previousLanguageCode);

$languageUtils->setCurrentLanguageCode($languageCode, false);

$responseText = <<<HEREDOC
{
"languageCode" : "$languageCode",
"previousLanguageCode" : "$previousLanguageCode"
}
HEREDOC;

print($responseText);

?>
