<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formId = LibEnv::getEnvHttpPOST("formId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$title = LibEnv::getEnvHttpPOST("title");

// An ajax request parameter value is UTF-8 encoded
$formId = utf8_decode($formId);
$title = utf8_decode($title);

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
