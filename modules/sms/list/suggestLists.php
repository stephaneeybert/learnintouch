<?PHP

require_once("website.php");

LibHtml::preventCaching();

$typedInString = LibEnv::getEnvHttpGET("term");

if (!$typedInString) {
  return;
}

// The name is stored in the database in a html encoded format
$typedInString = LibString::cleanString($typedInString);

$responseText = '[';

if ($smsLists = $smsListUtils->selectLikePattern($typedInString)) {
  foreach ($smsLists as $smsList) {
    $smsListId = $smsList->getId();
    $name = $smsList->getName();
    $name = LibString::decodeHtmlspecialchars($name);
    $name = LibString::escapeDoubleQuotes($name)
    $responseText .= " {\"id\": \"$smsListId\", \"label\": \"$name\", \"value\": \"$name\"},";
  }
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
