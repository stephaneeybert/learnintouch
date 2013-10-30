<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formItemId = LibEnv::getEnvHttpPOST("formItemId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$text = LibEnv::getEnvHttpPOST("text");

// An ajax request parameter value is UTF-8 encoded
$formItemId = utf8_decode($formItemId);
$text = utf8_decode($text);

if ($formItem = $formItemUtils->selectById($formItemId)) {
  $formItem->setText($languageUtils->setTextForLanguage($formItem->getText(), $languageCode, $text));
  $formItemUtils->update($formItem);
}

$notused = '';

$responseText = <<<HEREDOC
{
"notused" : "$notused"
}
HEREDOC;

print($responseText);

?>
