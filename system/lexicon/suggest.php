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
  $lexiconEntries = $lexiconEntryUtils->selectLikePattern($typedInString);
} else {
  $lexiconEntries = array();
}

$responseText = '[';

if (is_array($lexiconEntries)) {
  foreach ($lexiconEntries as $lexiconEntry) {
    $lexiconEntryId = $lexiconEntry->getId();
    $name = $lexiconEntry->getName();
    $explanation = $lexiconEntry->getExplanation();
    // The string must be html decoded to be correctly displayed
    $name = LibString::decodeHtmlspecialchars($name);
    $name = LibString::escapeDoubleQuotes($name);
    $explanation = LibString::decodeHtmlspecialchars($explanation);
    $explanation = LibString::escapeDoubleQuotes($explanation);
    $image = $lexiconEntry->getImage();
    if ($image) {
      $explanation .= '<div>' . $lexiconEntryUtils->renderImage($image) . '</div>';
    }
    $responseText .= " {\"id\": \"$lexiconEntryId\", \"label\": \"$name : $explanation\", \"value\": \"$name : $explanation\"},";
  }
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
