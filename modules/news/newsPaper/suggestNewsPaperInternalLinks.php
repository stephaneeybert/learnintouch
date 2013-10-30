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

if ($typedInString) {
  $newsPaperNames = $newsPaperUtils->getNewsPaperInternalLinks($typedInString);
} else {
  $newsPaperNames = array();
}

$responseText = '[';

if ($newsPaperNames) {
  foreach ($newsPaperNames as $id => $name) {
    $name = LibString::decodeHtmlspecialchars($name);
    $name = LibString::escapeDoubleQuotes($name);
    $responseText .= " {\"id\": \"$id\", \"label\": \"$name\", \"value\": \"$name\"},";
  }
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
