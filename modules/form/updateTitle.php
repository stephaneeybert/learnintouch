<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formId = LibEnv::getEnvHttpPOST("formId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$title = LibEnv::getEnvHttpPOST("title");

if ($form = $formUtils->selectById($formId)) {
  $form->setTitle($languageUtils->setTextForLanguage($form->getTitle(), $languageCode, $title));
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
