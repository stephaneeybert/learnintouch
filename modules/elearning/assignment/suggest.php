<?PHP

require_once("website.php");

// Prevent the caching of the page content
LibHtml::preventCaching();

$typedInString = LibEnv::getEnvHttpGET("term");

if (!$typedInString) {
  return;
}

// The name is stored in the database in a html encoded format
$typedInString = LibString::cleanString($typedInString);

$responseText = '[';

$userUtils = new UserUtils();
$elearningSubscriptionUtils = new ElearningSubscriptionUtils();
if ($elearningSubscriptions = $elearningSubscriptionUtils->selectLikePattern($typedInString)) {
  foreach ($elearningSubscriptions as $elearningSubscription) {
    $elearningSubscriptionId = $elearningSubscription->getId();
    $userId = $elearningSubscription->getUserId();
    if ($user = $userUtils->selectById($userId)) {
      $userName = $user->getFirstname() . ' ' . $user->getLastname();
      $userName = LibString::decodeHtmlspecialchars($userName);
      $userName = LibString::escapeDoubleQuotes($userName);
      $responseText .= " {\"id\": \"$elearningSubscriptionId\", \"label\": \"$userName\", \"value\": \"$userName\"},";
    }
  }
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
