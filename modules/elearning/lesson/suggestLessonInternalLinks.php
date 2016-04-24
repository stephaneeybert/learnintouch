<?PHP

require_once("website.php");

LibHtml::preventCaching();

$typedInString = LibEnv::getEnvHttpGET("term");

if (!$typedInString) {
  return;
}

// The name is stored in the database in a html encoded format
$typedInString = LibString::cleanString($typedInString);

if ($typedInString) {
  $internalLinks = $elearningLessonUtils->getInternalLinks($typedInString);
} else {
  $internalLinks = array();
}

$responseText = '[';

if ($internalLinks) {
  foreach ($internalLinks as $internalLink => $name) {
    $name = LibString::decodeHtmlspecialchars($name);
    $name = LibString::escapeDoubleQuotes($name);
    $responseText .= " {\"id\": \"$internalLink\", \"label\": \"$name\", \"value\": \"$name\"},";
  }
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
