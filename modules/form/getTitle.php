<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formId = LibEnv::getEnvHttpGET("formId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");

// An ajax request parameter value is UTF-8 encoded
$formId = utf8_decode($formId);
$languageCode = utf8_decode($languageCode);

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
