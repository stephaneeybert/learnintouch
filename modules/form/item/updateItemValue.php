<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formItemValueId = LibEnv::getEnvHttpPOST("formItemValueId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$text = LibEnv::getEnvHttpPOST("text");

// An ajax request parameter value is UTF-8 encoded
$formItemValueId = utf8_decode($formItemValueId);
$text = utf8_decode($text);

if ($formItemValue = $formItemValueUtils->selectById($formItemValueId)) {
  $formItemValue->setText($languageUtils->setTextForLanguage($formItemValue->getText(), $languageCode, $text));
  $formItemValueUtils->update($formItemValue);
}

$notused = '';

$responseText = <<<HEREDOC
{
"notused" : "$notused"
}
HEREDOC;

print($responseText);

?>
