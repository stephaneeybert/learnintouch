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
  $documents = $documentUtils->selectLikePattern($typedInString);
} else {
  $documents = array();
}

$responseText = '[';

foreach ($documents as $document) {
  $documentId = $document->getId();
  $file = $document->getFile();
  $description = $document->getDescription();
  // The variable must be html decoded to be correctly displayed
  $file = LibString::decodeHtmlspecialchars($file);
  $description = LibString::decodeHtmlspecialchars($description);
  $label = $file;
  if ($description) {
    $label .= ' : ' . $description;
  }
  $label = LibString::escapeDoubleQuotes($label);
  $responseText .= " {\"id\": \"$documentId\", \"label\": \"$label\", \"value\": \"$label\"},";
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
