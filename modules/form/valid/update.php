<?PHP

require_once("website.php");

LibHtml::preventCaching();

$formValidId = LibEnv::getEnvHttpPOST("formValidId");
$languageCode = LibEnv::getEnvHttpPOST("languageCode");
$message = LibEnv::getEnvHttpPOST("message");

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
