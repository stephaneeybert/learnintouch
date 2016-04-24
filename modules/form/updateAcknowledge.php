<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formId = LibEnv::getEnvHttpPOST("formId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$acknowledge = LibEnv::getEnvHttpPOST("acknowledge");

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
