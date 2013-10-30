<?PHP

require_once("website.php");

LibHtml::preventCaching();


$containerId = LibEnv::getEnvHttpGET("containerId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

// An ajax request parameter value is UTF-8 encoded
$containerId = utf8_decode($containerId);
$languageCode = utf8_decode($languageCode);

if ($container = $containerUtils->selectById($containerId)) {
  $content = $languageUtils->getTextForLanguage($container->getContent(), $languageCode);
  $content = LibString::jsonEscapeLinebreak($content);
  $content = LibString::escapeDoubleQuotes($content);
} else {
  $content = '';
}

$responseText = <<<HEREDOC
{
"content" : "$content"
}
HEREDOC;

print($responseText);

?>
