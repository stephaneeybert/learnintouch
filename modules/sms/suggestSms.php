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

if ($smss = $smsUtils->selectLikePattern($typedInString)) {
  foreach ($smss as $sms) {
    $smsId = $sms->getId();
    $description = $sms->getDescription();
    $description = LibString::decodeHtmlspecialchars($description);
    $description = LibString::escapeDoubleQuotes($description);
    $responseText .= " {\"id\": \"$smsId\", \"label\": \"$description\", \"value\": \"$description\"},";
  }
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
