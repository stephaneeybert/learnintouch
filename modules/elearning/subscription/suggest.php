<?PHP

require_once("website.php");

LibHtml::preventCaching();

$displayClass = LibEnv::getEnvHttpGET("displayClass");
$typedInString = LibEnv::getEnvHttpGET("term");

if (!$typedInString) {
  return;
}

// The name is stored in the database in a html encoded format
$typedInString = LibString::cleanString($typedInString);

$responseText = '[';

if ($elearningSubscriptions = $elearningSubscriptionUtils->selectLikePattern($typedInString)) {
  foreach ($elearningSubscriptions as $elearningSubscription) {
    $elearningSubscriptionId = $elearningSubscription->getId();
    $userId = $elearningSubscription->getUserId();
    if ($user = $userUtils->selectById($userId)) {
      $userName = $user->getFirstname() . ' ' . $user->getLastname();
      // The variable must be html decoded to be correctly displayed
      $userName = LibString::decodeHtmlspecialchars($userName);
      $className = '';
      if ($displayClass) {
        $elearningClassId = $elearningSubscription->getClassId();
        if ($elearningClass = $elearningClassUtils->selectById($elearningClassId)) {
          $className = '(' . $elearningClass->getName() . ')';
          $className = LibString::decodeHtmlspecialchars($className);
        }
      }
      $userName = LibString::escapeDoubleQuotes($userName);
      $className = LibString::escapeDoubleQuotes($className);
      $responseText .= " {\"id\": \"$elearningSubscriptionId\", \"label\": \"$userName $className\", \"value\": \"$userName $className\"},";
    }
  }
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
