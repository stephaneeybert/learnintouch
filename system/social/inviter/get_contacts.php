<?PHP

require_once("website.php");
require_once($gApiPath . 'OpenInviter/openinviter.php');

$mlText = $languageUtils->getWebsiteText(__FILE__);

LibHtml::preventCaching();

$email = LibEnv::getEnvHttpGET("email");
$password = LibEnv::getEnvHttpGET("password");
$selectedProvider = LibEnv::getEnvHttpGET("selectedProvider");

// An ajax request parameter value is UTF-8 encoded
$email = utf8_decode($email);
$password = utf8_decode($password);
$selectedProvider = utf8_decode($selectedProvider);

$openInviter = new OpenInviter();
$openInviterServices = $openInviter->getPlugins();
$openInviter->startPlugin($selectedProvider);
$error = '';
$strContacts = '';
if (!$openInviter->login($email, $password)) {
  $error = $mlText[0];
  $error = LibString::decodeHtmlspecialchars($error);
} else if (false === $contacts = $openInviter->getMyContacts()) {
  $error = $mlText[1];
  $error = LibString::decodeHtmlspecialchars($error);
} else {
  foreach ($contacts as $email => $name) {
    $name = utf8_decode($name);
    $strContacts .= "{email : \"$email\", name : \"$name\"},";
  }
}

$strContacts = substr($strContacts, 0, strlen($strContacts) - 1);

$responseText = <<<HEREDOC
{
"error" : "$error",
"contacts" : [ $strContacts ]
}
HEREDOC;

print($responseText);

?>
