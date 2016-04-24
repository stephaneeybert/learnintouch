<?PHP

require_once("website.php");

LibHtml::preventCaching();

$subscribe = LibEnv::getEnvHttpGET("subscribe");
$typedInString = LibEnv::getEnvHttpGET("term");

if (!$typedInString) {
  return;
}

// The name is stored in the database in a html encoded format
$typedInString = LibString::cleanString($typedInString);

if ($subscribe) {
  $users = $userUtils->searchMailSubscribersLikePattern($typedInString);
} else {
  $users = $userUtils->selectLikePattern($typedInString);
}

$responseText = '[';

foreach ($users as $user) {
  $userId = $user->getId();
  $email = $user->getEmail();
  $userName = $user->getFirstname() . ' ' . $user->getLastname();
  $userName = LibString::decodeHtmlspecialchars($userName);
  $userName = LibString::escapeDoubleQuotes($userName);
  $responseText .= " {\"id\": \"$userId\", \"label\": \"$userName | $email\", \"value\": \"$userName\"},";
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
