<?PHP

require_once("website.php");

LibHtml::preventCaching();


$containerId = LibEnv::getEnvHttpPOST("containerId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$content = LibEnv::getEnvHttpPOST("content");

if ($container = $containerUtils->selectById($containerId)) {
  $container->setContent($languageUtils->setTextForLanguage($container->getContent(), $languageCode, $content));
  $containerUtils->update($container);
}

$notused = '';

$responseText = <<<HEREDOC
{
"notused" : "$notused"
}
HEREDOC;

print($responseText);

?>
