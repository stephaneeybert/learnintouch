<?PHP

require_once("website.php");

LibHtml::preventCaching();

$userId = LibEnv::getEnvHttpGET("userId");

if (!$userId) {
  return;
}

$user = $userUtils->selectById($userId);

$responseText = '';

if ($user) {
  $userId = $user->getId();
  $email = $user->getEmail();
  $userName = $user->getFirstname() . ' ' . $user->getLastname();
  $userName = LibString::decodeHtmlspecialchars($userName);
  $userName = LibString::escapeDoubleQuotes($userName);
  $responseText = "{\"id\": \"$userId\", \"email\": \"$email\", \"name\": \"$userName\"}";
}

print($responseText);

?>
