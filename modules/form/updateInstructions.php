<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formId = LibEnv::getEnvHttpPOST("formId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$instructions = LibEnv::getEnvHttpPOST("instructions");

// An ajax request parameter value is UTF-8 encoded
$formId = utf8_decode($formId);
$instructions = utf8_decode($instructions);

if ($form = $formUtils->selectById($formId)) {
  $form->setInstructions($languageUtils->setTextForLanguage($form->getInstructions(), $languageCode, $instructions));
  $formUtils->update($form);
}

$notused = '';

$responseText = <<<HEREDOC
{
"notused" : "$notused"
}
HEREDOC;

print($responseText);

?>
