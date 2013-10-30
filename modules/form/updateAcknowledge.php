<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formId = LibEnv::getEnvHttpPOST("formId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$acknowledge = LibEnv::getEnvHttpPOST("acknowledge");

// An ajax request parameter value is UTF-8 encoded
$formId = utf8_decode($formId);
$acknowledge = utf8_decode($acknowledge);

if ($form = $formUtils->selectById($formId)) {
  $form->setAcknowledge($languageUtils->setTextForLanguage($form->getAcknowledge(), $languageCode, $acknowledge));
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
