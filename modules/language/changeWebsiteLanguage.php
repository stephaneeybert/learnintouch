<?php

require_once("website.php");

LibHtml::preventCaching();

$languageCode = LibEnv::getEnvHttpGET("languageCode");
$previousLanguageCode = LibEnv::getEnvHttpGET("previousLanguageCode");

$languageUtils->setCurrentLanguageCode($languageCode, false);

$responseText = <<<HEREDOC
{
"languageCode" : "$languageCode",
"previousLanguageCode" : "$previousLanguageCode"
}
HEREDOC;

print($responseText);

?>
