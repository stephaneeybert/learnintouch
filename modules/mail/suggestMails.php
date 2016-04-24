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

if ($mails = $mailUtils->selectLikePattern($typedInString)) {
  foreach ($mails as $mail) {
    $mailId = $mail->getId();
    $subject = $mail->getSubject();
    $subject = LibString::decodeHtmlspecialchars($subject);
    $subject = LibString::escapeDoubleQuotes($subject);
    $responseText .= " {\"id\": \"$mailId\", \"label\": \"$subject\", \"value\": \"$subject\"},";
  }
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
