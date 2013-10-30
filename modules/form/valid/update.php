<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formValidId = LibEnv::getEnvHttpPOST("formValidId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$message = LibEnv::getEnvHttpPOST("message");

// An ajax request parameter value is UTF-8 encoded
$formValidId = utf8_decode($formValidId);
$message = utf8_decode($message);

if ($formValid = $formValidUtils->selectById($formValidId)) {
  $formValid->setMessage($languageUtils->setTextForLanguage($formValid->getMessage(), $languageCode, $message));
  $formValidUtils->update($formValid);
}

$notused = '';

$responseText = <<<HEREDOC
{
"notused" : "$notused"
}
HEREDOC;

print($responseText);

?>
