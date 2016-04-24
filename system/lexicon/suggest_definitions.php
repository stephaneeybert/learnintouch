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
  $definitions = $lexiconEntryUtils->getEntryDefinitions($typedInString);
} else {
  $definitions = array();
}

$responseText = '[';

if (is_array($definitions)) {
  foreach ($definitions as $definition) {
    $definition = LibString::decodeHtmlspecialchars($definition);
    $definition = LibString::stripNonTextChar($definition);
    $definition = LibString::stripTags($definition);
    $definition = LibString::escapeDoubleQuotes($definition);
    $typedInString = LibString::escapeDoubleQuotes($typedInString);
    $responseText .= " {\"id\": \"$definition\", \"label\": \"$typedInString : $definition\", \"value\": \"$typedInString\"},";
  }
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
