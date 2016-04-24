<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formItemValueId = LibEnv::getEnvHttpPOST("formItemValueId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$text = LibEnv::getEnvHttpPOST("text");

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
