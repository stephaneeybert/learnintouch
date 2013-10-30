<?PHP

require_once("website.php");

LibHtml::preventCaching();

$typedInString = LibEnv::getEnvHttpGET("term");

if (!$typedInString) {
  return;
}

// Ajax treats its data as UTF-8
$typedInString = utf8_decode($typedInString);

// The name is stored in the database in a html encoded format
$typedInString = LibString::cleanString($typedInString);

$responseText = '[';

$systemDate = $clockUtils->getSystemDate();
if ($elearningSessions = $elearningSessionUtils->selectLikePatternAndNotClosed($typedInString, $systemDate)) {
  foreach ($elearningSessions as $elearningSession) {
    $elearningSessionId = $elearningSession->getId();
    $name = $elearningSession->getName();
    $name = LibString::decodeHtmlspecialchars($name);
    $name = LibString::escapeDoubleQuotes($name);
    $responseText .= " {\"id\": \"$elearningSessionId\", \"label\": \"$name\", \"value\": \"$name\"},";
    }
  }

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
