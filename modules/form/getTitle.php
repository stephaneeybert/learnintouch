<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formId = LibEnv::getEnvHttpGET("formId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

if ($form = $formUtils->selectById($formId)) {
  $title = $languageUtils->getTextForLanguage($form->getTitle(), $languageCode);
  $title = LibString::jsonEscapeLinebreak($title);
  $title = LibString::escapeDoubleQuotes($title);
} else {
  $title = '';
}

$responseText = <<<HEREDOC
{
"title" : "$title"
}
HEREDOC;

print($responseText);

?>
